<?php

namespace App\Http\Controllers;

use App\Jobs\SendVerifyEmailJob;
use App\Jobs\SendWelcomeEmailJob;
use App\Mail\ResetPassword;
use App\Mail\VerifyEmail;
use App\Models\Account;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validateData = Validator::make($request->all(), [
            'first_name'            => 'required|alpha',
            'last_name'             => 'required|alpha',
            'email'                 => 'required|email|unique:users',
            'phone'                 => 'required|numeric|unique:users|min:10',
            'password'              => 'required|min:8',
            'password_confirmation' => 'required|same:password'
        ]);

        if ($validateData->fails()) {
            return response()->json([
                'message' => 'Validation Error', 
                'Error'   => $validateData->errors()
            ], 205);
        }

        $user = User::create([
            'first_name'     => $request->first_name,
            'last_name'      => $request->last_name,
            'email'          => $request->email,
            'phone'          => $request->phone,
            'password'       => Hash::make($request->password),
            'verification_token' => Str::random(64),
        ]);

        dispatch(new SendWelcomeEmailJob($user));
        // dispatch(new SendVerifyEmailJob($user));
        Mail::to($request->email)->send(new VerifyEmail($user));

        return response()->json([
            'message' => 'User Registered Successfully',
            'user'    => $user,
        ], 201);
    }

    public function login(Request $request){
        $validateUser = Validator::make($request->all(), [
            'email'    => 'required|email|exists:users,email',
            'password' => 'required',
        ]);

        if($validateUser->fails()){
            return response()->json([
                'message' => 'Validation Error', 
                'Error'   => $validateUser->errors()
            ], 205);
        }

        if(!Auth::attempt($request->only('email','password'))){
            return response()->json([
                'status'  => false,
                'message' => 'Email and Password Does Not Match',
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

        return response()->json([
            'status'   => true,
            'message'  => 'Logged In Successfully',
            'token' => $user->createToken("API TOKEN")->plainTextToken,
        ], 200);
    }

    public function logout(){
        session()->flush();
        Auth::logout();
        return response()->json([
            'status'  => true,
            'message' => 'Logged Out Successfully'
        ], 200);
    }

    public function verify_user($token){
        $verify_user = User::where('verification_token', '=', $token)->first();
        if($verify_user){
            $verify_user->update([
                'is_onboarded'       => true,
                'email_verified_at'  => now(),
                'verification_token' => '',
            ]);
            
            return response()->json([
                'message' => 'User Email Verified Successfully',
            ], 200);
        }
        else{
            return response()->json([
                'message' => 'Invalid Token',
            ], 401);
        }
    }

    public function forgot_password(Request $request){
        $validateData = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if($validateData->fails()){
            return response()->json([
                'message' => 'Validation Error', 
                'Error'   => $validateData->errors()
            ], 205);
        }

        $user = User::where('email', '=', $request->email)->first();

        if($user){
            Mail::to($user->email)->send(new ResetPassword($user));
        }
        else{
            return response()->json([
                'message' => 'Invalid Email Address'
            ]);
        }
    }

    public function reset_password(Request $request){
        $validateData = Validator::make($request->all(), [
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required',
            'token' => 'required',
        ]);

        $user = User::where('verification_token', '=', $request->token)->first();

        if($user){
            $user->update([
                'password' => Hash::make($request->password),
            ]);
            return response()->json([
                'message' => 'Password Changed Successfully'
            ]);
        }
    }

    public function getUserProfile($id){
        $user = User::find($id);
        $accounts = User::find($id)->accounts;
        return response()->json([
            'message'         => 'User Details Fetched Successfully',
            'user'            => $user,
            'account_details' => $accounts,
        ]);
    }
}
