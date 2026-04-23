@extends('admin.layouts.app')

@section('title', __('Car Wash Management'))

@section('content')

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">{{ __('Car Washes') }}</h2>
        <p class="text-sm font-medium text-gray-500">{{ __('Manage car wash bookings, appointments, and agents') }}</p>
    </div>
</div>

<!-- Main Card -->
<div class="bg-white dark:bg-[#161b22] border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm overflow-hidden">
    
    <!-- Filters Header -->
    <div class="p-6 border-b border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5">
        <form action="{{ route('admin.car_washes.index') }}" method="GET" x-data x-ref="filterForm">
            <div class="flex flex-col xl:flex-row gap-4 items-center justify-between">
                
                <!-- Search Box -->
                <div class="relative w-full xl:w-1/3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search by order number, customer name...') }}" 
                           class="w-full bg-white dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl py-2.5 px-10 text-sm font-medium text-gray-700 dark:text-gray-200 focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 transition">
                    <i class="fa-solid fa-search absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    @if(request('search'))
                        <a href="{{ route('admin.car_washes.index', request()->except('search', 'page')) }}" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500"><i class="fa-solid fa-times-circle"></i></a>
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
                        <label class="cursor-pointer relative flex-1 text-center min-w-[150px]">
                            <input type="radio" name="status" value="pending" class="peer sr-only" @change="$refs.filterForm.submit()" {{ request('status') == 'pending' ? 'checked' : '' }}>
                            <div class="px-5 py-2 rounded-xl text-xs font-bold text-gray-500 hover:text-[#0f7c8c] peer-checked:bg-[#0f7c8c] peer-checked:text-white peer-checked:hover:text-white peer-checked:shadow-md transition-all duration-300 whitespace-nowrap">{{ __('New (Awaiting Confirmation)') }}</div>
                        </label>
                        <label class="cursor-pointer relative flex-1 text-center min-w-[120px]">
                            <input type="radio" name="status" value="confirmed" class="peer sr-only" @change="$refs.filterForm.submit()" {{ request('status') == 'confirmed' ? 'checked' : '' }}>
                            <div class="px-5 py-2 rounded-xl text-xs font-bold text-gray-500 hover:text-[#0f7c8c] peer-checked:bg-[#0f7c8c] peer-checked:text-white peer-checked:hover:text-white peer-checked:shadow-md transition-all duration-300 whitespace-nowrap">{{ __('Confirmed and Agent Assigned') }}</div>
                        </label>
                        <label class="cursor-pointer relative flex-1 text-center min-w-[120px]">
                            <input type="radio" name="status" value="completed" class="peer sr-only" @change="$refs.filterForm.submit()" {{ request('status') == 'completed' ? 'checked' : '' }}>
                            <div class="px-5 py-2 rounded-xl text-xs font-bold text-gray-500 hover:text-[#0f7c8c] peer-checked:bg-[#0f7c8c] peer-checked:text-white peer-checked:hover:text-white peer-checked:shadow-md transition-all duration-300 whitespace-nowrap">{{ __('Wash Completed') }}</div>
                        </label>
                        <label class="cursor-pointer relative flex-1 text-center min-w-[70px]">
                            <input type="radio" name="status" value="cancelled" class="peer sr-only" @change="$refs.filterForm.submit()" {{ request('status') == 'cancelled' ? 'checked' : '' }}>
                            <div class="px-5 py-2 rounded-xl text-xs font-bold text-gray-500 hover:text-[#0f7c8c] peer-checked:bg-[#0f7c8c] peer-checked:text-white peer-checked:hover:text-white peer-checked:shadow-md transition-all duration-300">{{ __('Cancelled') }}</div>
                        </label>
                    </div>

                    <!-- Sort -->
                    <div class="w-full sm:w-48 relative">
                        <select name="sort" @change="$refs.filterForm.submit()" class="w-full appearance-none bg-white dark:bg-black/30 border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 text-sm font-bold rounded-xl px-4 py-2.5 pr-10 focus:outline-none focus:border-sky-500 transition">
                            <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>{{ __('Latest first') }}</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('Oldest first') }}</option>
                            <option value="highest_cost" {{ request('sort') == 'highest_cost' ? 'selected' : '' }}>{{ __('Highest value') }}</option>
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
                    <th class="px-6 py-4 font-bold text-right">{{ __('Booking Number') }}</th>
                    <th class="px-6 py-4 font-bold text-right">{{ __('Wash Appointment') }}</th>
                    <th class="px-6 py-4 font-bold text-right">{{ __('Customer') }}</th>
                    <th class="px-6 py-4 font-bold text-right">{{ __('Car Wash Agent') }}</th>
                    <th class="px-6 py-4 font-bold text-right">{{ __('Car Type / Service') }}</th>
                    <th class="px-6 py-4 font-bold text-right">{{ __('Cost') }}</th>
                    <th class="px-6 py-4 font-bold text-right">{{ __('Booking Status') }}</th>
                    <th class="px-6 py-4 font-bold text-center">{{ __('Details') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                @forelse($carWashes as $wash)
                <tr class="hover:bg-gray-50/80 dark:hover:bg-white/5 transition duration-150">
                    
                    <!-- ID Number -->
                    <td class="px-6 py-4 text-right">
                        <div class="font-black text-gray-800 dark:text-gray-200" dir="ltr" style="display: inline-block;">
                            {{ $wash->id }}#
                        </div>
                    </td>

                    <!-- Date & Period -->
                    <td class="px-6 py-4 text-right">
                        <div class="text-sm font-bold text-gray-800 dark:text-gray-200" dir="ltr" style="display: inline-block;">
                            {{ optional($wash->scheduled_date)->format('M d, Y') ?? '---' }}
                        </div>
                        <div class="text-[11px] font-bold text-gray-500 mt-1">
                            الفترة: <span class="bg-gray-100 px-1 rounded">{{ str_replace('_', ' ', clone $wash)->time_period ?? '---' }}</span>
                        </div>
                    </td>

                    <!-- Customer -->
                    <td class="px-6 py-4 text-right">
                        <div class="text-sm font-bold text-amber-600 bg-amber-50 dark:bg-amber-500/10 dark:text-amber-400 px-3 py-1 rounded inline-block">
                            {{ mb_substr($wash->user->name ?? 'غير متوفر', 0, 15) }}...
                        </div>
                    </td>

                    <!-- Agent -->
                    <td class="px-6 py-4 text-right">
                        @if($wash->agent)
                            <div class="text-sm font-bold text-blue-600 bg-blue-50 dark:bg-blue-500/10 dark:text-blue-400 px-3 py-1 rounded inline-block">
                                {{ mb_substr($wash->agent->name, 0, 15) }}...
                            </div>
                        @else
                            <div class="text-sm font-bold text-gray-400 bg-gray-50 dark:bg-white/5 px-3 py-1 rounded inline-block border border-dashed border-gray-200">
                                غير مُكلف
                            </div>
                        @endif
                    </td>

                    <!-- Car & Wash Type -->
                    <td class="px-6 py-4 text-right">
                        <div class="flex flex-col gap-1.5">
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold text-sky-600 dark:text-sky-400 bg-sky-50 dark:bg-sky-500/10 px-2 rounded w-max border border-sky-100">
                                <i class="fa-solid fa-car-side"></i> 
                                {{ $wash->car_size == 'small' ? 'سيارة صغيرة' : ($wash->car_size == 'large' ? 'سيارة عائلية/كبيرة' : $wash->car_size) }}
                            </span>
                            <span class="text-[11px] font-bold text-gray-500 truncate w-32 border border-gray-100 bg-gray-50 px-2 rounded">
                                <i class="fa-solid fa-droplet text-gray-400"></i> {{ str_replace('_', ' + ', $wash->wash_type) }}
                            </span>
                        </div>
                    </td>

                    <!-- Cost -->
                    <td class="px-6 py-4 text-right">
                        <div class="font-black text-gray-800 dark:text-gray-200 flex items-center gap-1">
                            {{ number_format($wash->cost ?? 0, 2) }} <span class="text-[10px] font-bold text-gray-400">SAR</span>
                        </div>
                        <div class="text-[10px] font-bold {{ $wash->payment_status == 'paid' ? 'text-emerald-500' : 'text-amber-500' }} mt-1">
                           {{ $wash->payment_method }} • {{ $wash->payment_status == 'paid' ? 'مدفوع' : 'غير مدفوع' }}
                        </div>
                    </td>

                    <!-- Status -->
                    <td class="px-6 py-4 text-right">
                        @if($wash->status == 'pending')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded bg-amber-50 text-amber-600 text-xs font-bold border border-amber-100">
                                <i class="fa-solid fa-clock text-[10px]"></i> {{ __('New (Awaiting Confirmation)') }}
                            </span>
                        @elseif($wash->status == 'confirmed')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded bg-blue-50 text-blue-600 text-xs font-bold border border-blue-100">
                                <i class="fa-solid fa-clipboard-check text-[10px]"></i> {{ __('Confirmed') }}
                            </span>
                        @elseif($wash->status == 'completed')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded bg-emerald-50 text-emerald-600 text-xs font-bold border border-emerald-100">
                                <i class="fa-solid fa-check-double text-[10px]"></i> {{ __('Wash Completed') }}
                            </span>
                        @elseif($wash->status == 'cancelled')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded bg-red-50 text-red-600 text-xs font-bold border border-red-100">
                                <i class="fa-solid fa-xmark text-[10px]"></i> {{ __('Cancelled') }}
                            </span>
                        @endif
                    </td>

                    <!-- Action -->
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.car_washes.show', $wash->id) }}" class="inline-flex items-center justify-center gap-2 px-3 h-8 rounded-lg bg-gray-50 hover:bg-sky-600 dark:bg-white/10 text-gray-600 hover:text-white dark:text-gray-300 font-bold text-xs transition border border-gray-200 dark:border-white/5 hover:border-transparent">
                            {{ __('Details') }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <div class="w-16 h-16 bg-gray-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-3">
                                <i class="fa-solid fa-droplet text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-base font-bold text-gray-600 dark:text-gray-300">{{ __('No car wash bookings found') }}</h3>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Check filters or try another search') }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($carWashes->hasPages())
    <div class="p-4 border-t border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5">
        {{ $carWashes->links() }}
    </div>
    @endif

</div>
@endsection
