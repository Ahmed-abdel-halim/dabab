@extends('admin.layouts.app')

@section('title', __('Store Categories and Prices'))

@section('content')

    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-1">{{ __('Store Categories') }}</h2>
            <p class="text-sm font-medium text-gray-500">{{ __('Control fixed delivery prices for each category and activation') }}</p>
        </div>
        <button @click="$dispatch('open-modal', 'add-category')"
            class="bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 px-6 rounded-2xl transition shadow-lg shadow-amber-500/20">
            <i class="fa-solid fa-plus ml-2"></i> {{ __('Add New Category') }}
        </button>
    </div>

    @if(session('success'))
        <div
            class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-xl font-bold flex items-center gap-3">
            <i class="fa-solid fa-circle-check text-xl"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($categories as $category)
            <div
                class="bg-white dark:bg-[#161b22] border border-gray-100 dark:border-white/5 rounded-3xl p-6 shadow-sm hover:shadow-md transition">
                <form action="{{ route('admin.settings.categories.update', $category->id) }}" method="POST">
                    @csrf
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400 text-xl shadow-sm">
                                <i class="fa-solid fa-{{ $category->icon ?: 'box' }}"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-800 dark:text-white">{{ app()->getLocale() == 'ar' ? $category->name_ar : $category->name_en }}</h3>
                                {{-- Hidden other language name as requested --}}
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $category->is_active ? 'checked' : '' }} onchange="this.form.submit()">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-amber-500">
                            </div>
                        </label>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-2 px-1">
                                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ __('Fixed Delivery Price') }}</label>
                                <span class="text-[10px] font-black text-amber-500 uppercase">{{ __('SAR') }}</span>
                            </div>
                            <div class="relative">
                                <input type="number" name="fixed_price" step="0.5" value="{{ $category->fixed_price }}"
                                    class="w-full bg-gray-50 dark:bg-black/20 border border-gray-100 dark:border-white/10 rounded-xl py-4 px-5 text-base font-black text-gray-800 dark:text-white focus:outline-none focus:border-amber-500 transition shadow-inner">
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full bg-gray-800 dark:bg-white/5 dark:hover:bg-white/10 text-white font-bold py-3 px-4 rounded-xl transition text-sm">
                            {{ __('Save Changes') }}
                        </button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>

    <!-- Add Category Modal -->
    <x-admin-modal name="add-category" title="{{ __('Add New Store Category') }}">
        <form action="{{ route('admin.settings.categories.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-right">
                <div class="space-y-2">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest px-1">{{ __('Category Name (Arabic)') }}</label>
                    <input type="text" name="name_ar" placeholder="مثال: طلبات المطاعم" required
                        class="w-full bg-gray-50 dark:bg-black/40 border border-gray-200 dark:border-white/5 rounded-2xl py-4 px-5 text-sm font-bold text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition shadow-sm">
                </div>
                <div class="space-y-2" dir="ltr">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest px-1">{{ __('Category Name (English)') }}</label>
                    <input type="text" name="name_en" placeholder="Example: Restaurant Orders" required
                        class="w-full bg-gray-50 dark:bg-black/40 border border-gray-200 dark:border-white/5 rounded-2xl py-4 px-5 text-sm font-bold text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition shadow-sm text-left">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-right">
                <div class="space-y-2">
                    <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest px-1">{{ __('Category Icon (FontAwesome)') }}</label>
                    <div class="relative">
                        <input type="text" name="icon" placeholder="مثال: utensil-spoon" required
                            class="w-full bg-gray-50 dark:bg-black/40 border border-gray-200 dark:border-white/5 rounded-2xl py-4 px-5 text-sm font-bold text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition shadow-sm">
                    </div>
                    <p class="text-[10px] text-gray-400 font-medium mt-1">{{ __('Enter icon name without fa- (e.g., pharmacy)') }}</p>
                </div>
                    <div class="flex items-center justify-between mb-2 px-1">
                        <label class="text-[11px] font-black text-gray-400 uppercase tracking-widest px-1">{{ __('Fixed Delivery Price') }}</label>
                        <span class="text-[10px] font-black text-amber-500 uppercase">{{ __('SAR') }}</span>
                    </div>
                    <div class="relative">
                        <input type="number" name="fixed_price" step="0.5" placeholder="0.00" required
                            class="w-full bg-gray-50 dark:bg-black/40 border border-gray-200 dark:border-white/5 rounded-2xl py-4 px-5 text-sm font-bold text-gray-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition shadow-sm">
                    </div>
            </div>

            <button type="submit"
                class="w-full bg-amber-500 hover:bg-amber-600 text-white font-black py-4 rounded-2xl mt-4 shadow-xl shadow-amber-500/30 transition-all duration-300 transform hover:scale-[1.02] active:scale-95 text-lg">
                {{ __('Create Category and Save Data') }}
            </button>
        </form>
    </x-admin-modal>

@endsection