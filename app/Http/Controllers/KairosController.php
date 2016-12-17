<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Kairos;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\DB;

class KairosController extends Controller
{
	public $kairos;

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
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function login(Request $request)
    {
        GLOBAL $url, $kairos;

        // Upload the image file to Amazon S3 and set $url
        RegisterController::uploadFileToS3('verify', $request['image']);

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

		// Check for errors
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
}