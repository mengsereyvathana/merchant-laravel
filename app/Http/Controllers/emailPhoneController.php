<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\email_phone_otp;

class emailPhoneController extends Controller
{
    public function emailPhoneOtp(){
        // $opt=emial_phone_otp::where('email',$email)->first();
        // $optcode=email_phone_otp::first();
        // return view('mail.emailOTP',compact('optcode'));
    }
}
