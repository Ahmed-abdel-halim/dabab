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
    "location_id": 1,
    "scheduled_at": "2025-12-15 14:00:00",
    "payment_method": "cash",
    "items": [
        {
            "category_id": 1,
            "details": "2 قارورة , زجاجة زيت"
        },
        {
            "category_id": 2,
            "details": "صدور دجاج 20 قطعة"
        }
    ]
}
```
**ملاحظات:**
- `items` **إلزامي** ويجب أن يحتوي على طلب واحد على الأقل
- يمكنك إنشاء طلب واحد بإرسال `items` يحتوي على item واحد فقط
- يمكنك إنشاء طلب يحتوي على عدة طلبات فرعية من أماكن مختلفة
- كل طلب فرعي له فئة خاصة به وتفاصيله
- `delivery_cost` **يُحسب تلقائياً** من `fixed_price` للفئة المحددة في `category_id`
- التكلفة الإجمالية تُحسب تلقائياً من مجموع تكاليف الطلبات الفرعية

**مثال لطلب واحد:**
```json
{
    "location_id": 1,
    "scheduled_at": null,
    "payment_method": "cash",
    "items": [
        {
            "category_id": 1,
            "details": "2 قارورة , زجاجة زيت , صدور دجاج 20 قطعة"
        }
    ]
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

### الحصول على جميع الطلبات (من كل الخدمات)
```
GET /v1/all-orders?status=all
```
**Query Parameters:**
- `status`: all, pending, completed, cancelled
- `lang`: ar, en

**Response:**
```json
{
    "status": true,
    "message": "تم جلب جميع الطلبات",
    "data": [
        {
            "id": 1,
            "type": "order",
            "type_name": "طلب",
            "order_number": "ORD-2025-001",
            "status": "pending",
            "status_display": "قيد الانتظار",
            "total_cost": 25.00,
            "delivery_cost": 5.00,
            "payment_method": "cash",
            "payment_status": "pending",
            "scheduled_at": "2025-12-15 14:00:00",
            "location": { ... },
            "items": [ ... ],
            "created_at": "2025-12-13 10:00:00",
            "updated_at": "2025-12-13 10:00:00"
        },
        {
            "id": 1,
            "type": "delivery",
            "type_name": "توصيل",
            "order_number": "DEL-2025-001",
            "status": "completed",
            "status_display": "مكتمل",
            "shipment_details": "صندوق وزن 6 كيلو",
            "delivery_cost": 15.00,
            "created_at": "2025-12-12 09:00:00",
            "updated_at": "2025-12-12 11:00:00"
        },
        {
            "id": 1,
            "type": "rental",
            "type_name": "استئجار",
            "status": "completed",
            "status_display": "مكتمل",
            "personal_name": "أحمد محمد",
            "commercial_name": "صيدلية النور",
            "store_type": "صيدلية",
            "rental_type": "scooter_only",
            "created_at": "2025-12-11 08:00:00",
            "updated_at": "2025-12-11 10:00:00"
        },
        {
            "id": 1,
            "type": "car_wash",
            "type_name": "غسيل سيارات",
            "status": "pending",
            "status_display": "قيد الانتظار",
            "car_size": "large",
            "wash_type": "interior_exterior",
            "scheduled_date": "2025-12-15",
            "scheduled_time": "11:00",
            "cost": 100.00,
            "created_at": "2025-12-10 07:00:00",
            "updated_at": "2025-12-10 07:00:00"
        }
    ]
}
```
**ملاحظات:**
- يجلب الطلبات من جميع الخدمات الأربع: Orders, Deliveries, Rentals, Car Washes
- يتم ترتيب النتائج حسب التاريخ (الأحدث أولاً)
- `status` موحد: pending (قيد الانتظار), completed (مكتمل), cancelled (ملغي)
- `type` يحدد نوع الخدمة: order, delivery, rental, car_wash
- `status_display` يعرض حالة الطلب بالعربية أو الإنجليزية حسب اللغة المحددة

### إضافة طلب فرعي لطلب موجود
```
POST /v1/orders/{id}/items
```
**Body:**
```json
{
    "category_id": 3,
    "details": "طلب جديد من مكان آخر"
}
```
**ملاحظات:**
- يمكن إضافة طلبات فرعية فقط للطلبات في حالة `pending`
- `delivery_cost` يُحسب تلقائياً من `fixed_price` للفئة المحددة

### تحديث طلب فرعي
```
PUT /v1/orders/{orderId}/items/{itemId}
```
**Body:**
```json
{
    "category_id": 3,
    "details": "تفاصيل محدثة"
}
```
**ملاحظات:**
- يمكن تحديث الطلبات الفرعية فقط للطلبات في حالة `pending`
- إذا تم تغيير `category_id`، سيتم إعادة حساب `delivery_cost` تلقائياً من `fixed_price` للفئة الجديدة

### حذف طلب فرعي
```
DELETE /v1/orders/{orderId}/items/{itemId}
```
**ملاحظة:** يمكن حذف الطلبات الفرعية فقط للطلبات في حالة `pending`. عند الحذف، يتم تحديث التكلفة الإجمالية للطلب تلقائياً.

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

### الحصول على الأيام المتاحة
```
GET /v1/car-washes/available-dates
```
**Response:**
```json
{
    "status": true,
    "message": "تم جلب الأيام المتاحة",
    "data": [
        {
            "date": "2025-12-13",
            "day_name": "الخميس",
            "day_number": "13",
            "day_short": "خ"
        },
        {
            "date": "2025-12-14",
            "day_name": "الجمعة",
            "day_number": "14",
            "day_short": "ج"
        }
    ]
}
```
**ملاحظة:** يعيد الأيام المتاحة للأسبوع القادم (7 أيام من اليوم)

### الحصول على الفترات الزمنية
```
GET /v1/car-washes/time-periods?lang=ar
```
**ملاحظة:** الاسم (`name`) يعتمد على اللغة المحددة في `lang` parameter

**Response (عند lang=ar):**
```json
{
    "status": true,
    "message": "تم جلب الفترات الزمنية",
    "data": [
        {
            "period": "before_lunch",
            "name": "قبل الغداء",
            "time_range": "11:00 - 13:00",
            "start_time": "11:00",
            "end_time": "13:00",
            "period_type": "afternoon"
        },
        {
            "period": "early_evening",
            "name": "بداية المساء",
            "time_range": "17:30 - 19:30",
            "start_time": "17:30",
            "end_time": "19:30",
            "period_type": "evening"
        },
        {
            "period": "dinner_time",
            "name": "وقت العشاء",
            "time_range": "19:30 - 21:30",
            "start_time": "19:30",
            "end_time": "21:30",
            "period_type": "evening"
        },
        {
            "period": "late_night",
            "name": "آخر الليل",
            "time_range": "21:30 - 00:30",
            "start_time": "21:30",
            "end_time": "00:30",
            "period_type": "evening"
        }
    ]
}
```

**Response (عند lang=en):**
```json
{
    "status": true,
    "message": "Time periods loaded successfully",
    "data": [
        {
            "period": "before_lunch",
            "name": "Before Lunch",
            "time_range": "11:00 - 13:00",
            "start_time": "11:00",
            "end_time": "13:00",
            "period_type": "afternoon"
        },
        ...
    ]
}
```

### الحصول على مواعيد الغسيل
```
GET /v1/car-washes?status=all
```
**Query Parameters:**
- `status`: all, pending, confirmed, completed, cancelled

### حجز موعد غسيل
```
POST /v1/car-washes
```
**Body:**
```json
{
    "car_size": "large", // small, large
    "wash_type": "interior_exterior", // interior_exterior, exterior, interior
    "scheduled_date": "2025-12-13", // YYYY-MM-DD
    "scheduled_time": "11:00", // HH:mm (24 ساعة)
    "time_period": "before_lunch", // before_lunch, early_evening, dinner_time, late_night
    "location_id": 1 // ID من جدول user_locations
}
```
**ملاحظات:**
- `scheduled_date` يجب أن يكون تاريخ اليوم أو بعده
- `scheduled_time` بصيغة 24 ساعة (HH:mm)
- `time_period` يحدد الفترة الزمنية (قبل الغداء، بداية المساء، وقت العشاء، آخر الليل)

### الحصول على موعد غسيل محدد
```
GET /v1/car-washes/{id}
```
**Response يتضمن:**
- `scheduled_date_formatted`: التاريخ بصيغة YYYY-MM-DD
- `scheduled_time_formatted`: الوقت بصيغة HH:mm

### تحديث موعد غسيل
```
PUT /v1/car-washes/{id}
```
**ملاحظة:** يمكن تحديث المواعيد في حالة `pending` فقط

### إلغاء موعد غسيل
```
POST /v1/car-washes/{id}/cancel
```
**ملاحظة:** يمكن إلغاء المواعيد في حالة `pending` أو `confirmed` فقط

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

