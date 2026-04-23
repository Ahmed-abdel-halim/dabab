# دليل تصميم لوحة التحكم (Advanced Dashboard Design Guide)

هذا الدليل يشرح المبادئ والتنسيقات المستخدمة في مشروع **Smart School**، والتي تعتمد على **Tailwind CSS** و **Alpine.js** للحصول على واجهة عصرية وسريعة الاستجابة.

---

## 1. لوحة الألوان (Color Palette)

تستخدم اللوحة نظام الألوان المتغيرة (CSS Variables) لدعم الوضع الليلي والنهاري بشكل سلس:

| العنصر | اللون (فاتح) | اللون (داكن) | الفئة المستخدمة |
| :--- | :--- | :--- | :--- |
| الأساسي (الرئيسي) | Amber-500 | Sky-500 | `bg-amber-500` / `dark:bg-sky-500` |
| خلفية الصفحة | Gray-50 | Dark Blue (#0d1117) | `bg-gray-50` / `dark:bg-[#0d1117]` |
| البطاقات (Cards) | White (Glass) | Dark Glass | `card-glass` |
| النصوص الرئيسية | Blackish | White | `text-[var(--text-color)]` / `dark:text-white` |

---

## 2. نظام البطاقات الزجاجية (Glassmorphism)

للحصول على المظهر العصري، يتم استخدام كلاس مخصص يسمى `card-glass`:

```css
/* يتم تعريفه في ملف index.css أو داخل style tag */
.card-glass {
    @apply bg-white/80 dark:bg-[#161b22]/80 backdrop-blur-md border border-gray-100 dark:border-white/5;
}
```

---

## 3. تنسيق النوافذ المنبثقة (Modals) - "الأفضل للموبايل"

هذا هو التنسيق الذي قمنا بضبطه لضمان عدم قص المحتوى:

### الهيكل البرمجي (Alpine.js + Tailwind):
```html
<template x-teleport="body"> <!-- 1. النقل للجذر لضمان الظهور -->
    <div x-show="showAddModal" 
         class="fixed inset-0 z-[100] overflow-y-auto bg-black/60 backdrop-blur-sm"> <!-- 2. خلفية ضبابية وزد-اندكس عالي -->
        
        <div class="flex min-h-full items-center justify-center p-4"> <!-- 3. توسيط المحتوى مع بادينج مرن -->
            <div class="bg-white dark:bg-[#161b22] border border-gray-200 dark:border-white/10 w-full max-w-lg rounded-3xl p-6 shadow-2xl scale-up">
                
                <!-- العنوان -->
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">إضافة جديد</h3>
                    <button @click="showModal = false"><i class="fa-solid fa-xmark"></i></button>
                </div>

                <!-- الفورم -->
                <form class="space-y-4"> <!-- 4. مسافات مضغوطة y-4 بدلاً من y-6 -->
                    <div>
                        <label class="block mb-1 text-xs font-bold text-gray-500">اسم الحقل</label>
                        <input type="text" class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-xl px-4 py-2.5">
                    </div>
                    
                    <div class="pt-2 flex space-x-3 space-x-reverse">
                        <button type="submit" class="flex-1 py-3 bg-amber-500 dark:bg-sky-500 text-white rounded-xl font-bold">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
```

---

## 4. تنسيق الجداول (Premium Tables)

تتميز الجداول بكونها بسيطة وواضحة:
- **Header**: خلفية رمادية خفيفة جداً `bg-gray-50` أو `dark:bg-white/5`.
- **Rows**: تأثير Hover عند مرور الماوس `hover:bg-gray-50` وتحديد الخط `divide-y`.
- **Badges**: استخدام خلفية شفافة بنسبة 10% مع نص ملون بالكامل (مثال: `bg-green-500/10 text-green-500`).

---

## 5. الحركات (Animations)

تم استخدام حركات مخصصة لتشعر المستخدم أن النظام "حي":
- **Scale Up**: للمودالات عند الفتح.
- **Fade In**: للعناصر عند التحميل.
- **Translate-Y**: للبطاقات عند الـ Hover.

```css
.scale-up {
    animation: scaleUp 0.3s ease-out forwards;
}

@keyframes scaleUp {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
```

---

## 6. نصائح للتكرار في مشاريع أخرى:
1. **استخدم `x-teleport="body"`**: دائماً للنوافذ المنبثقة لتجنب مشاكل الـ CSS Positioning.
2. **الوضع الداكن**: اعتمد دائماً على كلاس `dark:` في Tailwind بدلاً من كتابة CSS يدوي طويل.
3. **الأيقونات**: استخدم **FontAwesome 6 Pro** للحصول على مظهر احترافي.
4. **الحواف المستديرة**: استخدم `rounded-2xl` أو `rounded-3xl` لتعطي انطباعاً "حديثاً" (Modern Soft UI).
