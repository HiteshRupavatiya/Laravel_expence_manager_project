<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function list(){
        $transactions = Transaction::all();
        return response()->json([
            'status'        => true,
            'message'       => 'All Transactions Fetched Successfully',
            'transactions'  => $transactions,
        ], 201);
    }

    public function create(Request $request){
        $validateTransaction = Validator::make($request->all(), [
            'type'            => 'required|alpha|min:6|max:10',
            'category'        => 'required|alpha|min:5|max:15',
            'amount'          => 'required|numeric|min:1',
            'account_user_id' => 'required|exists:account_users,id|numeric',
            'account_id'      => 'required|exists:accounts,id|numeric',
        ]);

        if($validateTransaction->fails()){
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Transaction Details', 
                'errors'  => $validateTransaction->errors()
            ], 205);
        }

        $transaction = Transaction::create($request->only('type','category','amount','account_user_id','account_id'));

        return response()->json([
            'status'      => true,
            'message'     => 'Transaction Created Successfully',
            'transaction' => $transaction,
        ], 201);
    }

    public function get($id){
        $transaction = Transaction::findOrFail($id);
        return response()->json([
            'status'        => true,
            'message'       => 'Transaction Fetched Successfully',
            'transaction'  => $transaction,
        ], 201);
    }

    public function update(Request $request, $id){
        $validateTransaction = Validator::make($request->all(), [
            'type'     => 'required|alpha|min:6|max:10',
            'category' => 'required|alpha|min:5|max:15',
            'amount'   => 'required|numeric|min:1',
        ]);

        if($validateTransaction->fails()){
            return response()->json([
                'status'  => false,
                'message' => 'Invalid Transaction Details', 
                'errors'  => $validateTransaction->errors()
            ], 205);
        }

        $transaction = Transaction::findOrFail($id)->update($request->only('type','category','amount'));

        return response()->json([
            'status'  => true,
            'message' => 'Transaction Updated Successfully',
        ], 201);
    }

    public function delete($id){
        Transaction::findOrFail($id)->delete();
        return response()->json([
            'status'  => true,
            'message' => 'Transaction Deleted Successfully',
        ], 201);
    }
}
