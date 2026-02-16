<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\WalletTransactionResource;
use App\Traits\ApiResponseTrait;

class WalletController extends Controller
{
    use ApiResponseTrait;

    public function getBalance(Request $request)
    {
        return $this->successResponse([
            'balance' => $request->user()->wallet_balance,
            'currency' => __('messages.wallet.currency'),
        ], __('messages.wallet.balance_loaded'));
    }

    public function chargeWallet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'payment_method_id' => 'required|string', // cash, apple_pay, or card_id
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), 422);
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

            return $this->successResponse([
                'new_balance' => $user->wallet_balance,
                'transaction' => new WalletTransactionResource($transaction)
            ], __('messages.wallet.charge_success'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse(__('messages.wallet.charge_failed'), 500);
        }
    }

    public function getTransactions(Request $request)
    {
        $transactions = $request->user()->walletTransactions()
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->successResponse(WalletTransactionResource::collection($transactions), __('messages.wallet.transactions_loaded'));
    }
}
