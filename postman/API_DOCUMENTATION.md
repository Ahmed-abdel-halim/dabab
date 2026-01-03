# دليل API لتطبيق دباب الحارة

## نظرة عامة
هذا الدليل يشرح جميع واجهات برمجة التطبيقات (APIs) المتاحة في تطبيق دباب الحارة.

## Base URL
```
http://localhost:8000/api
```

## المصادقة (Authentication)
جميع الطلبات المحمية تتطلب token في header:
```
Authorization: Bearer {token}
```

---

## 1. المصادقة والتسجيل (Authentication)

### إرسال كود التحقق
```
POST /v1/send-otp
```
**Body:**
```json
{
    "phone": "966501234567",
    "type": "registration" // أو "login"
}
```

### التحقق من كود OTP
```
POST /v1/verify-otp
```
**Body:**
```json
{
    "phone": "966501234567",
    "code": "1234"
}
```

### إكمال التسجيل
```
POST /v1/complete-profile
```
**Body:**
```json
{
    "temp_token": "token_from_verify_otp",
    "name": "محمد أحمد",
    "email": "mohammed@example.com"
}
```

### تسجيل الدخول
```
POST /v1/login
```
**Body:**
```json
{
    "phone": "966501234567",
    "code": "1234"
}
```

### تسجيل الخروج
```
POST /v1/logout
```
**Headers:** `Authorization: Bearer {token}`

### الحصول على الملف الشخصي
```
GET /v1/profile
```
**Headers:** `Authorization: Bearer {token}`

---

## 2. الموقع (Location)

### تحديث الموقع
```
POST /v1/complete-location
```
**Body:**
```json
{
    "lat": 24.7136,
    "lng": 46.6753,
    "address": "شارع الأميرة نورة بنت عبد الرحمن بن فيصل",
    "type": "home", // home, work, friend, other (اختياري)
    "is_default": true // (اختياري)
}
```

### الحصول على الموقع
```
GET /v1/mylocation
```

---

## 3. العناوين (Addresses)

### الحصول على جميع العناوين
```
GET /v1/addresses
```

### إنشاء عنوان جديد
```
POST /v1/addresses
```
**Body:**
```json
{
    "type": "home", // home, work, friend, other
    "address": "الرياض حي أم الحمام شارع أم الحمام",
    "lat": 24.7136,
    "lng": 46.6753,
    "is_default": true
}
```

### الحصول على عنوان محدد
```
GET /v1/addresses/{id}
```

### تحديث عنوان
```
PUT /v1/addresses/{id}
```

### حذف عنوان
```
DELETE /v1/addresses/{id}
```

---

## 4. الطلبات (Orders)

### الحصول على فئات الطلبات
```
GET /v1/orders/categories
```

### الحصول على طلباتي
```
GET /v1/orders?status=all
```
**Query Parameters:**
- `status`: all, pending, confirmed, in_progress, completed, cancelled

### إنشاء طلب جديد
```
POST /v1/orders
```
**Body:**
```json
{
    "category_id": 1,
    "details": "2 قارورة , زجاجة زيت , صدور دجاج 20 قطعة",
    "delivery_cost": 5,
    "scheduled_at": null, // أو "2025-12-15 14:00:00"
    "location_id": 1, // ID من جدول user_locations
    "payment_method": "cash" // cash, apple_pay, bank_card
}
```

### الحصول على طلب محدد
```
GET /v1/orders/{id}
```

### تحديث طلب
```
PUT /v1/orders/{id}
```
**ملاحظة:** يمكن تحديث الطلبات في حالة `pending` فقط

### إلغاء طلب
```
POST /v1/orders/{id}/cancel
```

### تتبع طلب
```
GET /v1/orders/{id}/track
```

### تأكيد طلب
```
POST /v1/orders/{id}/confirm
```
**Body:**
```json
{
    "payment_method": "cash"
}
```

---

## 5. الاستئجار (Rentals)

### الحصول على طلبات الاستئجار
```
GET /v1/rentals
```

### إنشاء طلب استئجار
```
POST /v1/rentals
```
**Body (form-data):**
- `personal_name`: "نادية أحمد"
- `commercial_name`: "صيدلية الامرية"
- `store_type`: "صيدلية"
- `rental_type`: "scooter_only" أو "scooter_with_driver"
- `commercial_registration_file`: ملف (PDF, JPG, PNG)
- `additional_details`: "تفاصيل إضافية"

### الحصول على طلب استئجار محدد
```
GET /v1/rentals/{id}
```

### تحديث طلب استئجار
```
PUT /v1/rentals/{id}
```

---

## 6. التوصيل (Deliveries)

### الحصول على طلبات التوصيل
```
GET /v1/deliveries?status=all
```

### إنشاء طلب توصيل
```
POST /v1/deliveries
```
**Body:**
```json
{
    "shipment_details": "صندوق وزن 6 كيلو يحتوى على تمر",
    "sender_address": "شارع الأمرية نورة بنت عبد الرحمن بن فيصل",
    "sender_lat": 24.7136,
    "sender_lng": 46.6753,
    "sender_phone": "966567891023",
    "recipient_address": "شارع الأمرية نورة بنت عبد الرحمن بن فيصل",
    "recipient_lat": 24.7236,
    "recipient_lng": 46.6853,
    "recipient_phone": "966567891024",
    "payment_method": "cash"
}
```

### الحصول على طلب توصيل محدد
```
GET /v1/deliveries/{id}
```

### تحديث طلب توصيل
```
PUT /v1/deliveries/{id}
```

### إلغاء طلب توصيل
```
POST /v1/deliveries/{id}/cancel
```

### تتبع طلب توصيل
```
GET /v1/deliveries/{id}/track
```

---

## 7. غسيل السيارات (Car Wash)

### الحصول على مواعيد الغسيل
```
GET /v1/car-washes?status=all
```

### حجز موعد غسيل
```
POST /v1/car-washes
```
**Body:**
```json
{
    "car_size": "large", // small, large
    "wash_type": "interior_exterior", // interior_exterior, exterior, interior
    "scheduled_date": "2025-12-13",
    "scheduled_time": "11:00",
    "time_period": "before_lunch", // before_lunch, early_evening, dinner_time, late_night
    "location_id": 1 // ID من جدول user_locations
}
```

### الحصول على موعد غسيل محدد
```
GET /v1/car-washes/{id}
```

### تحديث موعد غسيل
```
PUT /v1/car-washes/{id}
```

### إلغاء موعد غسيل
```
POST /v1/car-washes/{id}/cancel
```

---

## 8. التقييمات (Ratings)

### الحصول على تقييماتي
```
GET /v1/ratings
```

### إضافة تقييم
```
POST /v1/ratings
```
**Body:**
```json
{
    "order_id": 1, // أو "delivery_id": 1
    "rating": 5, // 1-5
    "comment": "خدمة ممتازة"
}
```

---

## حالات الطلبات (Status)

### الطلبات (Orders)
- `pending`: قيد الانتظار
- `confirmed`: مؤكد
- `in_progress`: قيد التنفيذ
- `completed`: مكتمل
- `cancelled`: ملغي

### التوصيل (Deliveries)
- `pending`: قيد الانتظار
- `in_progress`: قيد التنفيذ
- `completed`: مكتمل
- `cancelled`: ملغي

### الاستئجار (Rentals)
- `pending`: قيد الانتظار
- `approved`: موافق عليه
- `rejected`: مرفوض

### غسيل السيارات (Car Wash)
- `pending`: قيد الانتظار
- `confirmed`: مؤكد
- `completed`: مكتمل
- `cancelled`: ملغي

---

## طرق الدفع (Payment Methods)
- `cash`: نقدي
- `apple_pay`: أبل باي
- `bank_card`: بطاقة بنكية

---

## استيراد Postman Collection
يمكنك استيراد ملف `postman_collection.json` في Postman لاختبار جميع الـ APIs.

## ملاحظات
- جميع التواريخ والأوقات بصيغة ISO 8601
- جميع الأرقام العشرية بصيغة `decimal:2`
- OTP الافتراضي للاختبار: `1234`
- **ملاحظة مهمة:** تم دمج جدول `addresses` مع `user_locations`، لذلك جميع العناوين تُخزن في جدول `user_locations` مع إضافة حقول `type` و `is_default`
- عند استخدام `location_id` في الطلبات أو مواعيد الغسيل، يجب استخدام ID من جدول `user_locations`

