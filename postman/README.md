# Postman Collection - دباب الحارة API

## نظرة عامة
هذا المجلد يحتوي على ملف Postman Collection لاختبار جميع واجهات برمجة التطبيقات (APIs) لتطبيق دباب الحارة.

## الملفات
- `postman_collection.json` - ملف Postman Collection الرئيسي

## كيفية الاستيراد

### في Postman Desktop App:
1. افتح Postman
2. اضغط على **Import** (أو `Ctrl+O`)
3. اختر **File** tab
4. اختر ملف `postman_collection.json`
5. اضغط **Import**

### في Postman Web:
1. افتح Postman في المتصفح
2. اضغط على **Import** في الشريط الجانبي
3. اسحب ملف `postman_collection.json` أو اضغط **Upload Files**
4. اختر الملف واضغط **Import**

## إعداد المتغيرات

بعد الاستيراد، قم بتحديث المتغيرات التالية:

### 1. base_url
- القيمة الافتراضية: `http://localhost:8000/api`
- إذا كان الـ API يعمل على منفذ أو عنوان مختلف، قم بتحديثه

### 2. token
- اتركه فارغاً في البداية
- بعد تسجيل الدخول، انسخ الـ token من الـ response
- ضعه في متغير `token` في Collection Variables

### 3. lang
- القيمة الافتراضية: `ar`
- يمكن تغييرها إلى `en` للغة الإنجليزية

## كيفية استخدام Token

1. قم بتسجيل الدخول باستخدام endpoint **Login - تسجيل الدخول**
2. من الـ response، انسخ قيمة `token`
3. في Postman:
   - اضغط على Collection name
   - اختر **Variables** tab
   - حدّث قيمة `token` بالـ token الذي نسخته
   - جميع الطلبات المحمية ستستخدم هذا الـ token تلقائياً

## البنية

Collection منظمة في 8 مجموعات رئيسية:

1. **Authentication (المصادقة)** - 6 endpoints
2. **Location (الموقع)** - 2 endpoints
3. **Addresses (العناوين)** - 5 endpoints
4. **Orders (الطلبات)** - 8 endpoints
5. **Rentals (الاستئجار)** - 4 endpoints
6. **Deliveries (التوصيل)** - 6 endpoints
7. **Car Wash (غسيل السيارات)** - 5 endpoints
8. **Ratings (التقييمات)** - 2 endpoints

## ملاحظات مهمة

- **OTP للاختبار**: `1234`
- جميع الطلبات تحتوي على معامل `lang` للترجمة
- الطلبات المحمية تتطلب `Authorization: Bearer {{token}}`
- طلبات الاستئجار (Rentals) تستخدم `form-data` لرفع الملفات
- جميع التواريخ بصيغة ISO 8601

## الدعم

للمزيد من المعلومات، راجع ملف `API_DOCUMENTATION.md` في المجلد الرئيسي للمشروع.

