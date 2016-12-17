<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\Http\Controllers\KairosController;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    public $faceMatch;

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);         // Validates the usual credentials

        $this->validateFace($request);          // Validates the face of the user

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required',    // Email Address
            'password' => 'required',
            'image' => 'required|image'
        ]);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function validateFace(Request $request)
    {
        GLOBAL $faceMatch;

        return $faceMatch = KairosController::login($request);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        GLOBAL $faceMatch;

        if ($faceMatch === true) {
            return $this->guard()->attempt(
                $this->credentials($request), $request->has('remember')
            );
        }
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        GLOBAL $faceMatch;
        //['email' => trans($response)]
        if (Session::has('status_fail')) {
            // User has mis-typed email, or not registered yet
            return redirect()->back()
                ->withInput($request->only(['name', 'email', 'gallery_name']))
                ->withErrors(['email' =>
                    Lang::get('passwords.user'),
                ]);
        } else if ($faceMatch == false) {
            // Facial verification failed
            return redirect()->back()
                ->withInput($request->only(['name', 'email', 'gallery_name']))
                ->withErrors(['image' =>
                    Lang::get('auth.wrong_face'),
                ]);
        } else {
            // Password was wrong
            return redirect()->back()
                ->withInput($request->only(['name', 'email', 'gallery_name']))
                ->withErrors(['password' =>
                    Lang::get('auth.wrong_pw'),
                    //'name' => Lang::get('validation.custom.username.failed'),
                ]);
        }
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }
}