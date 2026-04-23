@extends('admin.layouts.app')

@section('title', __('Agents Management'))

@section('content')

    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">{{ __('Agent Requests and Subscriptions') }}</h2>
            <p class="text-sm font-medium text-gray-500">{{ __('Review and approve registered agent accounts') }}</p>
        </div>
    </div>

    <!-- Main Card -->
    <div class="container-3d">

        <!-- Filters Header -->
        <div class="p-6 border-b border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5">
            <form action="{{ route('admin.agents.index') }}" method="GET" x-data x-ref="filterForm">
                <div class="flex flex-col xl:flex-row gap-4 items-center justify-between">

                    <!-- Search Box -->
                    <div class="relative w-full xl:w-1/3">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="{{ __('Search by name, phone, or national ID...') }}"
                            class="w-full bg-white dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl py-2.5 px-10 text-sm font-medium text-gray-700 dark:text-gray-200 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                        <i class="fa-solid fa-search absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        @if(request('search'))
                            <a href="{{ route('admin.agents.index', request()->except('search', 'page')) }}"
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-500"><i
                                    class="fa-solid fa-times-circle"></i></a>
                        @endif
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-col sm:flex-row w-full xl:w-auto gap-4 items-center">
                        <!-- Status Tabs -->
                        <div
                            class="flex bg-gray-100 dark:bg-black/30 p-1 rounded-xl w-full sm:w-auto overflow-x-auto hide-scrollbar">
                            <label class="cursor-pointer relative flex-1 text-center min-w-max">
                                <input type="radio" name="status" value="all" class="peer sr-only"
                                    @change="$refs.filterForm.submit()" {{ request('status', 'all') == 'all' ? 'checked' : '' }}>
                                <div
                                    class="px-4 py-2 rounded-lg text-sm font-bold text-gray-500 peer-checked:bg-white dark:peer-checked:bg-[#161b22] peer-checked:text-blue-600 dark:peer-checked:text-blue-400 peer-checked:shadow-sm transition">
                                    {{ __('All') }}</div>
                            </label>
                            <label class="cursor-pointer relative flex-1 text-center min-w-max">
                                <input type="radio" name="status" value="pending" class="peer sr-only"
                                    @change="$refs.filterForm.submit()" {{ request('status') == 'pending' ? 'checked' : '' }}>
                                <div
                                    class="px-4 py-2 rounded-lg text-sm font-bold text-gray-500 peer-checked:bg-white dark:peer-checked:bg-[#161b22] peer-checked:text-amber-600 dark:peer-checked:text-amber-400 peer-checked:shadow-sm transition">
                                    {{ __('Pending Review') }}</div>
                            </label>
                            <label class="cursor-pointer relative flex-1 text-center min-w-max">
                                <input type="radio" name="status" value="active" class="peer sr-only"
                                    @change="$refs.filterForm.submit()" {{ request('status') == 'active' ? 'checked' : '' }}>
                                <div
                                    class="px-4 py-2 rounded-lg text-sm font-bold text-gray-500 peer-checked:bg-white dark:peer-checked:bg-[#161b22] peer-checked:text-emerald-600 dark:peer-checked:text-emerald-400 peer-checked:shadow-sm transition">
                                    {{ __('Active') }}</div>
                            </label>
                            <label class="cursor-pointer relative flex-1 text-center min-w-max">
                                <input type="radio" name="status" value="rejected" class="peer sr-only"
                                    @change="$refs.filterForm.submit()" {{ request('status') == 'rejected' ? 'checked' : '' }}>
                                <div
                                    class="px-4 py-2 rounded-lg text-sm font-bold text-gray-500 peer-checked:bg-white dark:peer-checked:bg-[#161b22] peer-checked:text-red-600 dark:peer-checked:text-red-400 peer-checked:shadow-sm transition">
                                    {{ __('Rejected') }}</div>
                            </label>
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
                        <th class="px-6 py-4 font-bold text-right" style="text-align: right;">{{ __('Agent') }}</th>
                        <th class="px-6 py-4 font-bold text-right" style="text-align: right;">{{ __('Phone Number') }}</th>
                        <th class="px-6 py-4 font-bold text-right" style="text-align: right;">{{ __('National ID') }}</th>
                        <th class="px-6 py-4 font-bold text-right" style="text-align: right;">{{ __('Nationality') }}</th>
                        <th class="px-6 py-4 font-bold text-right" style="text-align: right;">{{ __('Service Provided') }}</th>
                        <th class="px-6 py-4 font-bold text-right" style="text-align: right;">{{ __('Request Status') }}</th>
                        <th class="px-6 py-4 font-bold text-center">{{ __('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                    @forelse($agents as $agent)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-white/5 transition duration-150">

                            <!-- Agent Name -->
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm shrink-0 
                                                {{ $agent->status == 'active' ? 'bg-emerald-100 text-emerald-600' : 'bg-blue-100 text-blue-600' }}">
                                        {{ mb_substr($agent->user->name ?? __('m'), 0, 1) }}
                                    </div>
                                    <div class="font-bold text-gray-800 dark:text-white">{{ $agent->user->name ?? __('Not available') }}
                                    </div>
                                </div>
                            </td>

                            <!-- Phone Number -->
                            <td class="px-6 py-4 text-right">
                                <div class="text-sm font-bold text-gray-700 dark:text-gray-300" dir="ltr"
                                    style="text-align: right; direction: ltr; display: inline-block;">
                                    {{ $agent->user->phone ?? '---' }}
                                </div>
                            </td>

                            <!-- National ID -->
                            <td class="px-6 py-4 text-right">
                                <div class="text-sm font-bold text-gray-700 dark:text-gray-300 tracking-wider" dir="ltr"
                                    style="text-align: right; direction: ltr; display: inline-block;">
                                    {{ $agent->national_id_number ?? '---' }}
                                </div>
                            </td>

                            <!-- Nationality -->
                            <td class="px-6 py-4 text-right">
                                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                    {{ $agent->nationality ?? __('Not specified') }}
                                </div>
                            </td>

                            <!-- Service -->
                            <td class="px-6 py-4 text-right">
                                <div
                                    class="text-sm font-bold text-indigo-600 bg-indigo-50 dark:bg-indigo-500/10 dark:text-indigo-400 px-3 py-1 rounded inline-block">
                                    {{ Str::replace('_', ' ', $agent->working_service ?? __('Not specified')) }}
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4 text-right">
                                @if($agent->status == 'pending')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-600 text-xs font-bold">
                                        <i class="fa-solid fa-clock text-[10px]"></i> {{ __('Pending Review') }}
                                    </span>
                                @elseif($agent->status == 'active')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-bold">
                                        <i class="fa-solid fa-check text-[10px]"></i> {{ __('Active') }}
                                    </span>
                                @elseif($agent->status == 'rejected')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-50 text-red-600 text-xs font-bold">
                                        <i class="fa-solid fa-xmark text-[10px]"></i> {{ __('Rejected') }}
                                    </span>
                                @elseif($agent->status == 'suspended')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-bold">
                                        <i class="fa-solid fa-ban text-[10px]"></i> {{ __('Suspended') }}
                                    </span>
                                @endif
                            </td>

                            <!-- Action -->
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.agents.show', $agent->id) }}"
                                    class="inline-flex items-center justify-center gap-2 px-3 h-8 rounded-lg bg-gray-100 hover:bg-blue-600 dark:bg-white/10 text-gray-600 hover:text-white dark:text-gray-300 font-bold text-xs transition">
                                    <i class="fa-solid fa-file-invoice text-xs"></i> {{ __('Review') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-16 h-16 bg-gray-50 dark:bg-white/5 rounded-full flex items-center justify-center mb-3">
                                        <i class="fa-solid fa-motorcycle text-2xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-base font-bold text-gray-600 dark:text-gray-300">{{ __('No agent data found') }}
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-1">{{ __('Check filters or try another section') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($agents->hasPages())
            <div class="p-4 border-t border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5">
                {{ $agents->links() }}
            </div>
        @endif

    </div>
@endsection