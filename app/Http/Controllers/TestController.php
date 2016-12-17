<?php

namespace App\Http\Controllers;         // Custom

use Illuminate\Support\Facades\Auth;    // Custom

class TestController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the test page for troubleshooting only.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('test');
    }
}