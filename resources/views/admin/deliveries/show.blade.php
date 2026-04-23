@extends('admin.layouts.app')

@section('title', 'تفاصيل توصيلة #' . $delivery->order_number)

@section('content')

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.deliveries.index') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-blue-600 hover:border-blue-200 transition shadow-sm">
            <i class="fa-solid fa-arrow-right"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-1">طلب توصيل #{{ $delivery->order_number }}</h2>
            <p class="text-sm font-medium text-gray-500" dir="ltr">{{ $delivery->created_at->format('M d, Y - h:i A') }}</p>
        </div>
    </div>
    
    <div class="flex items-center gap-3 bg-white p-2 rounded-xl shadow-sm border border-gray-200">
        @if($delivery->status == 'pending')
            <span class="px-4 py-2 rounded-lg bg-amber-50 text-amber-600 text-sm font-bold border border-amber-200"><i class="fa-solid fa-search-location text-xs ml-1"></i> بانتظار استجابة مندوب</span>
        @elseif($delivery->status == 'in_progress')
            <span class="px-4 py-2 rounded-lg bg-blue-50 text-blue-600 text-sm font-bold border border-blue-200"><i class="fa-solid fa-truck-fast text-xs ml-1"></i> المندوب في الطريق</span>
        @elseif($delivery->status == 'completed' || $delivery->status == 'delivered')
            <span class="px-4 py-2 rounded-lg bg-emerald-50 text-emerald-600 text-sm font-bold border border-emerald-200"><i class="fa-solid fa-check-double text-xs ml-1"></i> تم التوصيل بنجاح</span>
        @elseif($delivery->status == 'cancelled')
            <span class="px-4 py-2 rounded-lg bg-red-50 text-red-600 text-sm font-bold border border-red-200"><i class="fa-solid fa-xmark text-xs ml-1"></i> توصيلة ملغية</span>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Right Sidebar (Overview & People involved) -->
    <div class="space-y-6">
        
        <!-- Financial Summary -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fa-solid fa-file-invoice-dollar text-blue-500"></i> تفاصيل الدفع</h4>
            
            <div class="pt-2 pb-4 border-b border-gray-50 flex justify-between items-center">
                <span class="text-sm font-bold text-gray-500">سعر التوصيلة</span>
                <span class="text-xl font-black text-gray-800">{{ number_format($delivery->delivery_cost, 2) }} <span class="text-sm">SAR</span></span>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-3 text-center">
                <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <p class="text-[10px] text-gray-400 font-bold mb-1">طريقة الدفع</p>
                    <p class="text-xs font-bold text-gray-700">
                        @if($delivery->payment_method == 'cash') الدفع نقداً @elseif($delivery->payment_method == 'apple_pay') Apple Pay @else {{ $delivery->payment_method }} @endif
                    </p>
                </div>
                <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <p class="text-[10px] text-gray-400 font-bold mb-1">حالة الدفع</p>
                    <p class="text-xs font-bold {{ $delivery->payment_status == 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $delivery->payment_status == 'paid' ? 'مدفوع' : 'غير مدفوع' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Customer Details -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fa-solid fa-user text-amber-500"></i> طالب التوصيلة (المرسل)</h4>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center font-bold text-xl">{{ mb_substr($delivery->user->name ?? 'ع', 0, 1) }}</div>
                <div>
                    <h5 class="font-bold text-gray-800">{{ $delivery->user->name ?? 'غير متوفر' }}</h5>
                    <p class="text-xs font-bold text-gray-500" dir="ltr">{{ $delivery->user->phone ?? '---' }}</p>
                </div>
            </div>
        </div>

        <!-- Delivery Agent -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden">
            @if($delivery->agent)
                <div class="absolute top-0 right-0 w-2 h-full bg-blue-500"></div>
            @endif
            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fa-solid fa-motorcycle text-blue-500"></i> المندوب</h4>
            @if($delivery->agent)
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xl"><i class="fa-solid fa-helmet-safety"></i></div>
                    <div>
                        <h5 class="font-bold text-gray-800">{{ $delivery->agent->name }}</h5>
                        <p class="text-xs font-bold text-gray-500" dir="ltr">{{ $delivery->agent->phone ?? '---' }}</p>
                    </div>
                </div>
                <!-- Mini actions for assigned agent -->
                <div class="mt-4 pt-4 border-t border-gray-50">
                    <a href="{{ route('admin.agents.show', $delivery->delivery_agent_id) }}" class="text-xs font-bold text-blue-600 hover:underline">عرض ملف المندوب <i class="fa-solid fa-arrow-left mr-1"></i></a>
                </div>
            @else
                <div class="bg-gray-50 p-4 rounded-xl border border-dashed border-gray-200 text-center">
                    <div class="w-12 h-12 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center text-xl mx-auto mb-2"><i class="fa-solid fa-search"></i></div>
                    <p class="text-sm font-bold text-gray-500">في انتظار قبول مندوب للتوصيلة</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Map & Details Column -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <h4 class="font-bold text-gray-800 mb-6 flex items-center gap-2"><i class="fa-solid fa-route text-indigo-500"></i> مسار التوصيل</h4>
            
            <div class="relative px-6">
                <!-- Vertical Line -->
                <div class="absolute top-4 bottom-4 right-8 w-1 bg-gray-100 rounded-full"></div>
                
                <!-- Start Point -->
                <div class="relative z-10 flex gap-4 mb-10">
                    <div class="w-5 h-5 rounded-full border-4 border-white bg-emerald-500 shadow-md shrink-0"></div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 mb-1">نقطة الاستلام (المرسل)</p>
                        <p class="text-sm font-bold text-gray-800 bg-gray-50 p-3 rounded-xl border border-gray-100">{{ $delivery->sender_address ?? 'لا يوجد عنوان' }}</p>
                        <div class="mt-2 text-xs font-bold text-gray-500 flex items-center gap-2" dir="ltr">
                            <i class="fa-solid fa-phone text-gray-400"></i> {{ $delivery->sender_phone ?? $delivery->user->phone ?? '---' }}
                        </div>
                    </div>
                </div>
                
                <!-- End Point -->
                <div class="relative z-10 flex gap-4">
                    <div class="w-5 h-5 rounded-full border-4 border-white bg-red-500 shadow-md shrink-0"></div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 mb-1">وجهة التوصيل (المستلم)</p>
                        <p class="text-sm font-bold text-gray-800 bg-gray-50 p-3 rounded-xl border border-gray-100">{{ $delivery->recipient_address ?? 'لا يوجد عنوان' }}</p>
                        <div class="mt-2 text-xs font-bold text-gray-500 flex items-center gap-2" dir="ltr">
                            <i class="fa-solid fa-phone text-gray-400"></i> {{ $delivery->recipient_phone ?? '---' }}
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <h4 class="font-bold text-gray-800 mb-5 flex items-center gap-2"><i class="fa-solid fa-box text-orange-500"></i> تفاصيل الطرد</h4>
            
            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 mb-6 flex gap-4 items-start">
                <div class="w-12 h-12 rounded-xl bg-white border border-gray-200 flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-box-open text-orange-400 text-xl"></i>
                </div>
                <div>
                    <h5 class="text-xs font-bold text-gray-500 mb-1">وصف الطرد المُدخل</h5>
                    <p class="text-sm font-bold text-gray-800 leading-snug">{{ $delivery->shipment_details ?? 'لم يُدخل تفاصيل إضافية' }}</p>
                </div>
            </div>
            
            <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 text-sm"><i class="fa-solid fa-camera text-gray-400"></i> صور متعلقة</h4>
            <div class="flex gap-4">
                @if($delivery->item_photo)
                    <div class="relative group w-32 h-32 rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                        <img src="{{ Storage::url($delivery->item_photo) }}" class="w-full h-full object-cover">
                        <a href="{{ Storage::url($delivery->item_photo) }}" target="_blank" class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex flex-col items-center justify-center text-white text-xs font-bold"><i class="fa-solid fa-eye text-lg mb-1"></i> صورة الطرد</a>
                    </div>
                @else
                    <div class="w-32 h-32 rounded-xl border border-dashed border-gray-200 flex flex-col items-center justify-center text-gray-400 text-xs font-bold">
                        <i class="fa-solid fa-image text-2xl text-gray-300 mb-1"></i>
                        لا توجد صورة
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
