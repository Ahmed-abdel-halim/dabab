@extends('admin.layouts.app')

@section('title', __('Car Wash Settings'))

@section('content')

    @if(session('success'))
        <div
            class="bg-green-50 dark:bg-green-500/10 text-green-500 p-4 rounded-2xl mb-6 font-bold flex items-center gap-3 border border-green-100 dark:border-green-500/20 scale-up">
            <i class="fa-solid fa-check-circle text-xl"></i> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8" x-data="{ showAddModal: false }">

        <!-- Prices Section -->
        <div
            class="bg-white/80 dark:bg-[#161b22]/80 backdrop-blur-md border border-gray-100 dark:border-white/5 p-6 md:p-8 rounded-3xl shadow-sm scale-up">
            <h3 class="text-xl font-bold mb-6 text-gray-800 dark:text-white flex items-center gap-3">
                <div
                    class="flex items-center justify-center w-12 h-12 bg-amber-50 dark:bg-sky-500/10 text-amber-500 dark:text-sky-500 rounded-2xl shadow-sm">
                    <i class="fa-solid fa-money-bill-wave text-xl"></i>
                </div>
                {{ __('Service Prices') }}
            </h3>

            <form action="{{ route('admin.settings.carwash.prices') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($prices as $setting)
                        <div>
                            <label
                                class="block text-xs font-bold text-gray-500 mb-2">{{ $setting->display_name ?? $setting->key }}</label>
                            <div class="relative">
                                <input type="number" name="{{ $setting->key }}" value="{{ $setting->value }}"
                                    class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3 pb-3 pr-16 text-gray-800 dark:text-white focus:outline-none focus:border-amber-500 dark:focus:border-sky-500 font-bold transition">
                                <span
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-amber-500 dark:text-sky-500 font-bold text-sm bg-amber-50 dark:bg-sky-500/10 px-2 py-1 rounded-lg">SAR</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4 flex justify-end">
                    <button type="submit"
                        class="bg-amber-500 dark:bg-sky-500 hover:opacity-90 text-white px-8 py-3 rounded-xl font-bold transition shadow-md flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> {{ __('Save Prices') }}
                    </button>
                </div>
            </form>
        </div>

        <!-- Periods Section -->
        <div class="bg-white/80 dark:bg-[#161b22]/80 backdrop-blur-md border border-gray-100 dark:border-white/5 rounded-3xl shadow-sm overflow-hidden scale-up"
            style="animation-delay: 0.1s;">
            <div
                class="p-6 md:p-8 border-b border-gray-100 dark:border-white/5 flex gap-4 justify-between items-center bg-gray-50 dark:bg-black/20">
                <h3 class="text-xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                    <div
                        class="flex items-center justify-center w-12 h-12 bg-amber-50 dark:bg-sky-500/10 text-amber-500 dark:text-sky-500 rounded-2xl shadow-sm">
                        <i class="fa-solid fa-clock text-xl"></i>
                    </div>
                    {{ __('Available Working Hours') }}
                </h3>
                <button @click="showAddModal = true"
                    class="bg-amber-500 dark:bg-sky-500 hover:opacity-90 text-white px-6 py-3 rounded-2xl text-sm font-bold transition flex items-center gap-2 shadow-[0_4px_12px_rgba(245,158,11,0.3)] dark:shadow-[0_4px_12px_rgba(14,165,233,0.3)] shrink-0 z-10 relative">
                    <i class="fa-solid fa-plus text-lg"></i> <span class="hidden md:inline">{{ __('Add New Period') }}</span>
                </button>
            </div>

            <div class="p-6 md:p-8 space-y-4">
                @foreach($periods as $period)
                    <div
                        class="flex items-center justify-between p-4 rounded-2xl border {{ $period->is_active ? 'border-amber-100 dark:border-sky-500/30 bg-amber-50/50 dark:bg-sky-500/5' : 'border-gray-200 dark:border-white/10 bg-gray-50/50 dark:bg-white/5 opacity-75 grayscale' }} transition duration-300 hover:scale-[1.01]">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-xl {{ $period->is_active ? 'bg-amber-100 text-amber-600 dark:bg-sky-500/20 dark:text-sky-500' : 'bg-gray-200 text-gray-500 dark:bg-black/50 dark:text-gray-400' }} flex items-center justify-center text-xl font-bold shadow-inner">
                                <i class="fa-solid {{ $period->period_type == 'evening' ? 'fa-moon' : 'fa-sun' }}"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 dark:text-white text-md">
                                    {{ __('messages.car_wash.periods.' . $period->period_key) }} <span
                                        class="text-xs text-gray-400 font-normal ml-1">({{ $period->period_key }})</span>
                                </h4>
                                <p class="text-sm font-black text-gray-500 mt-1 drop-shadow-sm" dir="ltr">
                                    {{ $period->start_time }} - {{ $period->end_time }}
                                </p>
                            </div>
                        </div>

                        <form action="{{ route('admin.settings.carwash.periods.toggle', $period->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="w-14 h-8 rounded-full flex items-center transition duration-300 {{ $period->is_active ? 'bg-amber-500 dark:bg-sky-500 justify-end shadow-md shadow-amber-500/20' : 'bg-gray-300 dark:bg-gray-700 justify-start' }} p-1 cursor-pointer ring-2 ring-transparent {{ $period->is_active ? 'hover:ring-amber-200 dark:hover:ring-sky-200' : 'hover:ring-gray-300' }}">
                                <div class="bg-white w-6 h-6 rounded-full shadow-sm transform transition duration-300"></div>
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Modal for adding period -->
        <template x-teleport="body">
            <div x-show="showAddModal" x-cloak
                class="fixed inset-0 z-[100] overflow-y-auto bg-black/60 backdrop-blur-sm transition-opacity">
                <div class="flex min-h-full items-center justify-center p-4">
                    <div @click.away="showAddModal = false"
                        class="bg-white dark:bg-[#161b22] border border-gray-200 dark:border-white/10 w-full max-w-lg rounded-3xl p-6 md:p-8 shadow-2xl scale-up">

                        <div
                            class="flex justify-between items-center mb-6 border-b border-gray-100 dark:border-white/5 pb-4">
                            <h3 class="text-xl font-bold dark:text-white flex items-center gap-2">
                                <i class="fa-solid fa-plus-circle text-blue-500"></i> {{ __('Add Work Period') }}
                            </h3>
                            <button type="button" @click="showAddModal = false"
                                class="text-gray-400 hover:text-red-500 transition w-8 h-8 flex items-center justify-center rounded-xl hover:bg-red-50 dark:hover:bg-red-500/10 bg-gray-50 dark:bg-white/5"><i
                                    class="fa-solid fa-xmark text-lg"></i></button>
                        </div>

                        <form action="{{ route('admin.settings.carwash.periods.store') }}" method="POST"
                            class="space-y-5 text-sm font-bold">
                            @csrf
                            <div>
                                <label class="block mb-2 text-gray-500">{{ __('Programmatic Key (indicates time in English, e.g., morning_time)') }}</label>
                                <input type="text" name="period_key" required
                                    class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3 text-gray-800 dark:text-white focus:outline-none focus:border-amber-500 dark:focus:border-sky-500 transition">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block mb-2 text-gray-500">{{ __('Start Time (e.g., 08:00)') }}</label>
                                    <input type="time" name="start_time" required
                                        class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3 text-gray-800 dark:text-white focus:border-amber-500 dark:focus:border-sky-500 font-sans transition"
                                        dir="ltr">
                                </div>
                                <div>
                                    <label class="block mb-2 text-gray-500">{{ __('End Time (e.g., 12:00)') }}</label>
                                    <input type="time" name="end_time" required
                                        class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3 text-gray-800 dark:text-white focus:border-amber-500 dark:focus:border-sky-500 font-sans transition"
                                        dir="ltr">
                                </div>
                            </div>
                            <div>
                                <label class="block mb-2 text-gray-500">{{ __('Text shown to users (e.g., 08:00 - 12:00)') }}</label>
                                <input type="text" name="time_range" placeholder="08:00 - 12:00" required
                                    class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3 text-gray-800 dark:text-white focus:border-amber-500 dark:focus:border-sky-500 transition"
                                    dir="ltr">
                            </div>
                            <div>
                                <label class="block mb-2 text-gray-500">{{ __('Period Type (Sun or Moon)') }}</label>
                                <select name="period_type"
                                    class="w-full bg-gray-50 dark:bg-black/20 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3 text-gray-800 dark:text-white focus:border-amber-500 dark:focus:border-sky-500 transition">
                                    <option value="morning">{{ __('Morning') }} (Morning)</option>
                                    <option value="afternoon">{{ __('Afternoon') }} (Afternoon)</option>
                                    <option value="evening">{{ __('Evening') }} (Evening)</option>
                                </select>
                            </div>

                            <div class="pt-4 flex gap-3 border-t border-gray-100 dark:border-white/5 pt-6 mt-6">
                                <button type="submit"
                                    class="flex-1 py-3 bg-blue-500 hover:opacity-90 text-white rounded-xl font-bold transition shadow-md">حفظ
                                    الفترة الجديدة</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </template>
    </div>
@endsection