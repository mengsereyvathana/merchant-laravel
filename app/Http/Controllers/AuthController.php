<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\loginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\VerifyEmailOtpRequest;
use App\Mail\EmailOTP;
use App\Mail\ForgotPassword;
use App\Models\PhoneOtp;
use Illuminate\Support\Facades\Mail;
use Kreait\Firebase\Factory;
use Exception;
//kakaka
class AuthController extends Controller
{
    public function login(loginRequest $request)
    {
        $user = User::where('phone', $request->phoneEmail)
            ->orWhere('email', $request->phoneEmail)
            ->with('scheme')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Incorrect credentials'
                ],
                200
            );
        }

        $token = $user->createToken('my-app-token')->plainTextToken;

        return response()->json(['success' => true, 'user' => $user, 'token' => $token], 200);
    }

    public function logout(Request $request)
    {
        if (auth()->check()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['success' => true, 'message' => 'Logout successfully'], 200);
        }

        return response()->json(
            [
                'success' => false,
                'message' => 'User is not authenticated'
            ],
            401
        );
    }

    public function register(RegisterRequest $request)
    {
        $user = new User;
        $user->name = $request->name;

        if (is_numeric($request->PhoneEmail)) {
            $user->phone = $request->PhoneEmail;
        }

        if (filter_var($request->PhoneEmail, FILTER_VALIDATE_EMAIL)) {
            $user->email = $request->PhoneEmail;
            Mail::to($request->PhoneEmail)->send(new EmailOTP(rand(1000, 9999)));
        }

        $user->password = Hash::make($request->password);
        $result = $user->save();

        if ($result) {
            $this->sendOtpAndSave($request->PhoneEmail);
            return response()->json(['success' => true, 'message' => 'Verification code sent', 'data' => $user], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Registration error'], 200);
        }
    }

    public function verifyEmailOtp(VerifyEmailOtpRequest $request)
    {
        date_default_timezone_set('Asia/Bangkok');
        $currentTime = now()->format('H:i:s');
        $phoneEmail = $request->emailPhone;

        if (is_numeric($phoneEmail)) {
            PhoneOtp::where('phone', $phoneEmail)->where('expired_date', '<=', $currentTime)->delete();
            $phoneOtp = PhoneOtp::where('phone', $phoneEmail)->first();
        } elseif (filter_var($phoneEmail, FILTER_VALIDATE_EMAIL)) {
            PhoneOtp::where('email', $phoneEmail)->where('expired_date', '<=', $currentTime)->delete();
            $phoneOtp = PhoneOtp::where('email', $phoneEmail)->where('expired_date', '>', $currentTime)->first();
        }

        if ($phoneOtp) {
            $check = Hash::check($request->otp, $phoneOtp->otp);

            if ($check) {
                $this->updateUserVerification($phoneEmail);
                return response()->json(['success' => true, 'message' => 'Successfully registered.'], 200);
            } else {
                return response()->json(['success' => false, 'message' => 'Incorrect OTP code'], 200);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Expired OTP, please click the resend button'], 200);
        }
    }

    public function resendOtp(Request $request)
    {
        $minutesToAdd = 1;
        $newTime = strtotime("+$minutesToAdd minutes");
        date_default_timezone_set('Asia/Bangkok');
        $time = date('H:i:s', $newTime);
        $otp = rand(1000, 9999);
        $phoneEmail = $request->emailPhone;

        PhoneOtp::where('phone', $phoneEmail)->orWhere('email', $phoneEmail)->delete();

        $resendOtp = new PhoneOtp;
        $resendOtp->otp = Hash::make($otp);
        $resendOtp->expired_date = $time;

        $result = $resendOtp->save();

        if ($result) {
            Mail::to($phoneEmail)->send(new EmailOTP($otp));
            return response()->json(['success' => true, 'message' => 'OTP code has been resent to ' . $phoneEmail], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Error resending OTP code'], 400);
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $user = User::find($request->id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        $isPasswordCorrect = Hash::check($request->old_password, $user->password);

        if (!$isPasswordCorrect) {
            return response()->json([
                'success' => false,
                'message' => 'Password does not match the old password.'
            ], 400);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json([
            'success' => true,
            'message' => 'Your password has been changed.'
        ], 200);
    }

    public function forgotPassword(Request $request)
    {
        $randPassword = rand(10000000, 99999999);
        $emailPhone = $request->emailPhone;

        $user = User::where('phone', $emailPhone)->orWhere('email', $emailPhone)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.'
            ], 404);
        }

        $user->update(['password' => Hash::make($randPassword)]);
        Mail::to($emailPhone)->send(new ForgotPassword($randPassword));

        return response()->json([
            'success' => true,
            'message' => 'We have sent you a new password on ' . $emailPhone
        ], 200);
    }
    public function loginWithPhone(Request $request)
    {
        $phoneNumber = $request->phoneNumber;
        $auth = $this->getFirebaseAuth();

        try {
            $user = $auth->getUserByPhoneNumber($phoneNumber);
            $comparedUser = User::where('phone', $user->phoneNumber)->first();

            if ($comparedUser) {
                $isPasswordCorrect = Hash::check($request->password, $comparedUser->password);

                if ($isPasswordCorrect) {
                    $token = $comparedUser->createToken('my-app-token')->plainTextToken;

                    return response()->json([
                        'success' => true,
                        'is_new' => false,
                        'data' => $comparedUser,
                        'token' => $token
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => "Password doesn't match"
                    ], 400);
                }
            } else {
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

    public function createUserWithPhone(Request $request)
    {
        $phoneNumber = $request->phoneNumber;
        $auth = $this->getFirebaseAuth();

        try {
            $user = $auth->getUserByPhoneNumber($phoneNumber);
            $comparedUser = User::where('phone', $user->phoneNumber)->first();

            if ($comparedUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'The phone already exists.'
                ], 200);
            }

            $newUser = new User;
            $newUser->phone = $phoneNumber;
            $newUser->name = $request->name;
            $newUser->dob = $request->dob;
            $newUser->password = Hash::make(12345678);
            $newUser->save();

            $token = $newUser->createToken('my-app-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => $newUser,
                'token' => $token
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    private function getFirebaseAuth()
    {
        $keyPath = storage_path("app/Firebase.json");
        $factory = (new Factory)->withServiceAccount($keyPath);

        return $factory->createAuth();
    }

    private function sendOtpAndSave($phoneEmail)
    {
        $otp = rand(1000, 9999);
        $phoneOtp = new PhoneOtp;

        if (is_numeric($phoneEmail)) {
            $phoneOtp->phone = $phoneEmail;
        }

        if (filter_var($phoneEmail, FILTER_VALIDATE_EMAIL)) {
            $phoneOtp->email = $phoneEmail;
        }

        $phoneOtp->otp = Hash::make($otp);
        $phoneOtp->expired_date = now()->addMinutes(1);
        $phoneOtp->save();
    }
}
