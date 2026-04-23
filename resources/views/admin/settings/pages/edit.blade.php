@extends('admin.layouts.app')

@section('title', __('Edit Page') . ': ' . ($page->title_ar ?: $page->slug))

@section('content')

<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.settings.pages.index') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-amber-600 transition">
        <i class="fa-solid fa-arrow-right"></i>
    </a>
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">{{ __('Edit Content') }}</h2>
        <p class="text-sm font-medium text-gray-500" dir="ltr">{{ $page->slug }}</p>
    </div>
</div>

<form action="{{ route('admin.settings.pages.update', $page->id) }}" method="POST">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Arabic Content -->
        <div class="space-y-4">
            <h4 class="font-black text-amber-600 flex items-center gap-2"><i class="fa-solid fa-language"></i> {{ __('Arabic Content') }}</h4>
            <textarea name="content_ar" rows="15" class="w-full bg-white dark:bg-[#161b22] border border-gray-200 dark:border-white/10 rounded-3xl p-6 text-sm font-medium text-gray-700 dark:text-gray-200 focus:outline-none focus:border-amber-500 shadow-sm leading-relaxed">{{ $page->content_ar }}</textarea>
        </div>

        <!-- English Content -->
        <div class="space-y-4">
            <h4 class="font-black text-amber-600 flex items-center gap-2"><i class="fa-solid fa-language"></i> English Content</h4>
            <textarea name="content_en" rows="15" dir="ltr" class="w-full bg-white dark:bg-[#161b22] border border-gray-200 dark:border-white/10 rounded-3xl p-6 text-sm font-medium text-gray-700 dark:text-gray-200 focus:outline-none focus:border-amber-500 shadow-sm leading-relaxed text-left">{{ $page->content_en }}</textarea>
        </div>

    </div>

    <div class="mt-8 flex justify-end">
        <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-4 px-12 rounded-2xl transition shadow-lg shadow-amber-500/20 text-lg">
            {{ __('Update Page') }}
        </button>
    </div>
</form>

@endsection
