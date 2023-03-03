<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function logout()
    {
        if (Auth::check()) {
            Auth::logout();
            session()->flush();
        }

        return response()->json([
            'status'  => true,
            'message' => 'Logged Out Successfully'
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $validateUser = Validator::make($request->all(), [
            'password'      => 'required|current_password',
            'new_password'  => 'required|min:8'
        ]);

        if ($validateUser->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Password',
                'errors'  => $validateUser->errors()
            ], 205);
        }

        $user = Auth::user();

        $user->update(
            [
                'password' => Hash::make($request->new_password),
            ]
        );

        return response()->json([
            'status'  => true,
            'message' => 'Password Changed Successfully'
        ]);
    }

    public function getUserProfile()
    {
        $user = User::with('accounts', 'account_users', 'transactions')->findOrFail(Auth::id());
        return response()->json([
            'status'        => true,
            'message'       => 'User Details Fetched Successfully',
            'user_profile'  => $user,
        ]);
    }
}
