@extends('admin.layouts.app')

@section('title', __('Financial Records and Wallet'))

@section('content')

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">{{ __('Wallet Transactions') }}</h2>
        <p class="text-sm font-medium text-gray-500">{{ __('Monitor all financial operations and transfers within the system') }}</p>
    </div>
</div>

<!-- Financial Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gradient-to-tr from-emerald-500 to-emerald-700 rounded-3xl p-6 text-white shadow-lg shadow-emerald-500/20">
        <p class="text-xs font-bold opacity-80 mb-2 uppercase tracking-wider">{{ __('Total Deposits') }}</p>
        <h3 class="text-3xl font-black">{{ number_format(\App\Models\WalletTransaction::where('type', 'deposit')->where('status', 'completed')->sum('amount'), 2) }} <span class="text-sm font-bold opacity-70">SAR</span></h3>
    </div>
    <div class="bg-gradient-to-tr from-amber-500 to-amber-700 rounded-3xl p-6 text-white shadow-lg shadow-amber-500/20">
        <p class="text-xs font-bold opacity-80 mb-2 uppercase tracking-wider">{{ __('Total Withdrawals') }}</p>
        <h3 class="text-3xl font-black">{{ number_format(\App\Models\WalletTransaction::where('type', 'withdraw')->where('status', 'completed')->sum('amount'), 2) }} <span class="text-sm font-bold opacity-70">SAR</span></h3>
    </div>
    <div class="bg-white dark:bg-[#161b22] border border-gray-100 dark:border-white/5 rounded-3xl p-6 shadow-sm">
        <p class="text-xs font-bold text-gray-400 mb-2 uppercase tracking-wider">{{ __('Net Trading') }}</p>
        <h3 class="text-3xl font-black text-gray-800 dark:text-white">{{ number_format(\App\Models\WalletTransaction::where('status', 'completed')->where('type', 'deposit')->sum('amount') - \App\Models\WalletTransaction::where('status', 'completed')->where('type', 'withdraw')->sum('amount'), 2) }} <span class="text-sm font-bold text-gray-400">SAR</span></h3>
    </div>
</div>

<div class="bg-white dark:bg-[#161b22] border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-right whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-white/5 text-gray-500 dark:text-gray-400 text-sm border-b border-gray-100 dark:border-white/5">
                    <th class="px-6 py-4 font-bold">{{ __('User') }}</th>
                    <th class="px-6 py-4 font-bold">{{ __('Transaction Type') }}</th>
                    <th class="px-6 py-4 font-bold">{{ __('Description') }}</th>
                    <th class="px-6 py-4 font-bold text-center">{{ __('Amount') }}</th>
                    <th class="px-6 py-4 font-bold">{{ __('Date') }}</th>
                    <th class="px-6 py-4 font-bold">{{ __('Status') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                @forelse($transactions as $transaction)
                <tr class="hover:bg-gray-50/80 dark:hover:bg-white/5 transition duration-150">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center font-bold text-gray-600 text-xs">
                                {{ mb_substr($transaction->user->name ?? 'ع', 0, 1) }}
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-white">{{ $transaction->user->name ?? __('User') }}</h4>
                                <p class="text-[10px] text-gray-400 font-bold" dir="ltr">{{ $transaction->user->phone ?? '---' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($transaction->type == 'deposit')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase ring-1 ring-emerald-100">
                                <i class="fa-solid fa-arrow-down"></i> {{ __('Deposit') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-amber-50 text-amber-600 text-[10px] font-black uppercase ring-1 ring-amber-100">
                                <i class="fa-solid fa-arrow-up"></i> {{ __('Withdrawal') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-xs font-bold text-gray-600 dark:text-gray-300">
                        {{ $transaction->description_ar }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-black {{ $transaction->type == 'deposit' ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ $transaction->type == 'deposit' ? '+' : '-' }} {{ number_format($transaction->amount, 2) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs font-medium text-gray-500" dir="ltr">
                        {{ $transaction->created_at->format('Y-m-d H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($transaction->status == 'completed')
                            <span class="flex items-center gap-1.5 text-emerald-500 font-bold text-xs"><i class="fa-solid fa-circle-check text-[10px]"></i> {{ __('Successful') }}</span>
                        @elseif($transaction->status == 'pending')
                            <span class="flex items-center gap-1.5 text-amber-500 font-bold text-xs"><i class="fa-solid fa-clock text-[10px]"></i> {{ __('Pending') }}</span>
                        @else
                            <span class="flex items-center gap-1.5 text-red-500 font-bold text-xs"><i class="fa-solid fa-circle-xmark text-[10px]"></i> {{ __('Failed') }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-20 text-center text-gray-400 font-bold">{{ __('No financial transactions recorded') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-8">
    {{ $transactions->links() }}
</div>

@endsection
