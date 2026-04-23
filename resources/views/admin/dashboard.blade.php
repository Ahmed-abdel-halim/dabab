@extends('admin.layouts.app')

@section('title', __('Overview'))

@section('content')

    <!-- Top Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6 mb-8">
        <!-- Stat Card 1 -->
        <div class="card-custom !p-5 relative group overflow-hidden">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 mb-1">{{ __('Total Users') }}</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($totalUsers) }}</h3>
                    <div class="mt-2 flex items-center gap-1">
                        <span class="text-[10px] text-emerald-500 bg-emerald-50 px-1.5 py-0.5 rounded font-bold">+
                            12%</span>
                        <span class="text-[10px] text-gray-400">{{ __('This Month') }}</span>
                    </div>
                </div>
                <div
                    class="w-12 h-12 rounded-lg bg-[#eaf6f8] flex items-center justify-center text-[#0f7c8c] text-xl shrink-0">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <button class="absolute top-3 left-3 text-gray-400 opacity-0 group-hover:opacity-100 transition">
                <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
            </button>
        </div>

        <!-- Stat Card 2 -->
        <div class="card-custom !p-5 relative group overflow-hidden">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 mb-1">{{ __('Active Agents') }}</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($activeAgents) }}</h3>
                    <div class="mt-2 flex items-center gap-1">
                        <span class="text-[10px] text-emerald-500 bg-emerald-50 px-1.5 py-0.5 rounded font-bold">+ 5%</span>
                        <span class="text-[10px] text-gray-400">{{ __('This Month') }}</span>
                    </div>
                </div>
                <div
                    class="w-12 h-12 rounded-lg bg-[#eaf6f8] flex items-center justify-center text-[#0f7c8c] text-xl shrink-0">
                    <i class="fa-solid fa-helmet-safety"></i>
                </div>
            </div>
            <button class="absolute top-3 left-3 text-gray-400 opacity-0 group-hover:opacity-100 transition">
                <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
            </button>
        </div>

        <!-- Stat Card 3 -->
        <div class="card-custom !p-5 relative group overflow-hidden">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 mb-1">{{ __('Total Orders') }}</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($totalOrdersCount) }}</h3>
                    <div class="mt-2 flex items-center gap-1">
                        <span class="text-[10px] text-rose-500 bg-rose-50 px-1.5 py-0.5 rounded font-bold">- 2%</span>
                        <span class="text-[10px] text-gray-400">{{ __('This Month') }}</span>
                    </div>
                </div>
                <div
                    class="w-12 h-12 rounded-lg bg-[#eaf6f8] flex items-center justify-center text-[#0f7c8c] text-xl shrink-0">
                    <i class="fa-solid fa-cart-shopping"></i>
                </div>
            </div>
            <button class="absolute top-3 left-3 text-gray-400 opacity-0 group-hover:opacity-100 transition">
                <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
            </button>
        </div>

        <!-- Stat Card 4 -->
        <div class="card-custom !p-5 relative group overflow-hidden">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="text-xs font-medium text-gray-500 mb-1">{{ __('Total Revenue') }}</p>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($totalRevenue, 2) }} <span
                            class="text-xs font-medium">{{ __('SAR') }}</span></h3>
                    <div class="mt-2 flex items-center gap-1">
                        <span class="text-[10px] text-emerald-500 bg-emerald-50 px-1.5 py-0.5 rounded font-bold">+
                            24%</span>
                        <span class="text-[10px] text-gray-400">{{ __('This Month') }}</span>
                    </div>
                </div>
                <div
                    class="w-12 h-12 rounded-lg bg-[#eaf6f8] flex items-center justify-center text-[#0f7c8c] text-xl shrink-0">
                    <i class="fa-solid fa-sack-dollar"></i>
                </div>
            </div>
            <button class="absolute top-3 left-3 text-gray-400 opacity-0 group-hover:opacity-100 transition">
                <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
            </button>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- المستخدمون حسب الدولة -->
        <div class="card-custom bg-white dark:bg-[#161b22]">
            <div class="flex items-center justify-between mb-6">
                <button
                    class="bg-[#eaf6f8] text-[#0f7c8c] text-[10px] font-bold px-3 py-1.5 rounded-md hover:bg-[#d5edf1] transition">{{ __('Export Report') }}</button>
                <h4 class="text-sm font-bold text-[#1f2937] dark:text-white">{{ __('Live Users By Country') }}</h4>
            </div>

            <div class="relative py-4">
                <div id="world-map" style="height: 250px;"></div>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    new jsVectorMap({
                        selector: "#world-map",
                        map: "world",
                        zoomButtons: false,
                        regionStyle: {
                            initial: {
                                fill: '#dfeff3',
                                stroke: '#fff',
                                strokeWidth: 0.5
                            },
                            hover: { fill: '#0f7c8c' }
                        },
                        markers: [
                            { name: "{{ __('Saudi Arabia') }}", coords: [23.8859, 45.0792], style: { fill: '#0f7c8c' } },
                            { name: "{{ __('Egypt') }}", coords: [26.8206, 30.8025], style: { fill: '#3b82f6' } },
                            { name: "{{ __('United States') }}", coords: [37.0902, -95.7129] },
                            { name: "{{ __('Brazil') }}", coords: [-14.235, -51.9253] },
                            { name: "{{ __('Russia') }}", coords: [61.524, 105.3188] },
                            { name: "{{ __('China') }}", coords: [35.8617, 104.1954] },
                            { name: "{{ __('Australia') }}", coords: [-25.2744, 133.7751] }
                        ],
                        markerStyle: {
                            initial: {
                                r: 5,
                                fill: '#475569',
                                stroke: '#fff',
                                strokeWidth: 2
                            }
                        },
                        lines: [
                            { from: "{{ __('United States') }}", to: "{{ __('Saudi Arabia') }}" },
                            { from: "{{ __('Brazil') }}", to: "{{ __('Saudi Arabia') }}" },
                            { from: "{{ __('Russia') }}", to: "{{ __('Saudi Arabia') }}" },
                            { from: "{{ __('China') }}", to: "{{ __('Saudi Arabia') }}" },
                            { from: "{{ __('Australia') }}", to: "{{ __('Saudi Arabia') }}" },
                            { from: "{{ __('Egypt') }}", to: "{{ __('Saudi Arabia') }}" }
                        ],
                        lineStyle: {
                            stroke: '#94a3b8',
                            strokeWidth: 1.5,
                            strokeDasharray: '6 6',
                            animation: true
                        }
                    });
                });
            </script>

            <div class="mt-4 px-1 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500">85%</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-200">{{ __('Saudi Arabia') }}</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-[#10b981]"></span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500">10%</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-200">{{ __('Egypt') }}</span>
                        <span class="w-2.5 h-2.5 rounded-full bg-[#3b82f6]"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Audiences Metrics -->
        <div class="card-custom bg-white dark:bg-[#161b22] lg:col-span-2">
            <div class="flex items-center justify-between mb-6">
                <div class="flex bg-gray-50 dark:bg-black/20 p-1 rounded-lg">
                    <button
                        class="px-3 py-1 text-[10px] font-bold rounded-md bg-white dark:bg-[#161b22] shadow-sm text-[#0f7c8c]">{{ __('Year') }}</button>
                    <button class="px-3 py-1 text-[10px] font-bold rounded-md text-gray-400">{{ __('6 Months') }}</button>
                    <button class="px-3 py-1 text-[10px] font-bold rounded-md text-gray-400">{{ __('Month') }}</button>
                </div>
                <h4 class="text-sm font-bold text-gray-800 dark:text-white uppercase">{{ __('Sales and Growth Stats') }}</h4>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center flex-1">
                <!-- Donut Chart -->
                <div class="relative flex items-center justify-center">
                    <div id="donutChart"></div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-[9px] font-bold text-gray-400 uppercase leading-none mb-1">{{ __('Total Value') }}</span>
                        <span
                            class="text-lg font-black text-gray-800 dark:text-white leading-none">{{ number_format($totalRevenue, 0) }} <span class="text-[10px]">{{ __('SAR') }}</span></span>
                    </div>
                </div>

                <!-- Bar Chart -->
                <div class="md:col-span-2">
                    <div id="barChart" class="-mb-6"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Bottom Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Daily Progress Section -->
        <div class="lg:col-span-2 space-y-6">
            <div class="card-custom bg-white dark:bg-[#161b22]">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white">{{ __('Today Statistics') }}</h3>
                        <p class="text-xs font-medium text-gray-500">{{ now()->translatedFormat('l, d M Y') }}</p>
                    </div>
                    <div class="text-left">
                        <span class="text-xs font-bold text-gray-400 uppercase">{{ __('Today Revenue') }}</span>
                        <p class="text-xl font-black text-emerald-600">{{ number_format($todayRevenue, 2) }} {{ __('SAR') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-[#f0f9fa] dark:bg-[#0f7c8c]/10 border border-[#cfeef3] dark:border-[#0f7c8c]/20 p-5 rounded-xl flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg bg-white dark:bg-[#161b22] flex items-center justify-center text-[#0f7c8c] shadow-sm">
                            <i class="fa-solid fa-cart-shopping text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 uppercase mb-0.5">{{ __("Today's Orders") }}</p>
                            <h4 class="text-xl font-black text-gray-800 dark:text-white">{{ $todayOrders }}</h4>
                        </div>
                    </div>
                    <div class="bg-[#f0fdf4] dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 p-5 rounded-xl flex items-center gap-4">
                        <div class="w-12 h-12 rounded-lg bg-white dark:bg-[#161b22] flex items-center justify-center text-emerald-600 shadow-sm">
                            <i class="fa-solid fa-users text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-500 uppercase mb-0.5">{{ __('New Users') }}</p>
                            <h4 class="text-xl font-black text-gray-800 dark:text-white">{{ $totalUsers > 10 ? 12 : $totalUsers }}</h4>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 dark:border-white/5">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-sm font-bold text-gray-800 dark:text-white">{{ __('Recent Platform Activities') }}</h4>
                        <a href="#" class="text-[11px] font-bold text-[#0f7c8c] hover:underline uppercase">{{ __('View All') }}</a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-right">
                            <thead>
                                <tr class="text-gray-400 text-[10px] font-bold uppercase border-b border-gray-50 dark:border-white/5">
                                    <th class="pb-3 pr-2">{{ __('Process') }}</th>
                                    <th class="pb-3">{{ __('User') }}</th>
                                    <th class="pb-3 text-center">{{ __('Number') }}</th>
                                    <th class="pb-3 text-left">{{ __('Cost') }}</th>
                                    <th class="pb-3 text-left pl-2">{{ __('Time') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-white/5">
                                @foreach($recentOperations as $op)
                                    <tr class="group hover:bg-gray-50 dark:hover:bg-white/5 transition duration-150">
                                        <td class="py-4 pr-2">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded bg-gray-100 dark:bg-black/20 flex items-center justify-center text-gray-600 dark:text-gray-300 group-hover:bg-[#0f7c8c] group-hover:text-white transition">
                                                    <i class="fa-solid {{ $op->type_icon }} text-[12px]"></i>
                                                </div>
                                                <span class="text-xs font-bold text-gray-700 dark:text-gray-200">{{ $op->op_type }}</span>
                                            </div>
                                        </td>
                                        <td class="py-4">
                                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $op->user->name ?? __('User') }}</span>
                                        </td>
                                        <td class="py-4 text-center">
                                            <span class="text-[10px] font-mono font-bold text-gray-400 bg-gray-50 dark:bg-black/20 px-2 py-0.5 rounded">{{ $op->display_id }}</span>
                                        </td>
                                        <td class="py-4 text-left">
                                            <span class="text-xs font-black text-gray-800 dark:text-white">{{ number_format($op->total_cost ?? $op->delivery_cost ?? $op->cost ?? 0, 2) }} <span class="text-[9px]">{{ __('SAR') }}</span></span>
                                        </td>
                                        <td class="py-4 text-left pl-2">
                                            <span class="text-[10px] text-gray-400 font-bold uppercase whitespace-nowrap">{{ $op->created_at->diffForHumans() }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Panel -->
        <div class="space-y-6">
            <!-- Welcome Card -->
            <div class="bg-gradient-to-br from-[#0f7c8c] to-[#0b6674] rounded-xl p-8 text-white relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-10">
                    <i class="fa-solid fa-rocket text-8xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2 relative z-10">{{ __('Welcome') }}، {{ Auth::guard('admin')->user()->name }}</h3>
                <p class="text-sm opacity-80 mb-6 leading-relaxed relative z-10">{{ __('You have full control over all system aspects, prices, and agents') }}</p>
                <div class="space-y-3 relative z-10">
                    <a href="{{ route('admin.settings.general') }}"
                        class="block w-full text-center bg-white/20 hover:bg-white/30 backdrop-blur-md text-white font-bold py-3 rounded-lg transition text-sm">{{ __('General Settings') }}</a>
                    <a href="{{ route('admin.settings.categories.index') }}"
                        class="block w-full text-center bg-white text-[#0f7c8c] font-bold py-3 rounded-lg transition shadow-sm text-sm">{{ __('Stocks and Sections Management') }}</a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="card-custom bg-white dark:bg-[#161b22]">
                <h4 class="text-sm font-bold text-gray-800 dark:text-white mb-4">{{ __('Quick Links') }}</h4>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('admin.agents.index', ['status' => 'pending']) }}"
                        class="p-4 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/5 text-center group transition hover:border-[#0f7c8c]">
                        <i class="fa-solid fa-user-clock text-amber-500 mb-2 block group-hover:scale-110 transition"></i>
                        <span class="text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase">{{ __('New Agents') }}</span>
                    </a>
                    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
                        class="p-4 rounded-lg bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/5 text-center group transition hover:border-[#0f7c8c]">
                        <i class="fa-solid fa-clock text-blue-500 mb-2 block group-hover:scale-110 transition"></i>
                        <span class="text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase">{{ __('Pending Orders') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Donut Chart
            var donutOptions = {
                series: [44, 55, 41],
                chart: { type: 'donut', height: 280, sparkline: { enabled: true } },
                colors: ['#0f7c8c', '#10b981', '#cbd5e1'],
                stroke: { width: 0 },
                dataLabels: { enabled: false },
                legend: { show: false },
                plotOptions: { pie: { donut: { size: '82%' } } }
            };
            new ApexCharts(document.querySelector("#donutChart"), donutOptions).render();

            // Bar Chart
            var barOptions = {
                series: [{
                    name: "{{ __('Sales') }}",
                    data: [44, 55, 41, 67, 22, 43, 21, 41, 56, 27, 43, 22]
                }, {
                    name: "{{ __('Growth') }}",
                    data: [13, 23, 20, 8, 13, 27, 33, 12, 11, 14, 21, 17]
                }],
                chart: { type: 'bar', height: 380, stacked: true, toolbar: { show: false }, zoom: { enabled: false } },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        borderRadius: 0,
                        columnWidth: '40%',
                        endingShape: 'flat'
                    }
                },
                colors: ['#0f7c8c', '#eaf6f8'],
                dataLabels: { enabled: false },
                xaxis: {
                    type: 'category',
                    categories: ["{{ __('January') }}", "{{ __('February') }}", "{{ __('March') }}", "{{ __('April') }}", "{{ __('May') }}", "{{ __('June') }}", "{{ __('July') }}", "{{ __('August') }}", "{{ __('September') }}", "{{ __('October') }}", "{{ __('November') }}", "{{ __('December') }}"],
                    labels: { offsetY: 0, style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 600 } },
                    axisBorder: { show: false }, axisTicks: { show: false }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) { return val },
                        style: { colors: '#94a3b8', fontSize: '10px', fontWeight: 600 }
                    }
                },
                legend: { show: false },
                fill: { opacity: 1 },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4, yaxis: { lines: { show: true } }, padding: { bottom: 0, left: 10 } }
            };
            new ApexCharts(document.querySelector("#barChart"), barOptions).render();
        });
    </script>

@endsection