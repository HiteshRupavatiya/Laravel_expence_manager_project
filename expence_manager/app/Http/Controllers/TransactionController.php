<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function show_all_transaction(){
        $transactions = Transaction::all();
        return response()->json([
            'message'    => 'All Transactions Fetched Successfully',
            'accounts'    => $transactions,
        ], 201);
    }

    public function add_transaction(Request $request){
        $validateTransaction = Validator::make($request->all(), [
            'type'     => 'required|alpha',
            'category' => 'required|alpha|min:3',
            'amount'   => 'required|numeric|min:1',
        ]);

        if($validateTransaction->fails()){
            return response()->json([
                'message' => 'Validation Error', 
                'Error'   => $validateTransaction->errors()
            ], 205);
        }

        $transaction = Transaction::create([
            'type'            => $request->type,
            'category'        => $request->category,
            'amount'          => $request->amount,
            'account_user_id' => 3,
            'account_id'      => 8,
        ]);

        return response()->json([
            'message'      => 'Transaction Created Successfully',
            'transaction' => $transaction,
        ], 201);
    }

    public function show_transaction($id){
        $transaction = Transaction::findOrFail($id);
        return response()->json([
            'message'       => 'Transaction Fetched Successfully',
            'account_user'  => $transaction,
        ], 201);
    }

    public function edit_transaction(Request $request, $id){
        $validateTransaction = Validator::make($request->all(), [
            'type'     => 'required|alpha',
            'category' => 'required|alpha|min:3',
            'amount'   => 'required|numeric|min:1',
        ]);

        if($validateTransaction->fails()){
            return response()->json([
                'message' => 'Validation Error', 
                'Error'   => $validateTransaction->errors()
            ], 205);
        }

        $transaction = Transaction::findOrFail($id);

        DB::table('transactions')->where('id', '=', $id)->update([
            'type'     => $request->type,
            'category' => $request->category,
            'amount'   => $request->amount,
        ]);

        return response()->json([
            'message' => 'Transaction Updated Successfully',
        ], 201);
    }

    public function destroy_transaction($id){
        Transaction::findOrFail($id)->delete();
        return response()->json([
            'message' => 'Transaction Deleted Successfully',
        ], 201);
    }
}
