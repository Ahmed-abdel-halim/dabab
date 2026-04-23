@extends('admin.layouts.app')

@section('title', __('Informational Pages'))

@section('content')

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">{{ __('Informational Pages Management') }}</h2>
    <p class="text-sm font-medium text-gray-500">{{ __('Edit terms and conditions and privacy policy texts') }}</p>
</div>

@if(session('success'))
<div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-xl font-bold flex items-center gap-3">
    <i class="fa-solid fa-circle-check text-xl"></i>
    {{ session('success') }}
</div>
@endif

<div class="bg-white dark:bg-[#161b22] border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-right whitespace-nowrap">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-white/5 text-gray-500 dark:text-gray-400 text-sm border-b border-gray-100 dark:border-white/5">
                    <th class="px-6 py-4 font-bold">{{ __('Page Name') }}</th>
                    <th class="px-6 py-4 font-bold">{{ __('Last Update') }}</th>
                    <th class="px-6 py-4 font-bold text-center">{{ __('Action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                @foreach($pages as $page)
                <tr class="hover:bg-gray-50/80 dark:hover:bg-white/5 transition duration-150">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-800 dark:text-white">{{ $page->title_ar ?: $page->slug }}</div>
                        <div class="text-[10px] text-gray-400 font-black" dir="ltr">{{ $page->slug }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-500" dir="ltr">
                        {{ $page->updated_at->format('Y-m-d H:i') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.settings.pages.edit', $page->id) }}" class="inline-flex items-center justify-center gap-2 px-4 h-9 rounded-xl bg-gray-50 hover:bg-amber-600 dark:bg-white/10 text-gray-600 hover:text-white dark:text-gray-300 font-bold text-xs transition border border-gray-200 dark:border-white/5 hover:border-transparent">
                            <i class="fa-solid fa-pen-to-square"></i> {{ __('Edit Content') }}
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
