<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل دخول الإدارة</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .scale-up {
            animation: scaleUp 0.3s ease-out forwards;
        }
        @keyframes scaleUp {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-[#0d1117] flex items-center justify-center min-h-screen font-sans">

    <div class="bg-white dark:bg-[#161b22] border border-gray-100 dark:border-white/5 p-8 rounded-3xl shadow-xl w-full max-w-md scale-up">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-tr from-amber-400 to-amber-600 rounded-2xl mx-auto flex items-center justify-center text-white text-3xl font-bold mb-4 shadow-lg">
                D
            </div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">لوحة تحكم دباب</h2>
            <p class="text-gray-500 text-sm mt-1">قم بتسجيل الدخول للمتابعة</p>
        </div>

        @if($errors->any())
            <div class="bg-red-50 dark:bg-red-500/10 text-red-500 p-4 rounded-xl mb-6 text-sm font-bold border border-red-100 dark:border-red-500/20 text-center">
                بيانات الدخول غير صحيحة
            </div>
        @endif

        <form action="{{ route('admin.login.submit') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block mb-2 text-sm font-bold text-gray-500">البريد الإلكتروني</label>
                <input type="email" name="email" required class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3 text-gray-800 dark:text-white focus:outline-none focus:border-amber-500 dark:focus:border-sky-500 transition">
            </div>
            <div>
                <label class="block mb-2 text-sm font-bold text-gray-500">كلمة المرور</label>
                <input type="password" name="password" required class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-3 text-gray-800 dark:text-white focus:outline-none focus:border-amber-500 dark:focus:border-sky-500 transition">
            </div>
            <div class="pt-2">
                <button type="submit" class="w-full py-3 bg-amber-500 dark:bg-sky-500 hover:bg-amber-600 dark:hover:bg-sky-600 text-white rounded-xl font-bold transition shadow-md hover:shadow-lg">
                    دخول
                </button>
            </div>
        </form>
    </div>

</body>
</html>
