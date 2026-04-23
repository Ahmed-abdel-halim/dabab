@extends('admin.layouts.app')

@section('title', 'تفاصيل الطلب #' . $order->order_number)

@section('content')

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.orders.index') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-amber-600 hover:border-amber-200 transition shadow-sm">
            <i class="fa-solid fa-arrow-right"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-1">الطلب #{{ $order->order_number }}</h2>
            <p class="text-sm font-medium text-gray-500" dir="ltr">{{ $order->created_at->format('M d, Y - h:i A') }}</p>
        </div>
    </div>
    
    <div class="flex items-center gap-3 bg-white p-2 rounded-xl shadow-sm border border-gray-200">
        @if($order->status == 'pending')
            <span class="px-4 py-2 rounded-lg bg-amber-50 text-amber-600 text-sm font-bold border border-amber-200"><i class="fa-solid fa-clock text-xs ml-1"></i> بانتظار التأكيد</span>
        @elseif($order->status == 'confirmed')
            <span class="px-4 py-2 rounded-lg bg-indigo-50 text-indigo-600 text-sm font-bold border border-indigo-200"><i class="fa-solid fa-clipboard-check text-xs ml-1"></i> مؤكد</span>
        @elseif($order->status == 'in_progress')
            <span class="px-4 py-2 rounded-lg bg-blue-50 text-blue-600 text-sm font-bold border border-blue-200"><i class="fa-solid fa-truck-fast text-xs ml-1"></i> في الطريق</span>
        @elseif($order->status == 'completed' || $order->status == 'delivered')
            <span class="px-4 py-2 rounded-lg bg-emerald-50 text-emerald-600 text-sm font-bold border border-emerald-200"><i class="fa-solid fa-check-double text-xs ml-1"></i> مكتمل</span>
        @elseif($order->status == 'cancelled')
            <span class="px-4 py-2 rounded-lg bg-red-50 text-red-600 text-sm font-bold border border-red-200"><i class="fa-solid fa-xmark text-xs ml-1"></i> ملغي</span>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Right Sidebar (Overview & People involved) -->
    <div class="space-y-6">
        
        <!-- Financial Summary -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fa-solid fa-receipt text-amber-500"></i> الملخص المالي</h4>
            
            <div class="space-y-3 pb-4 border-b border-gray-50">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-bold text-gray-500">قيمة المنتجات</span>
                    <span class="text-sm font-bold text-gray-800">{{ number_format($order->total_cost - $order->delivery_cost, 2) }} SAR</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-bold text-gray-500">رسوم التوصيل</span>
                    <span class="text-sm font-bold text-gray-800">{{ number_format($order->delivery_cost, 2) }} SAR</span>
                </div>
            </div>
            <div class="pt-4 flex justify-between items-center">
                <span class="text-base font-black text-gray-800">الإجمالي</span>
                <span class="text-xl font-black text-emerald-600">{{ number_format($order->total_cost, 2) }} <span class="text-sm">SAR</span></span>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-50 grid grid-cols-2 gap-3 text-center">
                <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <p class="text-[10px] text-gray-400 font-bold mb-1">طريقة الدفع</p>
                    <p class="text-xs font-bold text-gray-700">
                        @if($order->payment_method == 'cash') الدفع نقداً @elseif($order->payment_method == 'apple_pay') Apple Pay @else {{ $order->payment_method }} @endif
                    </p>
                </div>
                <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <p class="text-[10px] text-gray-400 font-bold mb-1">حالة الدفع</p>
                    <p class="text-xs font-bold {{ $order->payment_status == 'paid' ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $order->payment_status == 'paid' ? 'مدفوع' : 'غير مدفوع' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Customer Details -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-2 h-full bg-amber-400"></div>
            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fa-solid fa-user text-amber-500"></i> بيانات العميل</h4>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-xl">{{ mb_substr($order->user->name ?? 'ع', 0, 1) }}</div>
                <div>
                    <h5 class="font-bold text-gray-800">{{ $order->user->name ?? 'غير متوفر' }}</h5>
                    <p class="text-xs font-bold text-gray-500" dir="ltr">{{ $order->user->phone ?? '---' }}</p>
                </div>
            </div>
            @if($order->location)
            <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 flex items-start gap-3">
                <i class="fa-solid fa-map-location-dot text-amber-500 mt-1"></i>
                <div>
                    <p class="text-xs font-bold text-gray-800 mb-1">{{ $order->location->address_name ?? 'العنوان' }}</p>
                    <p class="text-[10px] font-medium text-gray-500 leading-snug">{{ $order->location->description ?? 'لا يوجد وصف للعنوان' }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Delivery Agent -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-2 h-full bg-blue-500"></div>
            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fa-solid fa-motorcycle text-blue-500"></i> المندوب</h4>
            @if($order->agent)
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xl"><i class="fa-solid fa-helmet-safety"></i></div>
                    <div>
                        <h5 class="font-bold text-gray-800">{{ $order->agent->name }}</h5>
                        <p class="text-xs font-bold text-gray-500" dir="ltr">{{ $order->agent->phone ?? '---' }}</p>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 p-4 rounded-xl border border-dashed border-gray-200 text-center">
                    <i class="fa-solid fa-user-slash text-2xl text-gray-300 mb-2"></i>
                    <p class="text-sm font-bold text-gray-500">لم يتم تعيين مندوب لهذا الطلب بعد</p>
                    <button class="mt-3 bg-white border border-gray-200 text-blue-600 hover:bg-blue-50 font-bold text-xs py-2 px-4 rounded-lg shadow-sm transition">تعيين مندوب يدوياً</button>
                </div>
            @endif
        </div>
    </div>

    <!-- Items List -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <h4 class="font-bold text-gray-800 mb-5 flex items-center gap-2"><i class="fa-solid fa-basket-shopping text-emerald-500"></i> محتويات الطلب</h4>
            
            @if($order->items && $order->items->count() > 0)
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-100 rounded-2xl hover:bg-white hover:shadow-md transition">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 bg-white rounded-xl border border-gray-100 flex items-center justify-center shadow-sm overflow-hidden p-2">
                                    @if($item->item_photo)
                                        <img src="{{ Storage::url($item->item_photo) }}" alt="{{ $item->item_name }}" class="w-full h-full object-contain">
                                    @else
                                        <i class="fa-solid fa-box text-gray-300 text-xl"></i>
                                    @endif
                                </div>
                                <div>
                                    <h5 class="font-bold text-gray-800 mb-1">{{ $item->item_name }}</h5>
                                    @if($item->special_instructions)
                                        <p class="text-[11px] font-medium text-gray-500 bg-amber-50 px-2 py-0.5 rounded text-amber-700 w-max"><i class="fa-regular fa-comment-dots"></i> {{ $item->special_instructions }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-center">
                                <span class="bg-gray-200 text-gray-700 font-bold text-xs px-3 py-1 rounded-full">{{ $item->quantity }}x</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                    <i class="fa-regular fa-file-lines text-4xl text-gray-300 mb-3"></i>
                    <p class="text-sm font-bold text-gray-500">لا توجد منتجات مسجلة (قد يكون طلب خدمات أخرى)</p>
                </div>
            @endif
            
            <div class="mt-8">
                <h4 class="font-bold text-gray-800 mb-3 flex items-center gap-2 text-sm"><i class="fa-solid fa-camera"></i> المرفقات</h4>
                <div class="flex gap-4">
                    @if($order->item_photo)
                        <div class="relative group w-32 h-32 rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                            <img src="{{ Storage::url($order->item_photo) }}" class="w-full h-full object-cover">
                            <a href="{{ Storage::url($order->order_photo) }}" target="_blank" class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex flex-col items-center justify-center text-white text-xs font-bold"><i class="fa-solid fa-eye text-lg mb-1"></i> صورة الطلب</a>
                        </div>
                    @endif
                    @if($order->invoice_photo)
                        <div class="relative group w-32 h-32 rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                            <img src="{{ Storage::url($order->invoice_photo) }}" class="w-full h-full object-cover">
                            <a href="{{ Storage::url($order->invoice_photo) }}" target="_blank" class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex flex-col items-center justify-center text-white text-xs font-bold"><i class="fa-solid fa-receipt text-lg mb-1"></i> صورة الفاتورة</a>
                        </div>
                    @endif
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection
