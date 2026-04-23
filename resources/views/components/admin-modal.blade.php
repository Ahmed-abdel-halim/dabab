@props(['name', 'title'])

<div
    x-data="{ show: false }"
    x-show="show"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') show = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') show = false"
    x-on:keydown.escape.window="show = false"
    class="fixed inset-0 z-[9999] overflow-y-auto px-4 py-6 flex items-start sm:items-center justify-center"
    style="display: none;"
>
    <!-- Background Overlay -->
    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transform transition-all" @click="show = false">
        <div class="absolute inset-0 bg-gray-900/80 backdrop-blur-sm"></div>
    </div>

    <!-- Modal Content -->
    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
         class="bg-white dark:bg-[#161b22] rounded-[2rem] overflow-hidden shadow-2xl transform transition-all w-full max-w-2xl border border-gray-100 dark:border-white/5 relative z-10">
        
        <div class="px-8 py-6 border-b border-gray-100 dark:border-white/5 flex items-center justify-between bg-gray-50/50 dark:bg-white/5">
            <h3 class="text-xl font-bold text-gray-800 dark:text-white">{{ $title }}</h3>
            <button @click="show = false" class="w-10 h-10 rounded-full flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-500 transition">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="p-8">
            {{ $slot }}
        </div>
    </div>
</div>
