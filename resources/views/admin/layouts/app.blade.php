<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dabab Admin - @yield('title', 'الرئيسية')</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo' . (app()->getLocale() == 'en' ? '-en' : '') . '.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/logo' . (app()->getLocale() == 'en' ? '-en' : '') . '.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- jsVectorMap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap/dist/css/jsvectormap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsvectormap/dist/maps/world.js"></script>
    
    <script>
        // Prevent FOUC and Sidebar jumping
        (function() {
            const theme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            if (theme === 'dark') document.documentElement.classList.add('dark');
            
            const collapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (collapsed) {
                document.documentElement.classList.add('sidebar-is-collapsed');
                document.documentElement.style.setProperty('--sidebar-width', '78px');
            } else {
                document.documentElement.style.setProperty('--sidebar-width', '260px');
            }
        })();
    </script>

    <style>
        :root {
            --primary: #0f7c8c;
            --primary-dark: #0b6674;
            --secondary: #eaf6f8;
            --bg-main: #f8fafc;
            --surface: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border: #f1f5f9;
            --shadow: none;
            --sidebar-width: 260px;
        }

        /* Initial sidebar width to prevent jumping */
        aside {
            width: var(--sidebar-width) !important;
            flex-basis: var(--sidebar-width) !important;
            flex-shrink: 0 !important;
        }
        
        /* Disable transitions inside sidebar during load */
        aside *, aside {
            transition: none !important;
        }

        /* Only animate after page is ready */
        .sidebar-ready aside,
        .sidebar-ready aside * {
            transition: all 0.3s ease, width 0.3s ease, transform 0.3s ease, flex-basis 0.3s ease !important;
        }

        @media (max-width: 1024px) {
            aside {
                width: 260px !important;
            }
        }

        .card-custom {
            background: var(--surface);
            border-radius: 0.375rem;
            padding: 24px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.03), 0 5px 15px -5px rgba(0, 0, 0, 0.02);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .dark .card-custom {
            box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.5), 0 5px 15px -5px rgba(0, 0, 0, 0.3);
            border-color: rgba(255, 255, 255, 0.05);
        }

        .card-custom:hover {
            transform: translateY(-6px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08), 0 10px 20px -5px rgba(0, 0, 0, 0.04);
        }

        .btn-primary-custom {
            background-color: var(--primary);
            color: #ffffff;
            border-radius: 8px;
            padding: 10px 16px;
            font-weight: 500;
            transition: all 0.25s ease;
        }

        .btn-light-custom {
            background-color: var(--secondary);
            color: var(--primary);
            border-radius: 8px;
            padding: 10px 16px;
            font-weight: 500;
            transition: all 0.25s ease;
        }

        .container-3d {
            background: #ffffff;
            border-radius: 0.5rem;
            border: 1px solid rgba(0, 0, 0, 0.03);
            box-shadow: 0 20px 60px -15px rgba(0, 0, 0, 0.08), 0 10px 20px -5px rgba(0, 0, 0, 0.02);
            overflow: hidden;
            transition: all 0.5s ease;
        }

        .dark .container-3d {
            background: #161b22;
            border-color: rgba(255, 255, 255, 0.05);
            box-shadow: 0 20px 60px -15px rgba(0, 0, 0, 0.4), 0 10px 20px -5px rgba(0, 0, 0, 0.2);
        }

        .input-custom {
            height: 42px;
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0 16px;
            outline: none;
            transition: border-color 0.2s;
        }

        .input-custom:focus {
            border-color: var(--primary);
        }

        [x-cloak] {
            display: none !important;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
        }

        ::-webkit-scrollbar-thumb {
            background: #0b6674;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #08505c;
        }

        /* For Firefox */
        * {
            scrollbar-width: thin;
            scrollbar-color: #0b6674 rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

    <body
    class="bg-[#f1f5f9] dark:bg-[#0d1117] text-[#1f2937] dark:text-gray-100 antialiased font-sans flex max-h-screen overflow-hidden"
    x-data="{ 
        sidebarOpen: false, 
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true', 
        darkMode: localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches), 
        isFullscreen: false 
    }"
    x-init="
        $watch('darkMode', val => {
            localStorage.setItem('theme', val ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', val);
        }); 
        $watch('sidebarCollapsed', val => {
            localStorage.setItem('sidebarCollapsed', val);
            document.documentElement.style.setProperty('--sidebar-width', val ? '78px' : '260px');
        });
        window.addEventListener('fullscreenchange', () => { isFullscreen = !!document.fullscreenElement });
        
        // Enable animations after initial load
        setTimeout(() => {
            document.body.classList.add('sidebar-ready');
        }, 400);
    "
    :class="{ 'dark': darkMode }">

    <!-- Sidebar -->
    <aside
        class="bg-[#0f7c8c] dark:bg-[#0b6674] flex flex-col h-screen fixed lg:relative z-40 transform lg:!translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full'">
        <div class="pb-6 px-6 flex items-center"
            :class="sidebarCollapsed ? 'justify-center pt-8' : 'justify-between pt-3'">

            <div class="flex items-center gap-2 overflow-hidden flex-1" x-show="!sidebarCollapsed"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <img src="{{ asset('assets/img/logo' . (app()->getLocale() == 'en' ? '-en' : '') . '.png') }}" alt="{{ __('Dabab') }}" class="h-16 w-auto object-contain">
            </div>

            <button @click="sidebarCollapsed = !sidebarCollapsed"
                class="text-white/80 hover:text-white transition hidden lg:block">
                <i class="fa-solid" :class="sidebarCollapsed ? 'fa-bars' : 'fa-align-right'"></i>
            </button>

            <button @click="sidebarOpen = false" class="lg:hidden text-white/80 transition-colors hover:text-white"><i
                    class="fa-solid fa-xmark"></i></button>
        </div>
        <nav class="flex-1 px-4 space-y-2 overflow-y-auto mt-4 hide-scrollbar">
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3 px-4 h-[44px] rounded-[10px] hover:bg-white/12 font-medium text-white {{ request()->routeIs('admin.dashboard') ? 'bg-white/18' : '' }}"
                :class="sidebarCollapsed ? 'justify-center px-0' : ''" title="{{ __('Dashboard') }}">
                <i class="fa-solid fa-chart-pie w-5 text-center shrink-0"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap overflow-hidden">{{ __('Dashboard') }}</span>
            </a>

            <p x-show="!sidebarCollapsed"
                class="px-4 py-2 mt-4 text-[10px] font-black text-white/40 uppercase tracking-[0.2em]">
                {{ __('System Management') }}</p>
            <div x-show="sidebarCollapsed" class="h-px bg-white/10 my-4 mx-2"></div>

            <a href="{{ route('admin.users.index') }}"
                class="flex items-center gap-3 px-4 h-[44px] rounded-[10px] hover:bg-white/12 font-medium text-white {{ request()->routeIs('admin.users.*') ? 'bg-white/18' : '' }}"
                :class="sidebarCollapsed ? 'justify-center px-0' : ''" title="{{ __('Users') }}">
                <i class="fa-solid fa-users w-5 text-center shrink-0"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap overflow-hidden">{{ __('Users') }}</span>
            </a>
            <a href="{{ route('admin.agents.index') }}"
                class="flex items-center gap-3 px-4 h-[44px] rounded-[10px] hover:bg-white/12 font-medium text-white {{ request()->routeIs('admin.agents.*') ? 'bg-white/18' : '' }}"
                :class="sidebarCollapsed ? 'justify-center px-0' : ''" title="{{ __('Agents') }}">
                <i class="fa-solid fa-motorcycle w-5 text-center shrink-0"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap overflow-hidden">{{ __('Agents') }}</span>
            </a>
            <a href="{{ route('admin.orders.index') }}"
                class="flex items-center gap-3 px-4 h-[44px] rounded-[10px] hover:bg-white/12 font-medium text-white {{ request()->routeIs('admin.orders.*') ? 'bg-white/18' : '' }}"
                :class="sidebarCollapsed ? 'justify-center px-0 relative' : ''" title="{{ __('Store Orders') }}">
                <i class="fa-solid fa-box-open w-5 text-center shrink-0"></i>
                <span x-show="!sidebarCollapsed"
                    class="whitespace-nowrap overflow-hidden">{{ __('Store Orders') }}</span>
                <span x-show="!sidebarCollapsed"
                    class="flex items-center justify-center w-5 h-5 ms-auto text-[10px] font-bold bg-[#10b981] text-white rounded-full shadow-sm">{{ $pendingOrdersCount }}</span>
                @if($pendingOrdersCount > 0)
                    <div x-show="sidebarCollapsed"
                        class="absolute top-2 right-2 w-2 h-2 bg-[#10b981] rounded-full border border-white/20"></div>
                @endif
            </a>
            <a href="{{ route('admin.deliveries.index') }}"
                class="flex items-center gap-3 px-4 h-[44px] rounded-[10px] hover:bg-white/12 font-medium text-white {{ request()->routeIs('admin.deliveries.*') ? 'bg-white/18' : '' }}"
                :class="sidebarCollapsed ? 'justify-center px-0 relative' : ''" title="{{ __('Delivery Orders') }}">
                <i class="fa-solid fa-truck-fast w-5 text-center shrink-0"></i>
                <span x-show="!sidebarCollapsed"
                    class="whitespace-nowrap overflow-hidden">{{ __('Delivery Orders') }}</span>
                <span x-show="!sidebarCollapsed"
                    class="flex items-center justify-center w-5 h-5 ms-auto text-[10px] font-bold bg-[#10b981] text-white rounded-full shadow-sm">{{ $pendingDeliveriesCount }}</span>
                @if($pendingDeliveriesCount > 0)
                    <div x-show="sidebarCollapsed"
                        class="absolute top-2 right-2 w-2 h-2 bg-[#10b981] rounded-full border border-white/20"></div>
                @endif
            </a>
            <a href="{{ route('admin.rentals.index') }}"
                class="flex items-center gap-3 px-4 h-[44px] rounded-[10px] hover:bg-white/12 font-medium text-white {{ request()->routeIs('admin.rentals.*') ? 'bg-white/18' : '' }}"
                :class="sidebarCollapsed ? 'justify-center px-0 relative' : ''" title="{{ __('Bike Rentals') }}">
                <i class="fa-solid fa-key w-5 text-center shrink-0"></i>
                <span x-show="!sidebarCollapsed"
                    class="whitespace-nowrap overflow-hidden">{{ __('Bike Rentals') }}</span>
                <span x-show="!sidebarCollapsed"
                    class="flex items-center justify-center w-5 h-5 ms-auto text-[10px] font-bold bg-[#10b981] text-white rounded-full shadow-sm">{{ $pendingRentalsCount }}</span>
                @if($pendingRentalsCount > 0)
                    <div x-show="sidebarCollapsed"
                        class="absolute top-2 right-2 w-2 h-2 bg-[#10b981] rounded-full border border-white/20"></div>
                @endif
            </a>
            <a href="{{ route('admin.car_washes.index') }}"
                class="flex items-center gap-3 px-4 h-[44px] rounded-[10px] hover:bg-white/12 font-medium text-white {{ request()->routeIs('admin.car_washes.*') ? 'bg-white/18' : '' }}"
                :class="sidebarCollapsed ? 'justify-center px-0 relative' : ''" title="{{ __('Car Wash Orders') }}">
                <i class="fa-solid fa-droplet w-5 text-center shrink-0"></i>
                <span x-show="!sidebarCollapsed"
                    class="whitespace-nowrap overflow-hidden">{{ __('Car Wash Orders') }}</span>
                <span x-show="!sidebarCollapsed"
                    class="flex items-center justify-center w-5 h-5 ms-auto text-[10px] font-bold bg-[#10b981] text-white rounded-full shadow-sm">{{ $pendingCarWashesCount }}</span>
                @if($pendingCarWashesCount > 0)
                    <div x-show="sidebarCollapsed"
                        class="absolute top-2 right-2 w-2 h-2 bg-[#10b981] rounded-full border border-white/20"></div>
                @endif
            </a>


            <a href="{{ route('admin.ratings.index') }}"
                class="flex items-center gap-3 px-4 h-[44px] rounded-[10px] hover:bg-white/12 font-medium text-white {{ request()->routeIs('admin.ratings.*') ? 'bg-white/18' : '' }}"
                :class="sidebarCollapsed ? 'justify-center px-0' : ''" title="{{ __('Ratings') }}">
                <i class="fa-solid fa-star w-5 text-center shrink-0"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap overflow-hidden">{{ __('Ratings') }}</span>
            </a>
            <a href="{{ route('admin.wallet.index') }}"
                class="flex items-center gap-3 px-4 h-[44px] rounded-[10px] hover:bg-white/12 font-medium text-white transition-all {{ request()->routeIs('admin.wallet.*') ? 'bg-white/18' : '' }}"
                :class="sidebarCollapsed ? 'justify-center px-0' : ''" title="{{ __('Wallet') }}">
                <i class="fa-solid fa-wallet w-5 text-center shrink-0"></i>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap overflow-hidden">{{ __('Wallet') }}</span>
            </a>

            <!-- Changed Settings Link to Expandable or directly to settings.carwash for now -->
            <div x-data="{ open: {{ request()->routeIs('settings.*') ? 'true' : 'false' }} }"
                :class="sidebarCollapsed ? 'relative' : ''">
                <button @click="sidebarCollapsed ? (sidebarCollapsed = false, open = true) : open = !open"
                    class="w-full flex items-center justify-between px-4 h-[44px] rounded-[10px] hover:bg-white/12 font-medium text-white transition-all {{ request()->routeIs('settings.*') ? 'bg-white/18' : '' }}"
                    :class="sidebarCollapsed ? 'justify-center px-0' : ''" title="{{ __('Settings') }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-cog w-5 text-center shrink-0"></i>
                        <span x-show="!sidebarCollapsed"
                            class="whitespace-nowrap overflow-hidden text-sm">{{ __('Settings') }}</span>
                    </div>
                    <i x-show="!sidebarCollapsed" class="fa-solid fa-chevron-down text-[10px] transition-transform"
                        :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open && !sidebarCollapsed" x-collapse class="pl-12 pr-4 py-2 space-y-1">
                    <a href="{{ route('admin.settings.carwash') }}"
                        class="block px-4 py-2 rounded-lg text-sm font-medium text-white/60 hover:text-white {{ request()->routeIs('admin.settings.carwash') ? 'text-white' : '' }}">{{ __('Car Wash') }}</a>
                    <a href="{{ route('admin.settings.categories.index') }}"
                        class="block px-4 py-2 rounded-lg text-sm font-medium text-white/60 hover:text-white {{ request()->routeIs('admin.settings.categories.*') ? 'text-white' : '' }}">{{ __('Store Categories') }}</a>
                    <a href="{{ route('admin.settings.pages.index') }}"
                        class="block px-4 py-2 rounded-lg text-sm font-medium text-white/60 hover:text-white {{ request()->routeIs('admin.settings.pages.*') ? 'text-white' : '' }}">{{ __('Informational Pages') }}</a>
                    <a href="{{ route('admin.settings.faqs.index') }}"
                        class="block px-4 py-2 rounded-lg text-sm font-medium text-white/60 hover:text-white {{ request()->routeIs('admin.settings.faqs.*') ? 'text-white' : '' }}">{{ __('FAQs') }}</a>
                    <a href="{{ route('admin.settings.general') }}"
                        class="block px-4 py-2 rounded-lg text-sm font-medium text-white/60 hover:text-white {{ request()->routeIs('admin.settings.general') ? 'text-white' : '' }}">{{ __('General Settings') }}</a>
                </div>
            </div>
        </nav>
        <div class="p-4 border-t border-white/10">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button
                    class="w-full flex items-center justify-center gap-2 h-[44px] bg-rose-500/10 text-white/90 hover:bg-rose-500/75 hover:text-white rounded-[10px] transition font-medium text-sm"
                    :class="sidebarCollapsed ? 'px-0' : 'px-4'" title="{{ __('Logout') }}">
                    <i class="fa-solid fa-sign-out-alt w-5 text-center shrink-0"></i>
                    <span x-show="!sidebarCollapsed"
                        class="whitespace-nowrap overflow-hidden text-sm">{{ __('Logout') }}</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Overlay -->
    <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
        class="fixed inset-0 bg-black/50 overflow-hidden z-30 lg:hidden"></div>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col min-w-0 h-screen scroll-smooth">
        <!-- Header -->
        <header
            class="h-[70px] bg-white dark:bg-[#161b22] border-b border-[var(--border)] px-6 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500">
                    <i class="fa-solid fa-bars-staggered text-xl"></i>
                </button>
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">@yield('title', __('Dashboard'))</h2>
            </div>

            <div class="flex-1"></div>

            <div class="flex items-center gap-2 sm:gap-4">
                <!-- Icon Group from Image -->
                <div class="hidden lg:flex items-center gap-1 border-l border-gray-100 dark:border-white/5 pl-4 ml-2">
                    <button
                        class="w-10 h-10 flex items-center justify-center bg-gray-50 dark:bg-white/5 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 rounded-xl transition">
                        <i class="fa-solid fa-cog text-lg"></i>
                    </button>
                    <button
                        class="w-10 h-10 flex items-center justify-center bg-gray-50 dark:bg-white/5 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 rounded-xl transition relative">
                        <i class="fa-solid fa-bell text-lg"></i>
                        <span
                            class="absolute top-2.5 right-2.5 w-2 h-2 bg-rose-500 border-2 border-white dark:border-[#161b22] rounded-full"></span>
                    </button>
                    <button
                        class="w-10 h-10 flex items-center justify-center bg-gray-50 dark:bg-white/5 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 rounded-xl transition"
                        @click="if (!document.fullscreenElement) { document.documentElement.requestFullscreen() } else { document.exitFullscreen() }">
                        <i class="fa-solid text-lg" :class="isFullscreen ? 'fa-compress' : 'fa-expand'"></i>
                    </button>
                    <!-- Language Switcher Dropdown -->
                    <div class="relative" x-data="{ langOpen: false }">
                        <button @click="langOpen = !langOpen"
                            class="w-10 h-10 flex items-center justify-center bg-gray-50 dark:bg-white/5 hover:bg-gray-100 dark:hover:bg-white/10 rounded-xl transition relative">
                            <img src="https://flagcdn.com/w40/{{ app()->getLocale() == 'ar' ? 'sa' : 'us' }}.png"
                                width="28" alt="Current Lang" class="rounded-sm shadow-sm">
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="langOpen" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            @click.away="langOpen = false"
                            class="absolute top-full mt-2 {{ app()->getLocale() == 'ar' ? 'left-0' : 'right-0' }} w-40 bg-white dark:bg-[#161b22] border border-gray-100 dark:border-white/5 rounded-xl shadow-xl z-50 overflow-hidden py-1">

                            <a href="{{ route('admin.lang.switch', ['locale' => 'ar']) }}"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-white/5 transition {{ app()->getLocale() == 'ar' ? 'bg-gray-50 dark:bg-white/5 pointer-events-none' : '' }}">
                                <img src="https://flagcdn.com/w40/sa.png" width="24" alt="AR"
                                    class="rounded-sm shadow-sm">
                                <span
                                    class="text-xs font-bold {{ app()->getLocale() == 'ar' ? 'text-[#0f7c8c]' : 'text-gray-700 dark:text-gray-300' }}">العربية</span>
                                @if(app()->getLocale() == 'ar')
                                    <i class="fa-solid fa-check mr-auto text-[10px] text-[#0f7c8c]"></i>
                                @endif
                            </a>

                            <a href="{{ route('admin.lang.switch', ['locale' => 'en']) }}"
                                class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-white/5 transition {{ app()->getLocale() == 'en' ? 'bg-gray-50 dark:bg-white/5 pointer-events-none' : '' }}">
                                <img src="https://flagcdn.com/w40/us.png" width="24" alt="EN"
                                    class="rounded-sm shadow-sm">
                                <span
                                    class="text-xs font-bold {{ app()->getLocale() == 'en' ? 'text-[#0f7c8c]' : 'text-gray-700 dark:text-gray-300' }}">English</span>
                                @if(app()->getLocale() == 'en')
                                    <i class="fa-solid fa-check ml-auto text-[10px] text-[#0f7c8c]"></i>
                                @endif
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Dark Mode Toggle -->
                <button @click="darkMode = !darkMode"
                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-gray-50 dark:bg-white/5 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 transition">
                    <i class="fa-solid text-lg" :class="darkMode ? 'fa-sun' : 'fa-moon'"></i>
                </button>

                <!-- User Info -->
                <div class="flex items-center gap-3 pr-4 border-r border-gray-100 dark:border-white/5">
                    <div class="text-left hidden sm:block">
                        <p class="text-sm font-bold text-gray-800 dark:text-white leading-none mb-1">
                            {{ Auth::guard('admin')->user()->name }}
                        </p>
                        <p class="text-[10px] font-bold text-emerald-500 uppercase">{{ __('Online') }}</p>
                    </div>
                    <div
                        class="w-10 h-10 rounded-xl bg-[#0f7c8c] flex items-center justify-center text-white font-bold shadow-sm">
                        {{ mb_substr(Auth::guard('admin')->user()->name, 0, 1) }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="flex-1 overflow-y-auto p-4 md:p-8">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </div>
    </main>

</body>

</html>