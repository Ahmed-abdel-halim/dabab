@extends('admin.layouts.app')

@section('title', 'مراجعة ملف المندوب: ' . ($agent->user->name ?? 'غير متوفر'))

@section('content')

<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.agents.index') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-500 hover:text-blue-600 hover:border-blue-200 transition shadow-sm">
            <i class="fa-solid fa-arrow-right"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-1">ملف المندوب</h2>
            <p class="text-sm font-medium text-gray-500">معلومات الحساب، المركبة، والبنك والمستندات</p>
        </div>
    </div>
    
    <div class="flex items-center gap-3 bg-white p-2 rounded-xl shadow-sm border border-gray-200">
        @if($agent->status == 'pending')
            <span class="px-4 py-2 rounded-lg bg-amber-50 text-amber-600 text-sm font-bold border border-amber-200">
                <i class="fa-solid fa-clock text-xs ml-1"></i> بانتظار الموافقة
            </span>
        @elseif($agent->status == 'active')
            <span class="px-4 py-2 rounded-lg bg-emerald-50 text-emerald-600 text-sm font-bold border border-emerald-200">
                <i class="fa-solid fa-check text-xs ml-1"></i> حساب نشط
            </span>
        @else
            <span class="px-4 py-2 rounded-lg bg-red-50 text-red-600 text-sm font-bold border border-red-200">
                <i class="fa-solid fa-xmark text-xs ml-1"></i> {{ $agent->status == 'rejected' ? 'مرفوض' : 'موقوف' }}
            </span>
        @endif
    </div>
</div>

@if(session('success'))
<div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-xl font-bold flex items-center gap-3">
    <i class="fa-solid fa-circle-check text-xl"></i>
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Right Sidebar (Profile Card & Actions) -->
    <div class="space-y-6">
        
        <!-- Personal Info Card -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 text-center">
            <div class="w-24 h-24 rounded-full mx-auto mb-4 bg-gradient-to-tr from-blue-500 to-indigo-600 shadow-lg text-white flex items-center justify-center text-4xl font-black">
                {{ mb_substr($agent->user->name ?? 'م', 0, 1) }}
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $agent->user->name ?? 'غير متوفر' }}</h3>
            <p class="text-gray-500 font-medium text-sm mb-4" dir="ltr">{{ $agent->user->phone ?? '---' }}</p>
            
            <div class="grid grid-cols-2 gap-3 text-right">
                <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <p class="text-[10px] uppercase text-gray-400 font-bold mb-1">الخدمة</p>
                    <p class="text-sm font-bold text-gray-700">{{ $agent->working_service ?? 'غير محدد' }}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <p class="text-[10px] uppercase text-gray-400 font-bold mb-1">الفئة</p>
                    <p class="text-sm font-bold text-gray-700">{{ $agent->service_category ?? 'غير محدد' }}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <p class="text-[10px] uppercase text-gray-400 font-bold mb-1">الجنسية</p>
                    <p class="text-sm font-bold text-gray-700">{{ $agent->nationality ?? 'غير محدد' }}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                    <p class="text-[10px] uppercase text-gray-400 font-bold mb-1">تاريخ الميلاد</p>
                    <p class="text-sm font-bold text-gray-700" dir="ltr">{{ $agent->birth_date ? $agent->birth_date->format('Y-m-d') : '---' }}</p>
                </div>
            </div>
            
            <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 mt-3 text-right">
                <p class="text-[10px] uppercase text-gray-400 font-bold mb-1">رقم الهوية الوطنية / الإقامة</p>
                <p class="text-base font-black text-gray-800 tracking-wider" dir="ltr">{{ $agent->national_id_number ?? '---' }}</p>
            </div>
        </div>

        <!-- Approval Action Box -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fa-solid fa-gavel text-amber-500"></i> قرار الإدارة</h4>
            
            <form action="{{ route('admin.agents.status', $agent->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-600 mb-2">تحديث الحالة:</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="active" class="peer sr-only" {{ $agent->status == 'active' ? 'checked' : '' }}>
                            <div class="py-2.5 text-center text-sm font-bold text-gray-500 border border-gray-200 rounded-xl peer-checked:bg-emerald-50 peer-checked:text-emerald-600 peer-checked:border-emerald-300 transition">تنشيط / قبول</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="status" value="rejected" class="peer sr-only" {{ $agent->status == 'rejected' ? 'checked' : '' }}>
                            <div class="py-2.5 text-center text-sm font-bold text-gray-500 border border-gray-200 rounded-xl peer-checked:bg-red-50 peer-checked:text-red-600 peer-checked:border-red-300 transition">رفض</div>
                        </label>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-600 mb-2">ملاحظات إدارية (اختياري)</label>
                    <textarea name="admin_comment" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition">{{ $agent->admin_comment }}</textarea>
                </div>
                
                <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white font-bold py-3 px-4 rounded-xl transition shadow-sm">
                    حفظ القرار
                </button>
            </form>
        </div>
    </div>

    <!-- Details Column (Vehicle, Bank, Docs) -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Vehicle Bank Details Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Vehicle Info -->
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <h4 class="font-bold text-gray-800 mb-5 flex items-center gap-2"><i class="fa-solid fa-car text-blue-500"></i> تفاصيل المركبة</h4>
                @if($agent->vehicle)
                    <div class="space-y-4">
                        <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                            <span class="text-sm font-bold text-gray-500">نوع المركبة</span>
                            <span class="text-sm font-bold text-gray-800">{{ $agent->vehicle->vehicle_type ?? '---' }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                            <span class="text-sm font-bold text-gray-500">رقم اللوحة</span>
                            <span class="text-sm font-black text-gray-800 border border-gray-200 px-3 py-1 rounded bg-gray-50" dir="ltr">{{ $agent->vehicle->plate_number ?? '---' }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-gray-50 pb-3">
                            <span class="text-sm font-bold text-gray-500">رقم التسلسل / الاستمارة</span>
                            <span class="text-sm font-bold text-gray-800" dir="ltr">{{ $agent->vehicle->sequence_number ?? '---' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-bold text-gray-500">موديل / لون</span>
                            <span class="text-sm font-bold text-gray-800">{{ $agent->vehicle->vehicle_model ?? '---' }} / {{ $agent->vehicle->vehicle_color ?? '---' }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-6">
                        <i class="fa-solid fa-triangle-exclamation text-3xl text-gray-300 mb-2"></i>
                        <p class="text-sm text-gray-500 font-bold">المندوب لم يدخل تفاصيل المركبة</p>
                    </div>
                @endif
            </div>

            <!-- Bank Details -->
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <h4 class="font-bold text-gray-800 mb-5 flex items-center gap-2"><i class="fa-solid fa-building-columns text-emerald-500"></i> بيانات الحساب البنكي</h4>
                @if($agent->bankDetails)
                    <div class="space-y-4">
                        <div class="flex flex-col gap-1 border-b border-gray-50 pb-3">
                            <span class="text-xs font-bold text-gray-500">اسم صاحب الحساب</span>
                            <span class="text-sm font-black text-gray-800">{{ $agent->bankDetails->account_name ?? '---' }}</span>
                        </div>
                        <div class="flex flex-col gap-1 border-b border-gray-50 pb-3">
                            <span class="text-xs font-bold text-gray-500">رقم الحساب أو STC Pay</span>
                            <span class="text-sm font-bold text-gray-800" dir="ltr">{{ $agent->bankDetails->stc_pay_number ?? '---' }}</span>
                        </div>
                        <div class="flex flex-col gap-1 border-b border-gray-50 pb-3">
                            <span class="text-xs font-bold text-gray-500">اسم البنك</span>
                            <span class="text-sm font-bold text-gray-800">{{ $agent->bankDetails->bank_name ?? '---' }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-bold text-gray-500">الآيبان (IBAN)</span>
                            <span class="text-sm font-black text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100 mt-1" dir="ltr">{{ $agent->bankDetails->iban ?? '---' }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-6">
                        <i class="fa-solid fa-money-check-dollar text-3xl text-gray-300 mb-2"></i>
                        <p class="text-sm text-gray-500 font-bold">لم يضف الحساب البنكي بعد</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Documents Gallery -->
        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <h4 class="font-bold text-gray-800 mb-5 flex items-center gap-2"><i class="fa-solid fa-images text-indigo-500"></i> المستندات المرفقة</h4>
            @if($agent->documents && $agent->documents->count() > 0)
                <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($agent->documents as $doc)
                        <div class="border border-gray-200 rounded-2xl overflow-hidden group">
                           <div class="bg-gray-100 h-32 relative flex items-center justify-center">
                                @if(Str::endsWith($doc->file_path, ['.pdf']))
                                    <i class="fa-solid fa-file-pdf text-4xl text-red-400"></i>
                                @else
                                    <img src="{{ Storage::url($doc->file_path) }}" alt="{{ $doc->document_type }}" class="w-full h-full object-cover">
                                @endif
                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white backdrop-blur-sm">
                                    <i class="fa-solid fa-up-right-from-square text-2xl"></i>
                                </a>
                           </div>
                           <div class="p-3 text-center bg-gray-50">
                                <span class="text-xs font-bold text-gray-700">
                                    {{ Str::replace('_', ' ', $doc->document_type) }}
                                </span>
                           </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 border-2 border-dashed border-gray-200 rounded-2xl">
                    <i class="fa-regular fa-file-image text-4xl text-gray-300 mb-3"></i>
                    <p class="text-sm text-gray-500 font-bold">لا يوجد مستندات مرفوعة</p>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
