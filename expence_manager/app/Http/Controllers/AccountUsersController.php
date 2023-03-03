<?php

namespace App\Http\Controllers;

use App\Models\AccountUsers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountUsersController extends Controller
{
    public function list()
    {
        $account_users = AccountUsers::all();
        return response()->json([
            'status'        => true,
            'message'       => 'All Account Users Fetched Successfully',
            'account_users' => $account_users,
        ], 201);
    }

    public function create(Request $request)
    {
        $validateAccountUser = Validator::make($request->all(), [
            'email'      => 'required|email|unique:account_users,email',
            'account_id' => 'required|exists:accounts,id'
        ]);

        if ($validateAccountUser->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Account User Details',
                'errors'  => $validateAccountUser->errors()
            ], 205);
        }

        $userData = User::with('accounts')->first();

        $user_account = AccountUsers::create(
            $request->only('email', 'account_id') +
                [
                    'first_name' => $userData['first_name'],
                    'last_name'  => $userData['last_name'],
                ]
        );

        return response()->json([
            'status'       => true,
            'message'      => 'Account User Added Successfully',
            'account_user' => $user_account,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validateAccountUser = Validator::make($request->all(), [
            'first_name' => 'required|alpha|min:5|max:15',
            'last_name'  => 'required|alpha|min:5|max:15',
            'email'      => 'required|email|unique:account_users,email',
        ]);

        if ($validateAccountUser->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Account User Details',
                'errors'   => $validateAccountUser->errors()
            ], 205);
        }

        $user_account = AccountUsers::findOrFail($id)->update(
            [
                'first_name',
                'last_name',
                'email'
            ]
        );

        return response()->json([
            'status'  => true,
            'message' => 'Account User Updated Successfully',
        ], 201);
    }

    public function get($id)
    {
        $user_account = AccountUsers::with('transactions')->findOrFail($id);
        return response()->json([
            'status'       => true,
            'message'      => 'Account User Fetched Successfully',
            'account_user' => $user_account,
        ], 201);
    }

    public function delete($id)
    {
        AccountUsers::findOrFail($id)->delete();
        return response()->json([
            'status'  => true,
            'message' => 'Account User Deleted Successfully',
        ], 201);
    }
}
