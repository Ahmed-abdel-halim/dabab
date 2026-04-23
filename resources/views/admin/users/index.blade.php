@extends('admin.layouts.app')

@section('title', __('Users Management'))

@section('content')

    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">{{ __('Users') }}</h2>
            <p class="text-sm font-medium text-gray-500">{{ __('Manage agent and customer accounts directly') }}</p>
        </div>
    </div>

    <!-- Main Card for Table and Filters -->
    <div class="container-3d">

        <!-- Filters Header -->
        <div class="p-6 border-b border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5">
            <form action="{{ route('admin.users.index') }}" method="GET" x-data x-ref="filterForm">
                <div class="flex flex-col xl:flex-row gap-4 items-center justify-between">

                    <!-- Search Box -->
                    <div class="relative w-full xl:w-1/3">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="{{ __('Search for name, email, or mobile...') }}"
                            class="w-full bg-white dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl py-2.5 px-10 text-sm font-medium text-gray-700 dark:text-gray-200 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition">
                        <i class="fa-solid fa-search absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        @if(request('search'))
                            <a href="{{ route('admin.users.index', request()->except('search', 'page')) }}"
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500"><i
                                    class="fa-solid fa-times-circle"></i></a>
                        @endif
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-col sm:flex-row w-full xl:w-auto gap-4 items-center">


                        <!-- Sort -->
                        <div class="w-full sm:w-48 relative">
                            <select name="sort" @change="$refs.filterForm.submit()"
                                class="w-full appearance-none bg-white dark:bg-black/30 border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 text-sm font-bold rounded-xl px-4 py-2.5 pr-10 focus:outline-none focus:border-amber-500 transition">
                                <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>{{ __('Latest registered') }}</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>{{ __('Oldest registered') }}
                                </option>
                                <option value="highest_balance" {{ request('sort') == 'highest_balance' ? 'selected' : '' }}>
                                    {{ __('Highest balance') }}</option>
                            </select>
                            <i
                                class="fa-solid fa-sort absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
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
                    <tr
                        class="bg-gray-50/50 dark:bg-white/5 text-gray-500 dark:text-gray-400 text-sm border-b border-gray-100 dark:border-white/5">
                        <th class="px-6 py-4 font-bold">{{ __('Account Info') }}</th>
                        <th class="px-6 py-4 font-bold">{{ __('Contact') }}</th>
                        <th class="px-6 py-4 font-bold">{{ __('Balance') }}</th>
                        <th class="px-6 py-4 font-bold">{{ __('Joined Date') }}</th>
                        <th class="px-6 py-4 font-bold text-center">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-white/5 transition duration-150">
                            <!-- User -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3 w-56">
                                    <div
                                        class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg shrink-0
                                                {{ $user->role == 'delivery_agent' ? 'bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-400' : 'bg-amber-100 text-amber-600 dark:bg-sky-500/20 dark:text-sky-400' }}">
                                        {{ mb_substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="font-bold text-gray-800 dark:text-white truncate">{{ $user->name }}</div>
                                        @if($user->role == 'delivery_agent')
                                            <div class="text-[11px] font-bold text-blue-500 mt-0.5">{{ __('Delivery Agent') }}</div>
                                        @else
                                            <div class="text-[11px] font-bold text-gray-500 mt-0.5">{{ __('App User') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Contact -->
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-600 dark:text-gray-300 w-48 truncate mb-1"
                                    title="{{ $user->email }}">
                                    {{ $user->email }}
                                </div>
                                <div class="text-xs font-bold text-gray-500 dark:text-gray-400" dir="ltr">
                                    {{ $user->phone ?? '---' }}
                                </div>
                            </td>

                            <!-- Wallet -->
                            <td class="px-6 py-4">
                                <div class="font-black text-emerald-500 dark:text-emerald-400">
                                    {{ number_format($user->wallet_balance ?? 0, 2) }} <span
                                        class="text-xs font-bold opacity-70">SAR</span>
                                </div>
                            </td>

                            <!-- Date -->
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-700 dark:text-gray-300" dir="ltr">
                                    {{ $user->created_at->format('Y-m-d') }}
                                </div>
                            </td>

                            <!-- Action -->
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.users.show', $user->id) }}"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 hover:bg-amber-500 dark:bg-white/10 dark:hover:bg-sky-500 text-gray-500 hover:text-white dark:text-gray-300 transition">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-16 h-16 bg-gray-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-3">
                                        <i class="fa-solid fa-search text-2xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-600 dark:text-gray-300">لم يتم العثور على نتائج
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-1">تأكد من إعدادات البحث والفلتر</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5">
                {{ $users->links() }}
            </div>
        @endif

    </div>
@endsection