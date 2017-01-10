<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Contracts\Filesystem\Filesystem;
use Input;
use App;
use App\Http\Controllers\KairosController;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    public $url, $kairos, $validPhoto, $errorCode;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        GLOBAL $validPhoto, $errorCode;

        $this->validator($request->all())->validate();      // Validates the usual credentials

        $this->validatePhotoID($request);                   // Validates the Photo ID of the registrant

        if ($validPhoto == true) {      // Good Photo ID!

            event(new Registered($user = $this->create($request->all())));

            $this->guard()->login($user);

            return $this->registered($request, $user)
                ?: redirect($this->redirectPath());
        } else {                        // Bad Photo ID!
            if ($errorCode == 5002) {
                // No Faces found in Photo ID submitted
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image' =>
                        Lang::get('validation.custom.validatePhoto.noFaces'),
                    ]);
            } else if ($errorCode == 5010) {
                // Too Many Faces found in Photo ID submitted
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['image' =>
                        Lang::get('validation.custom.validatePhoto.tooManyFaces'),
                    ]);
            }
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
            'image' => 'required|image|max:255',
            'gallery_name' => 'required|max:255',
        ]);
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function validatePhotoID(Request $request)
    {
        GLOBAL $validPhoto;

        return $validPhoto = KairosController::register($request);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        GLOBAL $url;

        // Create the new user in the db
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'image' => $url,
            'gallery_name' => $data['gallery_name'],
        ]);
    }

}
