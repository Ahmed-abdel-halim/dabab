@extends('admin.layouts.app')

@section('title', 'تفاصيل طلب التأجير #' . $rental->order_number)

@section('content')

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.rentals.index') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-amber-600 hover:border-amber-200 transition shadow-sm">
            <i class="fa-solid fa-arrow-right"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-1">تأجير #{{ $rental->order_number }}</h2>
            <p class="text-sm font-medium text-gray-500" dir="ltr">{{ $rental->created_at->format('M d, Y - h:i A') }}</p>
        </div>
    </div>
    
    <div class="flex items-center gap-3 bg-white p-2 rounded-xl shadow-sm border border-gray-200">
        @if($rental->status == 'pending')
            <span class="px-4 py-2 rounded-lg bg-amber-50 text-amber-600 text-sm font-bold border border-amber-200"><i class="fa-solid fa-clock text-xs ml-1"></i> في الانتظار</span>
        @elseif($rental->status == 'active')
            <span class="px-4 py-2 rounded-lg bg-blue-50 text-blue-600 text-sm font-bold border border-blue-200"><i class="fa-solid fa-key text-xs ml-1"></i> مؤجر حالياً</span>
        @elseif($rental->status == 'completed')
            <span class="px-4 py-2 rounded-lg bg-emerald-50 text-emerald-600 text-sm font-bold border border-emerald-200"><i class="fa-solid fa-check-double text-xs ml-1"></i> مكتمل المسترجع</span>
        @elseif($rental->status == 'cancelled')
            <span class="px-4 py-2 rounded-lg bg-red-50 text-red-600 text-sm font-bold border border-red-200"><i class="fa-solid fa-xmark text-xs ml-1"></i> ملغي</span>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Right Sidebar (Overview) -->
    <div class="space-y-6">
        
        <!-- Financial Summary -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fa-solid fa-file-invoice-dollar text-emerald-500"></i> بيانات الدفع</h4>
            
            <div class="pt-2 pb-4 border-b border-gray-50 flex justify-between items-center">
                <span class="text-sm font-bold text-gray-500">إجمالي الإيجار</span>
                <span class="text-xl font-black text-emerald-600">{{ number_format($rental->cost, 2) }} <span class="text-sm">SAR</span></span>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-3 text-center">
                <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <p class="text-[10px] text-gray-400 font-bold mb-1">طريقة الدفع</p>
                    <p class="text-xs font-bold text-gray-700">
                        @if($rental->payment_method == 'cash') نقداً @elseif($rental->payment_method == 'apple_pay') Apple Pay @else {{ $rental->payment_method }} @endif
                    </p>
                </div>
                <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <p class="text-[10px] text-gray-400 font-bold mb-1">حالة الدفع</p>
                    <p class="text-xs font-bold {{ $rental->payment_status == 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $rental->payment_status == 'paid' ? 'مدفوع' : 'غير مدفوع' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Customer Details -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-2 h-full bg-amber-400"></div>
            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fa-solid fa-user text-amber-500"></i> العميل (المستأجر)</h4>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xl">{{ mb_substr($rental->user->name ?? 'ع', 0, 1) }}</div>
                <div>
                    <h5 class="font-bold text-gray-800">{{ $rental->user->name ?? 'غير متوفر' }}</h5>
                    <p class="text-xs font-bold text-gray-500" dir="ltr">{{ $rental->user->phone ?? '---' }}</p>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-50 text-center">
               <a href="{{ route('admin.users.show', $rental->user_id) }}" class="text-amber-600 hover:underline font-bold text-xs">عرض ملف العميل</a>
            </div>
        </div>
    </div>

    <!-- Details Column -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <h4 class="font-bold text-gray-800 mb-5 flex items-center gap-2"><i class="fa-solid fa-motorcycle text-indigo-500"></i> تفاصيل الدباب وموعد الحجز</h4>
            
            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div class="border-l border-gray-200 pl-4 last:border-0 last:pl-0">
                    <i class="fa-regular fa-calendar-check text-gray-400 text-xl mb-2"></i>
                    <p class="text-[10px] font-bold text-gray-500 mb-1">تاريخ الحجز (البداية)</p>
                    <p class="text-xs font-black text-gray-800" dir="ltr">{{ optional($rental->scheduled_at)->format('Y-m-d H:i') ?? '---' }}</p>
                </div>
                <div class="border-l border-gray-200 pl-4 last:border-0 last:pl-0">
                    <i class="fa-solid fa-hourglass-half text-gray-400 text-xl mb-2"></i>
                    <p class="text-[10px] font-bold text-gray-500 mb-1">مدة التأجير</p>
                    <p class="text-xs font-black text-blue-600">{{ $rental->duration_hours }} ساعة</p>
                </div>
                <div class="border-l border-gray-200 pl-4 last:border-0 last:pl-0">
                    <i class="fa-regular fa-calendar-xmark text-gray-400 text-xl mb-2"></i>
                    <p class="text-[10px] font-bold text-gray-500 mb-1">تاريخ الانتهاء</p>
                    <p class="text-xs font-black text-gray-800" dir="ltr">{{ optional($rental->expires_at)->format('Y-m-d H:i') ?? '---' }}</p>
                </div>
                <div>
                   <i class="fa-solid fa-location-crosshairs text-gray-400 text-xl mb-2"></i>
                   <p class="text-[10px] font-bold text-gray-500 mb-1">موقع التسليم</p>
                   <p class="text-xs font-black text-gray-800 truncate" title="{{ $rental->location->address_name ?? 'المركز الرئيسي' }}">{{ $rental->location->address_name ?? 'المركز الرئيسي' }}</p>
                </div>
            </div>
            
        </div>
        
    </div>
</div>
@endsection
