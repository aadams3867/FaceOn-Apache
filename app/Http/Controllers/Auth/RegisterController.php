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
use App\Kairos;

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

    public $url;
    public $kairos;

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
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        GLOBAL $url, $kairos;

        // Upload the image file to Amazon S3
        RegisterController::uploadFileToS3($data['gallery_name'], $data['image']);

        // Set up Kairos object with credentials
        $kairos = new Kairos(config('kairos_app.id'), config('kairos_app.key'));

        // Setup up array of data to submit to Kairos
        $argumentArray = array(
            'image' => $url,
            'subject_id' => $data['name'],
            'gallery_name' => $data['gallery_name']
        );

        // Enroll the image with Kairos for later facial recognition
        $response = $kairos->enroll($argumentArray);

        // for debugging only
/*        var_dump($response);
        ?><br><br><?php
        die;*/

        // Create the new user in the db
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'image' => $url,
            'gallery_name' => $data['gallery_name'],
        ]);
    }

    /**
     * Upload image file to Amazon S3.
     *
     * @param string $gallery (User gallery name)
     * @param object $img (initial image data)
     */
    public static function uploadFileToS3($gallery, $img)
    {
        GLOBAL $url;

        // Create an S3 Client object
        $s3 = App::make('aws')->createClient('s3');

        // Assemble the 'gallery directory / prefixed file name' for S3
        $key = $gallery . '/' . time() . '_' . $img->getClientOriginalName();

        // Send a PutObject request to upload the file to S3
        $s3->putObject([
            'Bucket' => config('filesystems.disks.s3.bucket'),
            //'Bucket' => 'face-on-bucket',
            'Key'    => $key,
            'SourceFile'   => $img->getRealPath(),
            'ContentType'   => $img->getMimeType(),
            'ContentDisposition'   => '',          
        ]);

        // Assemble the URL for storing in the user table in the db, etc.
        $url = $s3->getObjectUrl('face-on-bucket', $key);
    }

}
