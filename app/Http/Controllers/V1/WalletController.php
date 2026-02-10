<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\WalletTransactionResource;

class WalletController extends Controller
{
    public function getBalance(Request $request)
    {
        $currency = app()->getLocale() === 'ar' ? 'ريال' : 'SAR';
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'balance' => $request->user()->wallet_balance,
                'currency' => $currency,
            ]
        ]);
    }

    public function chargeWallet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'payment_method_id' => 'required|string', // cash, apple_pay, or card_id
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()->first()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $user = $request->user();
            $amount = $request->amount;

            // In a real app, call payment gateway here
            
            $user->wallet_balance += $amount;
            $user->save();

            $transaction = $user->walletTransactions()->create([
                'amount' => $amount,
                'type' => 'credit',
                'transaction_type' => 'charge',
                'description_ar' => 'شحن المحفظة',
                'description_en' => 'Wallet Recharge',
                'status' => 'completed',
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Wallet charged successfully',
                'data' => [
                    'new_balance' => $user->wallet_balance,
                    'transaction' => new WalletTransactionResource($transaction)
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to charge wallet'
            ], 500);
        }
    }

    public function getTransactions(Request $request)
    {
        $transactions = $request->user()->walletTransactions()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => WalletTransactionResource::collection($transactions)
        ]);
    }
}
