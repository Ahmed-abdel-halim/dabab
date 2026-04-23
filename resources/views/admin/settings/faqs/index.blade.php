@extends('admin.layouts.app')

@section('title', __('FAQs'))

@section('content')

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">{{ __('FAQs') }}</h2>
        <p class="text-sm font-medium text-gray-500">{{ __('Manage the list of frequently asked questions for users') }}</p>
    </div>
    <button @click="$dispatch('open-modal', 'add-faq')" class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-6 rounded-2xl transition shadow-lg shadow-amber-500/20">
        <i class="fa-solid fa-plus ml-2"></i> {{ __('Add New FAQ') }}
    </button>
</div>

@if(session('success'))
<div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-xl font-bold flex items-center gap-3">
    <i class="fa-solid fa-circle-check text-xl"></i>
    {{ session('success') }}
</div>
@endif

<div class="space-y-4">
    @forelse($faqs as $faq)
    <div class="bg-white dark:bg-[#161b22] border border-gray-100 dark:border-white/5 rounded-3xl p-6 shadow-sm overflow-hidden" x-data="{ open: false }">
        <div class="flex items-center justify-between">
            <div class="flex-1 cursor-pointer" @click="open = !open">
                <h3 class="font-bold text-gray-800 dark:text-white flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs">{{ __('Q') }}</span>
                    {{ app()->getLocale() == 'ar' ? $faq->question_ar : $faq->question_en }}
                </h3>
            </div>
            <div class="flex items-center gap-2">
                <button @click="$dispatch('open-modal', 'edit-faq-{{ $faq->id }}')" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-blue-50 text-gray-400 hover:text-blue-600 transition flex items-center justify-center">
                    <i class="fa-solid fa-pen-to-square"></i>
                </button>
                <form action="{{ route('admin.settings.faqs.destroy', $faq->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete?') }}')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-9 h-9 rounded-xl bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-600 transition flex items-center justify-center">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </form>
                <button @click="open = !open" class="w-9 h-9 rounded-xl bg-gray-50 text-gray-400 transition flex items-center justify-center" :class="open ? 'rotate-180' : ''">
                    <i class="fa-solid fa-chevron-down"></i>
                </button>
            </div>
        </div>
        
        <div x-show="open" x-collapse>
            <div class="mt-4 pt-4 border-t border-gray-50 dark:border-white/5 text-sm font-medium text-gray-600 dark:text-gray-300 leading-relaxed flex items-start gap-3">
                <span class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs shrink-0">{{ __('A') }}</span>
                {{ app()->getLocale() == 'ar' ? $faq->answer_ar : $faq->answer_en }}
            </div>

        </div>

        <!-- Edit Modal -->
        <x-admin-modal name="edit-faq-{{ $faq->id }}" title="{{ __('Edit FAQ') }}">
            <form action="{{ route('admin.settings.faqs.update', $faq->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-right">
                    <div class="space-y-4">
                        <h4 class="text-xs font-black text-amber-600 uppercase">{{ __('Arabic') }}</h4>
                        <input type="text" name="question_ar" value="{{ $faq->question_ar }}" placeholder="{{ __('Question (Arabic)') }}" class="w-full bg-gray-50 border border-gray-100 rounded-xl py-3 px-4 text-sm font-bold focus:outline-none">
                        <textarea name="answer_ar" rows="4" placeholder="{{ __('Answer (Arabic)') }}" class="w-full bg-gray-50 border border-gray-100 rounded-xl py-3 px-4 text-sm font-bold focus:outline-none">{{ $faq->answer_ar }}</textarea>
                    </div>
                    <div class="space-y-4" dir="ltr">
                        <h4 class="text-xs font-black text-amber-600 uppercase">English</h4>
                        <input type="text" name="question_en" value="{{ $faq->question_en }}" placeholder="Question in English" class="w-full bg-gray-50 border border-gray-100 rounded-xl py-3 px-4 text-sm font-bold focus:outline-none text-left">
                        <textarea name="answer_en" rows="4" placeholder="Answer in English" class="w-full bg-gray-50 border border-gray-100 rounded-xl py-3 px-4 text-sm font-bold focus:outline-none text-left">{{ $faq->answer_en }}</textarea>
                    </div>
                </div>
                <button type="submit" class="w-full bg-amber-500 text-white font-bold py-4 rounded-xl mt-4">{{ __('Update Data') }}</button>
            </form>
        </x-admin-modal>
    </div>
    @empty
    <div class="p-16 text-center bg-white dark:bg-[#161b22] border border-dashed border-gray-200 rounded-3xl">
        <i class="fa-solid fa-circle-question text-4xl text-gray-200 mb-4"></i>
        <p class="text-gray-500 font-bold">{{ __('No FAQs added yet') }}</p>
    </div>
    @endforelse
</div>

<!-- Add Modal -->
<x-admin-modal name="add-faq" title="{{ __('Add New FAQ') }}">
    <form action="{{ route('admin.settings.faqs.store') }}" method="POST" class="space-y-4">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-right">
            <div class="space-y-4">
                <h4 class="text-xs font-black text-amber-600 uppercase">{{ __('Arabic') }}</h4>
                <input type="text" name="question_ar" placeholder="{{ __('Question (Arabic)') }}" class="w-full bg-gray-50 border border-gray-100 rounded-xl py-3 px-4 text-sm font-bold focus:outline-none">
                <textarea name="answer_ar" rows="4" placeholder="{{ __('Answer (Arabic)') }}" class="w-full bg-gray-50 border border-gray-100 rounded-xl py-3 px-4 text-sm font-bold focus:outline-none"></textarea>
            </div>
            <div class="space-y-4" dir="ltr">
                <h4 class="text-xs font-black text-amber-600 uppercase">English</h4>
                <input type="text" name="question_en" placeholder="Question in English" class="w-full bg-gray-50 border border-gray-100 rounded-xl py-3 px-4 text-sm font-bold focus:outline-none text-left">
                <textarea name="answer_en" rows="4" placeholder="Answer in English" class="w-full bg-gray-50 border border-gray-100 rounded-xl py-3 px-4 text-sm font-bold focus:outline-none text-left"></textarea>
            </div>
        </div>
        <button type="submit" class="w-full bg-amber-500 text-white font-bold py-4 rounded-xl mt-4">{{ __('Add Question') }}</button>
    </form>
</x-admin-modal>

@endsection
