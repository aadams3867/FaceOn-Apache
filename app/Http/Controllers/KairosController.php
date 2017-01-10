<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Kairos;
use App\Http\Controllers\KairosController;
use App;
use Illuminate\Support\Facades\DB;

class KairosController extends Controller
{
	public $url, $kairos;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Handle a registration request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function register(Request $request)
    {
        GLOBAL $url, $kairos;

        // Upload the image file to Amazon S3
        KairosController::uploadFileToS3($request['gallery_name'], $request['image']);

        // Set up Kairos object with credentials
        $kairos = new Kairos(config('kairos_app.id'), config('kairos_app.key'));

        // Setup up array of data to submit to Kairos
        $argumentArray = array(
            'image' => $url,
            'subject_id' => $request['name'],
            'gallery_name' => $request['gallery_name']
        );

        // Enroll the image with Kairos for later facial recognition
        $response = $kairos->enroll($argumentArray);

        // Reformat the response
        $jsonDecoded = json_decode($response, true);

        // Validate Photo ID for facial recognition
        if (array_key_exists('Errors', $jsonDecoded)) {
            GLOBAL $errorCode;

            $errorCode = $jsonDecoded['Errors'][0]['ErrCode'];

            // Unacceptable Photo ID, do not register this User
            return false;
        }

        // for debugging only
        /*        var_dump($response);
                ?><br><br><?php
                die;*/

        // Everything looks good, go ahead a register this User!
        return true;
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function login(Request $request)
    {
        GLOBAL $url, $kairos;

        // Upload the image file to Amazon S3 and set $url
        KairosController::uploadFileToS3('verify', $request['image']);

        // Query db for user credentials
        $email = $request['email'];
        $requester = DB::select('select * from users where email = ?', [$email]);

        if ($requester == []) {
            // User NOT in db yet!
            request()->session()->flash( 'status_fail',  // Calls the Bootstrap pop-up alert containing fail msg
                sprintf( 'Email address not found.')
            );
            return;
        }

        $name = $requester[0]->name;
        $gallery_name = $requester[0]->gallery_name;

        // Set up Kairos object with app credentials
        $kairos = new Kairos(config('kairos_app.id'), config('kairos_app.key'));

        // Setup up array of data to submit to Kairos
        $argumentArray = array(
            'image' => $url,
            'subject_id' => $name,
            'gallery_name' => $gallery_name
        );

        // Call Kairos API to see if the image is verified
        $response = $kairos->verify($argumentArray);

        // Reformat the response 
		$jsonDecoded = json_decode($response, true);

		// Check for errors from Kairos API
		if (array_key_exists('Errors', $jsonDecoded)) {
			return false;
		    /*echo "ERROR: ";
			dd($jsonDecoded['Errors'][0]['Message']);
			*/?><!--<br><br><?php
/*			echo $url;
			*/?><br><br>--><?php
/*			die;*/
		}

		// Facial Verification logic
		if ($jsonDecoded['images'][0]['transaction']['confidence'] >= 0.60) {
			// Facial verification successful!
			return true;
		} else {
			// Imposter!  Better luck next time.
            return false;
		}
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