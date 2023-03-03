<?php

namespace App\Http\Controllers;

use App\Mail\ResetPassword;
use App\Mail\VerifyEmail;
use App\Models\Account;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'first_name'            => 'required|alpha|min:5|max:15',
            'last_name'             => 'required|alpha|min:5|max:15',
            'email'                 => 'required|email|unique:users,email|max:40',
            'phone'                 => 'required|numeric|unique:users,phone|min:10',
            'password'              => 'required|min:8',
            'password_confirmation' => 'required|same:password',
            'account_name'          => 'required|alpha|min:5|max:20',
            'account_number'        => 'required|unique:accounts,account_number|digits:12|numeric',
        ]);

        if ($validateData->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid User Details',
                'errors'  => $validateData->errors()
            ], 205);
        }

        $user = User::create(
            $request->only(['first_name', 'last_name', 'email', 'phone'])
                + [
                    'password'           => Hash::make($request->password),
                    'verification_token' => Str::random(64)
                ]
        );

        $defaultAccount = Account::create(
            $request->only('account_name', 'account_number') +
                [
                    'is_default' => true,
                    'user_id'    => $user->id,
                ]
        );

        //dispatch(new SendWelcomeEmailJob($user));
        Mail::to($request->email)->send(new VerifyEmail($user));

        return response()->json([
            'status'       => true,
            'message'      => 'User Registered Successfully',
            'user_data'    => $user,
            'account_data' => $defaultAccount,
        ], 201);
    }

    public function login(Request $request)
    {
        $validateUser = Validator::make($request->all(), [
            'email'    => 'required|email|exists:users,email',
            'password' => 'required',
        ]);

        if ($validateUser->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Credintials',
                'errors'  => $validateUser->errors()
            ], 205);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status'  => false,
                'message' => 'Email and Password Does Not Match',
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        return response()->json([
            'status'   => true,
            'message'  => 'Logged In Successfully',
            'token'    => $user->createToken("API TOKEN")->plainTextToken,
        ], 200);
    }

    public function verifyUser($token)
    {
        $verify_user = User::where('verification_token', '=', $token)->first();
        if ($verify_user) {
            $verify_user->update([
                'is_onboarded'       => true,
                'email_verified_at'  => now(),
                'verification_token' => '',
            ]);

            return response()->json([
                'message' => 'User Email Verified Successfully',
            ], 200);
        } else {
            return response()->json([
                'message' => 'Invalid Token',
            ], 401);
        }
    }

    public function forgotPassword(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validateData->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Email Address',
                'errors'  => $validateData->errors()
            ], 205);
        }

        $token = Str::random(64);

        PasswordReset::create(
            $request->only('email') +
                [
                    'token'      => $token,
                    'created_at' => now(),
                    'expired_at' => now()->addDays(2)
                ]
        );

        Mail::to($request->email)->send(new ResetPassword($token));
        return response()->json([
            'status'  => true,
            'message' => 'Email Sent To Your Mail Id',
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'email'                 => 'required|exists:users,email',
            'password'              => 'required|min:8|max:20|confirmed',
            'password_confirmation' => 'required',
            'token'                 => 'required|exists:password_resets,token',
        ]);

        if ($validateData->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Email Address',
                'errors'  => $validateData->errors()
            ], 205);
        }



        $hasData = PasswordReset::where('email', $request->email)->first();

        $hasData->expired_at >= $hasData->created_at;

        if ($hasData) {
            $user = User::where('email', '=', $request->email)->first();
            if ($user) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);

                PasswordReset::where('email', $request->email)->delete();

                return response()->json([
                    'status'  => true,
                    'message' => 'Password Changed Successfully'
                ]);
            }
        } else {
            return response()->json([
                'status'  => false,
                'message' => 'Token Has Been expired',
            ], 500);
        }
    }
}
