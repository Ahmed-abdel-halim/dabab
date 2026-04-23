@extends('admin.layouts.app')

@section('title', 'تفاصيل العميل: ' . $user->name)

@section('content')

<div class="mb-8 flex items-center justify-between">
    <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-amber-500 dark:hover:text-sky-500 font-bold flex items-center gap-2 transition bg-white/50 dark:bg-black/20 px-4 py-2 rounded-xl border border-gray-100 dark:border-white/5 backdrop-blur-md w-max">
        <i class="fa-solid fa-arrow-right"></i> العودة للمستخدمين
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <!-- User Card -->
    <div class="md:col-span-1 border border-gray-100 dark:border-white/5 bg-white/80 dark:bg-[#161b22]/80 backdrop-blur-md p-8 rounded-3xl shadow-sm text-center scale-up">
        <div class="w-28 h-28 mx-auto rounded-[2rem] bg-gradient-to-tr {{ $user->role == 'delivery' ? 'from-blue-400 to-blue-600 shadow-blue-500/30' : 'from-amber-400 to-amber-600 dark:from-sky-500 dark:to-sky-700 dark:shadow-sky-500/30 shadow-amber-500/30' }} text-white flex items-center justify-center font-black text-5xl shadow-xl transform transition-transform hover:rotate-3 hover:scale-105 mb-6">
            {{ mb_substr($user->name, 0, 1) }}
        </div>
        
        <h2 class="text-2xl font-black text-gray-800 dark:text-white mb-2">{{ $user->name }}</h2>
        @if($user->role == 'delivery')
            <span class="inline-block mt-1 bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 px-4 py-1.5 rounded-xl text-xs font-bold border border-blue-100 dark:border-blue-500/20 shadow-sm"><i class="fa-solid fa-motorcycle ml-1"></i> مندوب توصيل</span>
        @else
            <span class="inline-block mt-1 bg-gray-50 dark:bg-white/5 text-gray-500 dark:text-gray-300 px-4 py-1.5 rounded-xl text-xs font-bold border border-gray-100 dark:border-white/5 shadow-sm"><i class="fa-regular fa-user ml-1"></i> مستخدم تطبيق</span>
        @endif

        <div class="mt-8 space-y-5 text-right border-t border-gray-100 dark:border-white/5 pt-8">
            <div class="flex items-center gap-4 text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-black/20 p-3 rounded-2xl">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-sky-500/10 text-amber-500 dark:text-sky-500 flex items-center justify-center shadow-sm shrink-0">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <span class="font-bold text-sm">{{ $user->email }}</span>
            </div>
            
            <div class="flex items-center gap-4 text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-black/20 p-3 rounded-2xl justify-end" dir="ltr">
                <span class="font-bold text-sm">{{ $user->phone ?? 'لا يوجد هاتف' }}</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-sky-500/10 text-amber-500 dark:text-sky-500 flex items-center justify-center shadow-sm shrink-0">
                    <i class="fa-solid fa-phone"></i>
                </div>
            </div>
            
            <div class="flex items-center gap-4 text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-black/20 p-3 rounded-2xl">
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-sky-500/10 text-amber-500 dark:text-sky-500 flex items-center justify-center shadow-sm shrink-0">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
                <div>
                    <span class="block text-[10px] text-gray-400 font-bold mb-0.5">تاريخ الانضمام</span>
                    <span class="font-bold text-sm" dir="ltr">{{ $user->created_at->format('Y-M-d h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="md:col-span-2 space-y-6">
        <div class="grid grid-cols-2 gap-6">
            <div class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20 p-8 rounded-3xl scale-up relative overflow-hidden" style="animation-delay: 0.1s;">
                <i class="fa-solid fa-wallet absolute -left-6 -bottom-6 text-emerald-500/10 text-8xl -rotate-12"></i>
                <p class="text-emerald-700 dark:text-emerald-500 text-sm font-bold mb-3 relative z-10">الرصيد في المحفظة</p>
                <h3 class="text-4xl font-black text-emerald-600 dark:text-emerald-400 relative z-10">{{ number_format($user->wallet_balance ?? 0, 2) }} <span class="text-sm font-bold opacity-70">SAR</span></h3>
            </div>
            
            <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20 p-8 rounded-3xl scale-up relative overflow-hidden" style="animation-delay: 0.2s;">
                <i class="fa-solid fa-box-open absolute -left-6 -bottom-6 text-blue-500/10 text-8xl -rotate-12"></i>
                <p class="text-blue-700 dark:text-blue-500 text-sm font-bold mb-3 relative z-10">الطلبات / الخدمات</p>
                <h3 class="text-4xl font-black text-blue-600 dark:text-blue-400 relative z-10">{{ $user->orders_count + $user->deliveries_count }} <span class="text-sm font-bold opacity-70 tracking-widest leading-loose">طلبات</span></h3>
            </div>
        </div>
        
        <div class="bg-white/80 dark:bg-[#161b22]/80 backdrop-blur-md border border-red-100 dark:border-red-500/10 p-8 rounded-3xl scale-up mt-4 shadow-sm relative overflow-hidden" style="animation-delay: 0.3s;">
             <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-red-500 to-red-400"></div>
             <h3 class="text-red-500 font-black text-lg mb-2 flex items-center gap-3"><i class="fa-solid fa-shield-halved text-2xl"></i> منطقة محظورة (Danger Zone)</h3>
             <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 font-bold">هذه الإجراءات تؤثر على حساب العميل بشكل نهائي، استخدمها بحذر شديد.</p>
             <button class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-600 dark:text-red-500 hover:bg-red-500 hover:text-white dark:hover:bg-red-500 dark:hover:text-white font-bold py-3 px-8 rounded-xl transition-all duration-300 shadow-sm flex items-center gap-2">
                 <i class="fa-solid fa-ban"></i> حظر وإيقاف المستخدم
             </button>
         </div>
    </div>
</div>
@endsection
