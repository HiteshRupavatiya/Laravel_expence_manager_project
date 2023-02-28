<?php

namespace App\Http\Controllers;

use App\Jobs\SendVerifyEmailJob;
use App\Jobs\SendWelcomeEmailJob;
use App\Models\Account;
use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        dispatch(new SendVerifyEmailJob($user));

        $account = Account::create([
            'account_name'   => $user->first_name . " " . $user->last_name,
            'account_number' => fake()->unique()->regexify('[1-9]{1}[0-9]{11}'),
            'is_default'     => 1,
            'user_id'        => $user->id,
        ]);

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
            return $validateUser->errors();
        }

        if(!Auth::attempt($request->only('email','password'))){
            return response()->json([
                'status'  => false,
                'message' => 'Email and Password Does Not Match',
            ], 401);
        }

        return response()->json([
            'status'   => true,
            'message'  => 'Logged In Successfully'
        ], 200);
    }

    public function logout(){
        Session::flush();
        Auth::logout();
        return response()->json([
            'status'  => true,
            'message' => 'Logged Out Successfully'
        ], 200);
    }

    public function varifyUser($token){
        return $token;
    }
}
