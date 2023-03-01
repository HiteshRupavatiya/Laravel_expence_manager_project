<?php

namespace App\Http\Controllers;

use App\Models\AccountUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AccountUsersController extends Controller
{
    public function show_all_account_user(){
        $account_users = AccountUsers::all();
        return response()->json([
            'message'    => 'All Accounts User Fetched Successfully',
            'accounts'    => $account_users,
        ], 201);
    }

    public function add_account_user(Request $request){
        $validateAccountUser = Validator::make($request->all(), [
            'first_name' => 'required|min:5|alpha',
            'last_name'  => 'required|min:5|alpha',
            'email'      => 'required|email|unique:account_users',
        ]);

        if($validateAccountUser->fails()){
            return response()->json([
                'message' => 'Validation Error', 
                'Error'   => $validateAccountUser->errors()
            ], 205);
        }

        $account_user = AccountUsers::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'account_id' => 8,
        ]);

        return response()->json([
            'message'      => 'Account User Created Successfully',
            'account_user' => $account_user,
        ], 201);
    }

    public function show_account_user($id){
        $user_account = AccountUsers::findOrFail($id);
        return response()->json([
            'message'      => 'Account User Fetched Successfully',
            'account_user' => $user_account,
        ], 201); 
    }

    public function edit_account_user(Request $request, $id){
        $validateAccountUser = Validator::make($request->all(), [
            'first_name' => 'required|min:5|alpha',
            'last_name'  => 'required|min:5|alpha',
            'email'      => 'required|email|unique:account_users',
        ]);

        if($validateAccountUser->fails()){
            return response()->json([
                'message' => 'Validation Error', 
                'Error'   => $validateAccountUser->errors()
            ], 205);
        }

        $user_account = AccountUsers::findOrFail($id);

        DB::table('account_users')->where('id', '=', $id)->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
        ]);

        return response()->json([
            'message' => 'Account User Updated Successfully',
        ], 201);
    }

    public function destroy_account_user($id){
        AccountUsers::findOrFail($id)->delete();
        return response()->json([
            'message' => 'Account User Deleted Successfully',
        ], 201);
    }
}
