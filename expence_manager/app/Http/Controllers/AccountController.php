<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function list()
    {
        $accounts = Account::all();
        return response()->json([
            'status'   => true,
            'message'  => 'All Accounts Fetched Successfully',
            'accounts' => $accounts,
        ], 201);
    }

    public function create(Request $request)
    {
        $validateAccount = Validator::make($request->all(), [
            'account_name'   => 'required|alpha|min:5|max:15',
            'account_number' => 'required|unique:accounts,account_number|digits:12|numeric',
            'user_id'        => 'required|exists:users,id'
        ]);

        if ($validateAccount->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Account Details',
                'errors'  => $validateAccount->errors()
            ], 205);
        }

        $account = Account::create(
            $request->only('account_name', 'account_number', 'user_id') +
                [
                    'is_default' => true,
                ]
        );

        return response()->json([
            'status'  => true,
            'message' => 'Account Created Successfully',
            'account' => $account,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validateAccountData = Validator::make($request->all(), [
            'account_name'   => 'required|alpha|min:5|max:15',
        ]);

        if ($validateAccountData->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Account Details',
                'errors'  => $validateAccountData->errors()
            ], 205);
        }

        $account = Account::findOrFail($id);

        $account->update($request->only('account_name'));

        return response()->json([
            'status'  => true,
            'message' => 'Account Updated Successfully',
        ], 201);
    }

    public function get($id)
    {
        $account = Account::with('account_users')->findOrFail($id);
        return response()->json([
            'status'   => true,
            'message'  => 'Account Fetched Successfully',
            'account'  => $account,
        ], 201);
    }

    public function delete($id)
    {
        Account::findOrFail($id)->delete();
        return response()->json([
            'status'  => true,
            'message' => 'Account Deleted Successfully',
        ], 201);
    }
}
