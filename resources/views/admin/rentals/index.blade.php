@extends('admin.layouts.app')

@section('title', __('Bike Rentals Management'))

@section('content')

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">{{ __('Bike Rentals') }}</h2>
        <p class="text-sm font-medium text-gray-500">{{ __('Manage motorcycle and scooter rental requests') }}</p>
    </div>
</div>

<!-- Main Card -->
<div class="bg-white dark:bg-[#161b22] border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm overflow-hidden">
    
    <!-- Filters Header -->
    <div class="p-6 border-b border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5">
        <form action="{{ route('admin.rentals.index') }}" method="GET" x-data x-ref="filterForm">
            <div class="flex flex-col xl:flex-row gap-4 items-center justify-between">
                
                <!-- Search Box -->
                <div class="relative w-full xl:w-1/3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by order number, customer name...') }}" 
                           class="w-full bg-white dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl py-2.5 px-10 text-sm font-medium text-gray-700 dark:text-gray-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                    <i class="fa-solid fa-search absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    @if(request('search'))
                        <a href="{{ route('admin.rentals.index', request()->except('search', 'page')) }}" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500"><i class="fa-solid fa-times-circle"></i></a>
                    @endif
                </div>

                <!-- Filters -->
                <div class="flex flex-col sm:flex-row w-full xl:w-auto gap-4 items-center">
                    <!-- Status Tabs -->
                    <div class="flex bg-gray-100/80 dark:bg-black/40 p-1.5 rounded-2xl w-full sm:w-auto overflow-x-auto hide-scrollbar border border-gray-200/50 dark:border-white/5 shadow-inner">
                        <label class="cursor-pointer relative flex-1 text-center min-w-[70px]">
                            <input type="radio" name="status" value="all" class="peer sr-only" @change="$refs.filterForm.submit()" {{ request('status', 'all') == 'all' ? 'checked' : '' }}>
                            <div class="px-5 py-2 rounded-xl text-xs font-bold text-gray-500 hover:text-[#0f7c8c] peer-checked:bg-[#0f7c8c] peer-checked:text-white peer-checked:hover:text-white peer-checked:shadow-md transition-all duration-300">{{ __('All') }}</div>
                        </label>
                        <label class="cursor-pointer relative flex-1 text-center min-w-[100px]">
                            <input type="radio" name="status" value="pending" class="peer sr-only" @change="$refs.filterForm.submit()" {{ request('status') == 'pending' ? 'checked' : '' }}>
                            <div class="px-5 py-2 rounded-xl text-xs font-bold text-gray-500 hover:text-[#0f7c8c] peer-checked:bg-[#0f7c8c] peer-checked:text-white peer-checked:hover:text-white peer-checked:shadow-md transition-all duration-300 whitespace-nowrap">{{ __('Waitlist') }}</div>
                        </label>
                        <label class="cursor-pointer relative flex-1 text-center min-w-[110px]">
                            <input type="radio" name="status" value="active" class="peer sr-only" @change="$refs.filterForm.submit()" {{ request('status') == 'active' ? 'checked' : '' }}>
                            <div class="px-5 py-2 rounded-xl text-xs font-bold text-gray-500 hover:text-[#0f7c8c] peer-checked:bg-[#0f7c8c] peer-checked:text-white peer-checked:hover:text-white peer-checked:shadow-md transition-all duration-300 whitespace-nowrap">{{ __('Rented (Active)') }}</div>
                        </label>
                        <label class="cursor-pointer relative flex-1 text-center min-w-[70px]">
                            <input type="radio" name="status" value="completed" class="peer sr-only" @change="$refs.filterForm.submit()" {{ request('status') == 'completed' ? 'checked' : '' }}>
                            <div class="px-5 py-2 rounded-xl text-xs font-bold text-gray-500 hover:text-[#0f7c8c] peer-checked:bg-[#0f7c8c] peer-checked:text-white peer-checked:hover:text-white peer-checked:shadow-md transition-all duration-300">{{ __('Completed') }}</div>
                        </label>
                        <label class="cursor-pointer relative flex-1 text-center min-w-[70px]">
                            <input type="radio" name="status" value="cancelled" class="peer sr-only" @change="$refs.filterForm.submit()" {{ request('status') == 'cancelled' ? 'checked' : '' }}>
                            <div class="px-5 py-2 rounded-xl text-xs font-bold text-gray-500 hover:text-[#0f7c8c] peer-checked:bg-[#0f7c8c] peer-checked:text-white peer-checked:hover:text-white peer-checked:shadow-md transition-all duration-300">{{ __('Cancelled') }}</div>
                        </label>
                    </div>

                    <!-- Sort -->
                    <div class="w-full sm:w-48 relative">
                        <select name="sort" @change="$refs.filterForm.submit()" class="w-full appearance-none bg-white dark:bg-black/30 border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 text-sm font-bold rounded-xl px-4 py-2.5 pr-10 focus:outline-none focus:border-amber-500 transition">
                            <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>{{ __('Latest first') }}</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('Oldest first') }}</option>
                            <option value="highest_cost" {{ request('sort') == 'highest_cost' ? 'selected' : '' }}>{{ __('Highest profit') }}</option>
                        </select>
                        <i class="fa-solid fa-sort absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    </div>
                </div>
            </div>
            <button type="submit" class="hidden"></button>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-right whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-white/5 text-gray-500 dark:text-gray-400 text-sm border-b border-gray-100 dark:border-white/5">
                    <th class="px-6 py-4 font-bold text-right">{{ __('Order Number') }}</th>
                    <th class="px-6 py-4 font-bold text-right">{{ __('Rental Date') }}</th>
                    <th class="px-6 py-4 font-bold text-right">{{ __('Renting Customer') }}</th>
                    <th class="px-6 py-4 font-bold text-right">{{ __('Duration') }}</th>
                    <th class="px-6 py-4 font-bold text-right">{{ __('Cost') }}</th>
                    <th class="px-6 py-4 font-bold text-right">{{ __('Rental Status') }}</th>
                    <th class="px-6 py-4 font-bold text-center">{{ __('Details') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                @forelse($rentals as $rental)
                <tr class="hover:bg-gray-50/80 dark:hover:bg-white/5 transition duration-150">
                    
                    <!-- Order Number -->
                    <td class="px-6 py-4 text-right">
                        <div class="font-black text-gray-800 dark:text-gray-200" dir="ltr" style="display: inline-block;">
                            {{ $rental->order_number }}#
                        </div>
                    </td>

                    <!-- Date & Time -->
                    <td class="px-6 py-4 text-right">
                        <div class="text-sm font-bold text-gray-600 dark:text-gray-400" dir="ltr" style="display: inline-block;">
                            {{ $rental->scheduled_at ? $rental->scheduled_at->format('M d, Y - h:i A') : $rental->created_at->format('M d, Y - h:i A') }}
                        </div>
                    </td>

                    <!-- Customer -->
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-user text-amber-500 bg-amber-50 rounded p-1 text-[10px]"></i>
                            <span class="font-bold text-gray-700 text-sm truncate w-32" title="{{ $rental->user->name ?? 'غير متوفر' }}">{{ $rental->user->name ?? 'غير متوفر' }}</span>
                        </div>
                    </td>

                    <!-- Duration -->
                    <td class="px-6 py-4 text-right">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 dark:bg-white/5 rounded-full font-bold text-gray-600 dark:text-gray-300 text-xs">
                            <i class="fa-regular fa-clock"></i> {{ $rental->duration_hours }} ساعة
                        </span>
                    </td>

                    <!-- Cost -->
                    <td class="px-6 py-4 text-right">
                        <div class="font-black text-gray-800 dark:text-gray-200 flex items-center gap-1">
                            {{ number_format($rental->cost ?? 0, 2) }} <span class="text-[10px] font-bold text-gray-400">SAR</span>
                        </div>
                        <div class="text-[10px] font-bold {{ $rental->payment_status == 'paid' ? 'text-emerald-500' : 'text-amber-500' }} mt-1">
                           {{ $rental->payment_method }} • {{ $rental->payment_status == 'paid' ? 'مدفوع' : 'غير مدفوع' }}
                        </div>
                    </td>

                    <!-- Status -->
                    <td class="px-6 py-4 text-right">
                        @if($rental->status == 'pending')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded bg-amber-50 text-amber-600 text-xs font-bold border border-amber-100">
                                <i class="fa-solid fa-clock"></i> قيد الانتظار
                            </span>
                        @elseif($rental->status == 'active')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded bg-blue-50 text-blue-600 text-xs font-bold border border-blue-100">
                                <i class="fa-solid fa-key"></i> {{ __('Currently Rented') }}
                            </span>
                        @elseif($rental->status == 'completed')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded bg-emerald-50 text-emerald-600 text-xs font-bold border border-emerald-100">
                                <i class="fa-solid fa-check-double"></i> {{ __('Completed') }}
                            </span>
                        @elseif($rental->status == 'cancelled')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded bg-red-50 text-red-600 text-xs font-bold border border-red-100">
                                <i class="fa-solid fa-xmark"></i> {{ __('Cancelled') }}
                            </span>
                        @endif
                    </td>

                    <!-- Action -->
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.rentals.show', $rental->id) }}" class="inline-flex items-center justify-center gap-2 px-3 h-8 rounded-lg bg-gray-50 hover:bg-amber-600 dark:bg-white/10 text-gray-600 hover:text-white dark:text-gray-300 font-bold text-xs transition border border-gray-200 dark:border-white/5 hover:border-transparent">
                            {{ __('Details') }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-gray-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-3">
                                <i class="fa-solid fa-key text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-base font-bold text-gray-600 dark:text-gray-300">{{ __('No rental data found') }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Check filters or try another search') }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($rentals->hasPages())
    <div class="p-4 border-t border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5">
        {{ $rentals->links() }}
    </div>
    @endif

</div>
@endsection
