@extends('admin.layouts.app')

@section('title', 'تفاصيل حجز الغسيل #' . $carWash->id)

@section('content')

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.car_washes.index') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-sky-600 hover:border-sky-200 transition shadow-sm">
            <i class="fa-solid fa-arrow-right"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-1">حجز غسيل #{{ $carWash->id }}</h2>
            <p class="text-sm font-medium text-gray-500" dir="ltr">{{ $carWash->created_at->format('M d, Y - h:i A') }}</p>
        </div>
    </div>
    
    <div class="flex items-center gap-3 bg-white p-2 rounded-xl shadow-sm border border-gray-200">
        @if($carWash->status == 'pending')
            <span class="px-4 py-2 rounded-lg bg-amber-50 text-amber-600 text-sm font-bold border border-amber-200"><i class="fa-solid fa-clock text-xs ml-1"></i> بانتظار التأكيد</span>
        @elseif($carWash->status == 'confirmed')
            <span class="px-4 py-2 rounded-lg bg-blue-50 text-blue-600 text-sm font-bold border border-blue-200"><i class="fa-solid fa-clipboard-check text-xs ml-1"></i> مؤكد ومسند لمندوب</span>
        @elseif($carWash->status == 'completed')
            <span class="px-4 py-2 rounded-lg bg-emerald-50 text-emerald-600 text-sm font-bold border border-emerald-200"><i class="fa-solid fa-check-double text-xs ml-1"></i> غسيل مكتمل</span>
        @elseif($carWash->status == 'cancelled')
            <span class="px-4 py-2 rounded-lg bg-red-50 text-red-600 text-sm font-bold border border-red-200"><i class="fa-solid fa-xmark text-xs ml-1"></i> ملغي</span>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Right Sidebar -->
    <div class="space-y-6">
        
        <!-- Financial Summary -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fa-solid fa-file-invoice-dollar text-sky-500"></i> بيانات الدفع</h4>
            
            <div class="pt-2 pb-4 border-b border-gray-50 flex justify-between items-center">
                <span class="text-sm font-bold text-gray-500">سعر الخدمة</span>
                <span class="text-xl font-black text-sky-600">{{ number_format($carWash->cost, 2) }} <span class="text-sm">SAR</span></span>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-3 text-center">
                <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <p class="text-[10px] text-gray-400 font-bold mb-1">طريقة الدفع</p>
                    <p class="text-xs font-bold text-gray-700">
                        @if($carWash->payment_method == 'cash') نقداً @elseif($carWash->payment_method == 'apple_pay') Apple Pay @else {{ $carWash->payment_method }} @endif
                    </p>
                </div>
                <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <p class="text-[10px] text-gray-400 font-bold mb-1">حالة الدفع</p>
                    <p class="text-xs font-bold {{ $carWash->payment_status == 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $carWash->payment_status == 'paid' ? 'مدفوع' : 'غير مدفوع' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Customer Details -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-2 h-full bg-amber-400"></div>
            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fa-solid fa-user text-amber-500"></i> العميل</h4>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xl">{{ mb_substr($carWash->user->name ?? 'ع', 0, 1) }}</div>
                <div>
                    <h5 class="font-bold text-gray-800">{{ $carWash->user->name ?? 'غير متوفر' }}</h5>
                    <p class="text-xs font-bold text-gray-500" dir="ltr">{{ $carWash->user->phone ?? '---' }}</p>
                </div>
            </div>
            @if($carWash->location)
            <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 flex items-start gap-3">
                <i class="fa-solid fa-map-location-dot text-amber-500 mt-1"></i>
                <div>
                    <p class="text-xs font-bold text-gray-800 mb-1">{{ $carWash->location->address_name ?? 'العنوان' }}</p>
                    <p class="text-[10px] font-medium text-gray-500 leading-snug">{{ $carWash->location->description ?? 'لا يوجد وصف للعنوان' }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Agent Details -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden">
            @if($carWash->agent)
                <div class="absolute top-0 right-0 w-2 h-full bg-blue-500"></div>
            @endif
            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fa-solid fa-motorcycle text-blue-500"></i> المندوب</h4>
            @if($carWash->agent)
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xl"><i class="fa-solid fa-helmet-safety"></i></div>
                    <div>
                        <h5 class="font-bold text-gray-800">{{ $carWash->agent->name }}</h5>
                        <p class="text-xs font-bold text-gray-500" dir="ltr">{{ $carWash->agent->phone ?? '---' }}</p>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 p-4 rounded-xl border border-dashed border-gray-200 text-center">
                    <i class="fa-solid fa-user-slash text-2xl text-gray-300 mb-2"></i>
                    <p class="text-sm font-bold text-gray-500">لم يتم تعيين مندوب مغسلة لهذا الحجز</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Details Column -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <h4 class="font-bold text-gray-800 mb-6 flex items-center gap-2"><i class="fa-solid fa-car-clean text-sky-500"></i> تفاصيل خدمة الغسيل</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">نوع وحجم السيارة</p>
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-car-side text-2xl text-sky-500"></i>
                        <span class="font-bold text-gray-800">{{ $carWash->car_size == 'small' ? 'سيارة صغيرة' : 'سيارة كبيرة/عائلية' }}</span>
                    </div>
                </div>
                <div class="bg-gray-50 p-5 rounded-2xl border border-gray-100">
                    <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">نوع الغسيل</p>
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-droplet text-2xl text-sky-500"></i>
                        <span class="font-bold text-gray-800">{{ str_replace('_', ' + ', $carWash->wash_type) }}</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-sky-50/50 border border-sky-100 rounded-2xl p-5 grid grid-cols-2 md:grid-cols-3 gap-4 text-center">
                <div>
                    <i class="fa-regular fa-calendar-check text-sky-500/50 text-xl mb-2"></i>
                    <p class="text-[10px] font-bold text-gray-400 mb-1">تاريخ الحجز</p>
                    <p class="text-sm font-black text-gray-800" dir="ltr">{{ optional($carWash->scheduled_date)->format('Y-m-d') ?? '---' }}</p>
                </div>
                <div>
                    <i class="fa-regular fa-clock text-sky-500/50 text-xl mb-2"></i>
                    <p class="text-[10px] font-bold text-gray-400 mb-1">الفترة الزمنية</p>
                    <p class="text-sm font-black text-gray-800">{{ str_replace('_', ' ', $carWash->time_period) }}</p>
                </div>
                <div class="md:col-span-1">
                    <i class="fa-solid fa-stopwatch text-sky-500/50 text-xl mb-2"></i>
                    <p class="text-[10px] font-bold text-gray-400 mb-1">وقت التنفيذ</p>
                    <p class="text-sm font-black text-gray-800" dir="ltr">{{ $carWash->scheduled_time ?? '---' }}</p>
                </div>
            </div>
            
            <div class="mt-8">
                <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 text-sm"><i class="fa-solid fa-camera"></i> المرفقات</h4>
                <div class="flex gap-4">
                    @if($carWash->item_photo)
                        <div class="relative group w-32 h-32 rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                            <img src="{{ Storage::url($carWash->item_photo) }}" class="w-full h-full object-cover">
                            <a href="{{ Storage::url($carWash->item_photo) }}" target="_blank" class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex flex-col items-center justify-center text-white text-xs font-bold"><i class="fa-solid fa-eye text-lg mb-1"></i> صورة السيارة</a>
                        </div>
                    @endif
                    @if($carWash->invoice_photo)
                        <div class="relative group w-32 h-32 rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                            <img src="{{ Storage::url($carWash->invoice_photo) }}" class="w-full h-full object-cover">
                            <a href="{{ Storage::url($carWash->invoice_photo) }}" target="_blank" class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex flex-col items-center justify-center text-white text-xs font-bold"><i class="fa-solid fa-receipt text-lg mb-1"></i> صورة الفاتورة</a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
