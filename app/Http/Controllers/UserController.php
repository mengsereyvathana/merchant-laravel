<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\loginRequest;
use validate;
use App\Models\scheme;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\admin_loginRequest;
use App\Http\Requests\changePasswordRequest;
use App\Http\Requests\VerifyEmailOtpRequest;
use App\Mail\emailOTP;
use App\Mail\forgotPassword;
use App\Models\email_phone_otp;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Kreait\Firebase\Factory;
use Exception;
//kakaka 
class UserController extends Controller
{
    public function admin_login(admin_loginRequest $req)
    {
        $name = $req->name;

        $user = User::where('name', $name)->first();
        $user_check = User::where('role', 3)->first();

        if (!$user) {
            return response()->json([
                'success'   =>  false,
                'message'   => 'Incorrect name'
            ], 201);
        }
        if (!$user_check) {
            return response()->json([
                'success'   =>  false,
                'message'   => "This accound isn't admin"
            ], 200);
        }
        if (!$user_check & !$user) {
            return response()->json([
                'success'   =>  false,
                'message'   => "User not found"
            ], 200);
        } else {
            $check = User::where('password', $req->password);
            if (!$check) {
                return response()->json([
                    'success'   =>  false,
                    'message'   => 'Incorrect password'
                ], 201);
            }
            $token = $user->createToken('my-app-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'user' => $user,
                'token' => $token
            ], 200);
        }
    }

    public function login(loginRequest $req)
    {
        $phoneEmail_check = $req->PhoneEmail;

        if (is_numeric($phoneEmail_check)) {
            $user = User::where('phone', $phoneEmail_check)->with('scheme')->first();
            $message = 'Incorrect phone number';
        }
        if (filter_var($phoneEmail_check, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $phoneEmail_check)->with('scheme')->first();
            $message = 'Incorrect email';
        } else {
            return response()->json([
                'success'   =>  false,
                'message'   => 'Incorrect Phone Email'
            ], 200);
        }

        if (!$user) {
            return response()->json([
                'success'   =>  false,
                'message'   => $message
            ], 200);
        } else {
            $check = Hash::check($req->password, $user->password);
            if (!$check) {
                return response()->json([
                    'success'   =>  false,
                    'message'   => 'Incorrect password'
                ], 200);
            }
            $token = $user->createToken('my-app-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'user' => $user,
                'token' => $token
            ], 200);
        }
    }

    public function logout(Request $request)
    {
        if (auth()->check()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'success' => true,
                'message' => 'Logout successfuly'
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'User is not authenticated'
        ], 401);
    }

    public function register(RegisterRequest $req)
    {
        $phoneEmail_check = $req->PhoneEmail;
        $user = new User;
        $user->name     = $req->name;
        if (is_numeric($phoneEmail_check)) {

            $user->phone    = $phoneEmail_check;
        }
        if (filter_var($phoneEmail_check, FILTER_VALIDATE_EMAIL)) {

            $user->email    = $phoneEmail_check;
            // Mail::to($phoneEmail_check)->send(new emailOTP);

        }
        $user->password = Hash::make($req->password);
        // $user->comfirmPassword = Hash::make($req->comfirm);
        $result         = $user->save();
        $data           = $user->refresh();

        if ($result) {
            // check for add to tbl otp
            $email_phone_otp = new email_phone_otp;
            $otp = rand(1000, 9999);
            if (is_numeric($phoneEmail_check)) {

                $email_phone_otp->phone = $phoneEmail_check;
            }
            if (filter_var($phoneEmail_check, FILTER_VALIDATE_EMAIL)) {

                $email_phone_otp->email = $phoneEmail_check;

                Mail::to($phoneEmail_check)->send(new emailOTP($otp));

                // return view('mail.emailOTP',compact('otp'));

            }
            $minutesToAdd = 1;
            $newTime = strtotime("+$minutesToAdd minutes");
            date_default_timezone_set('Asia/Bangkok');
            $time = date('H:i:s', $newTime);
            $email_phone_otp->otp   = Hash::make($otp);
            $email_phone_otp->expired_date  = $time;
            $email_phone_otp->save();
            //end check for add to tbl otp
            return response()->json([
                'success'   => true,
                'message'   => 'Verify code We already sent to ' . $phoneEmail_check,
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'success'   => false,
                'message'   => 'Register Error'
            ], 200);
        }
    }

    public function verify_email_otp(VerifyEmailOtpRequest $req)
    {

        date_default_timezone_set('Asia/Bangkok');
        $current_time = date('H:i:s'); // take currend time
        if (is_numeric($req->emailPhone)) {
            email_phone_otp::where('phone', $req->emailPhone)->where('expired_date', '<=', $current_time)->delete();
            $tbl_email_phone_otp = email_phone_otp::where('phone', $req->emailPhone)->first();
        }
        if (filter_var($req->emailPhone, FILTER_VALIDATE_EMAIL)) {
            email_phone_otp::where('email', $req->emailPhone)->where('expired_date', '<=', $current_time)->delete();
            $tbl_email_phone_otp = email_phone_otp::where('email', $req->emailPhone)->where('expired_date', '>', $current_time)->first();

            /*  */
        }
        if ($tbl_email_phone_otp) {
            $check = Hash::check($req->otp, $tbl_email_phone_otp->otp);
            if ($check) {

                if (is_numeric($req->emailPhone)) {

                    User::where('phone', $req->emailPhone)->update(['verify' => 1]);
                    email_phone_otp::where('phone', $req->emailPhone)->delete();
                }
                if (filter_var($req->emailPhone, FILTER_VALIDATE_EMAIL)) {

                    User::where('email', $req->emailPhone)->update(['verify' => 1]);
                    email_phone_otp::where('email', $req->emailPhone)->delete();
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Successfuly Register.'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Incorrect OTP code'
                ], 200);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Expired OTP please click the resend button'
            ], 200);
        }
    }

    public function resend_otp(Request $req)
    {
        $minutesToAdd = 1;
        $newTime = strtotime("+$minutesToAdd minutes");
        date_default_timezone_set('Asia/Bangkok');
        $time = date('H:i:s', $newTime);
        $otp = rand(1000, 9999);
        $phoneEmail_check = $req->emailPhone;
        $resend_otp = new email_phone_otp;
        if (is_numeric($phoneEmail_check)) {
            email_phone_otp::where('phone', $phoneEmail_check)->delete();
            $resend_otp->phone = $phoneEmail_check;
        }
        if (filter_var($phoneEmail_check, FILTER_VALIDATE_EMAIL)) {
            email_phone_otp::where('email', $phoneEmail_check)->delete();
            $resend_otp->email = $phoneEmail_check;
        }
        $resend_otp->otp = Hash::make($otp);
        $resend_otp->expired_date = $time;
        $result = $resend_otp->save();
        if ($result) {
            Mail::to($phoneEmail_check)->send(new emailOTP($otp));
            return response()->json([
                'success' => true,
                'message' => 'OTP code has been resend to ' . $phoneEmail_check
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Error resend OTP code'
            ], 400);
        }
    }

    public function change_password(changePasswordRequest $req)
    {

        $user = User::find($req->id);
        if ($user) {
            $chack = Hash::check($req->old_password, $user->password);
            if ($chack) {
                User::where('id', $req->id)->update(['password' => Hash::make($req->new_password)]);
                return response()->json([
                    'success' => true,
                    'message' => 'You has been change the password.'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Password not match old password.'
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Error change password.'
            ], 400);
        }
    }

    public function forgot(Request $req)
    {
        $randPassword = rand(10000000, 99999999);
        $user = User::query();
        if (is_null($req->emailPhone)) {
            $usercheck = User::where('phone', $req->emailPhone);
            $user->where('phone', $req->emailPhone);
        }
        if (filter_var($req->emailPhone, FILTER_VALIDATE_EMAIL)) {
            $usercheck = User::where('email', $req->emailPhone);
            $user->where('email', $req->emailPhone);
        }
        if ($usercheck) {
            $user->update(['password' => Hash::make($randPassword)]);
            Mail::to($req->emailPhone)->send(new forgotPassword($randPassword));
            return response()->json([
                'success' => true,
                'message' => 'We have sent you a new password on ' . $req->emailPhone
            ], 200);
        } else {
            return  'error';
        }
    }
    public function log_with_phone(Request $request)
    {

        // Set your API key here
        // $apiKey = 'AIzaSyCeElRoZ9Aj_NKeT4hfLYVHc04UJPb4uwE';

        // Set the user's temporary ID token here
        $keyPath = storage_path("app/FireBase.json");
        $factory = (new Factory)->withServiceAccount($keyPath);
        $auth = $factory->createAuth();

        // Create a temporary ID token for the user
        // $idToken = $auth->createCustomToken($request->phoneNumber)->toString();
        try {
            // change $examplePhoneNumber to yours
            $examplePhoneNumber = $request->phoneNumber;
            $user = $auth->getUserByPhoneNumber($examplePhoneNumber);
            $object = json_decode(json_encode($user));
            $phone = $object->phoneNumber;
            $compair_user = User::where('phone', $phone)->first();
            //check user has new  or not
            if ($compair_user) {
                $check = Hash::check($request->pss, $compair_user->password);
                if ($check) {
                    $token = $compair_user->createToken('my-app-token')->plainTextToken;
                    return response()->json([
                        'success' => true,
                        'is_new' => false,
                        'data' => $compair_user,
                        'token' => $token
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => "pss don't match"
                    ], 400);
                }
            } else {
                // $greate_user = new User;
                // $greate_user->phone = $examplePhoneNumber;
                // $greate_user->name = $request->name;
                // $greate_user->dob = $request->dob;
                // $created = $greate_user->save();
                return response()->json([
                    'success' => true,
                    'is_new' => true
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }


    public function create_user_with_phone(Request $request)
    {

        // Set your API key here
        // $apiKey = 'AIzaSyCeElRoZ9Aj_NKeT4hfLYVHc04UJPb4uwE';

        // Set the user's temporary ID token here
        $keyPath = storage_path("app/Firebase.json");
        $factory = (new Factory)->withServiceAccount($keyPath);
        $auth = $factory->createAuth();
        // Create a temporary ID token for the user
        // $idToken = $auth->createCustomToken($request->phoneNumber)->toString();
        try {
            // change $examplePhoneNumber to yours
            $examplePhoneNumber = $request->phoneNumber;
            $user = $auth->getUserByPhoneNumber($examplePhoneNumber);
            $object = json_decode(json_encode($user));
            $phone = $object->phoneNumber;
            $compair_user = User::where('phone', $phone)->first();
            //check user new or not
            if (!$compair_user) {
                $greate_user = new User;
                $greate_user->phone = $examplePhoneNumber;
                $greate_user->name = $request->name;
                $greate_user->dob = $request->dob;
                $greate_user->password = Hash::make(12345678);
                $created = $greate_user->save();
                $get_user = $greate_user->refresh();
                $getuser = User::where('phone', $phone)->first();
                //check pss 
                $check = Hash::check($request->pss, $getuser->password);
                if ($check) {
                    $token = $getuser->createToken('my-app-token')->plainTextToken;
                    // $update = $getuser->save();
                    return response()->json([
                        'success' => true,
                        'data' => $get_user,
                        'token' => $token
                    ], 200);
                } else {
                    $getuser = User::where('phone', $phone)->delete();
                    return response()->json([
                        'success' => false,
                        'message' => 'incorrect pss'
                    ], 400);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "The phone already created"
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
