<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    public function show_all_accounts(){
        $accounts = Account::all();
        return response()->json([
            'message'    => 'All Accounts Fetched Successfully',
            'accounts'    => $accounts,
        ], 201);
    }

    public function add_account(Request $request){
        $validateData = Validator::make($request->all(), [
            'account_name'   => 'required|alpha|min:5',
            'account_number' => 'required|unique:accounts,account_number|digits:12|numeric',
            'user_id'        => 'required|exists:users,id'
        ]);

        if($validateData->fails()){
            return response()->json([
                'message' => 'Validation Error', 
                'Error'   => $validateData->errors()
            ], 205);
        }

        $account = Account::create([
            'account_name'   => $request->account_name,
            'account_number' => $request->account_number,
            'is_default'     => true,
            'user_id'        => $request->user_id,
        ]);

        return response()->json([
            'message' => 'Account Created Successfully',
            'account'    => $account,
        ], 201);
    }

    public function show_account($id){
        $account = Account::findOrFail($id);
        return response()->json([
            'message'    => 'Account Fetched Successfully',
            'account'    => $account,
        ], 201);
    }

    public function edit_account(Request $request, $id){
        $validateAccountData = Validator::make($request->all(), [
            'account_name'   => 'required|alpha|min:5',
            'account_number' => 'required|unique:accounts,account_number|digits:12|numeric',
        ]);

        if($validateAccountData->fails()){
            return response()->json([
                'message' => 'Validation Error', 
                'Error'   => $validateAccountData->errors()
            ], 205);
        }

        $account = Account::findOrFail($id);

        DB::table('accounts')->where('id', '=', $id)->update([
            'account_name'   => $request->account_name,
            'account_number' => $request->account_number,
        ]);

        return response()->json([
            'message' => 'Account Updated Successfully',
        ], 201);
    }

    public function destroy_account($id){
        Account::findOrFail($id)->delete();
        return response()->json([
            'message'    => 'Account Deleted Successfully',
        ], 201);
    }
}
