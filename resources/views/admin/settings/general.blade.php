@extends('admin.layouts.app')

@section('title', __('General Settings'))

@section('content')

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">{{ __('System Settings') }}</h2>
    <p class="text-sm font-medium text-gray-500">{{ __('Control system constants and base platform prices') }}</p>
</div>

@if(session('success'))
<div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-xl font-bold flex items-center gap-3">
    <i class="fa-solid fa-circle-check text-xl"></i>
    {{ session('success') }}
</div>
@endif

<form action="{{ route('admin.settings.general.update') }}" method="POST">
    @csrf
    
    <div class="space-y-8">
        @foreach($settings as $group => $items)
        <div class="bg-white dark:bg-[#161b22] border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 bg-gray-50/50 dark:bg-white/5 border-b border-gray-100 dark:border-white/5">
                <h3 class="font-bold text-gray-800 dark:text-white uppercase tracking-widest text-xs">
                    {{ $group == 'prices' ? __('Base Service Prices') : __('Settings') . ' ' . $group }}
                </h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($items as $item)
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2">{{ $item->display_name }}</label>
                    <div class="relative">
                        <input type="text" name="{{ $item->key }}" value="{{ $item->value }}" 
                               class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl py-3 px-4 text-sm font-bold text-gray-800 dark:text-white focus:outline-none focus:border-amber-500 transition">
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="flex justify-end">
            <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-4 px-10 rounded-2xl transition shadow-lg shadow-amber-500/20">
                {{ __('Save All Settings') }}
            </button>
        </div>
    </div>
</form>

@endsection
