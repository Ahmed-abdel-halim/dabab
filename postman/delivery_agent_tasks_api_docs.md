# توثيق رواد البرمجة (API Documentation) - إدارة مهام المندوب

## مقدمة: أنواع تخصص المندوب (Agent Services)
يعتمد النظام على تخصيص المندوب لخدمة محددة عند التسجيل عبر حقل الـ `working_service` كـ `enum` لضمان الدقة:

1.  **`dabab_tawseel` (دباب توصيل)**: المندوب في هذا التخصص يسـتقبل جميع طلبات **"التوصيل"** (`delivery`) و **"الطلبات/المتجر"** (`order`).
2.  **`car_wash` (غسيل سيارات)**: المندوب في هذا التخصص يسـتقبل فقط طلبات **"غسيل السيارات"** (`car_wash`).

---


هذا الملف يشرح نقاط الاتصال الجديدة المخصصة لمندوب التوصيل لإدارة الطلبات، التوصيل، وغسيل السيارات.

---

## 1. جلب المهام المتاحة (Get Available Tasks)
لجلب المهام التي لم يتم استلامها من قبل أي مندوب آخر.

*   **الرابط (URL):** `GET /v1/delivery-agent/tasks/available`
*   **المعطيات (Params):**
    *   `type`: (اختياري) للفلترة حسب النوع (`order`, `delivery`, `car_wash`).
    *   `lang`: (اختياري) لغة الرد (`ar`, `en`).

### حالة النجاح (200 OK):
```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "order_number": "ORD-123",
            "task_type": "order",
            "delivery_cost": "15.00",
            "location": { "address": "الرياض، حي النرجس" },
            "items": [...]
        }
    ],
    "message": "تم تحميل المهام بنجاح"
}
```

### حالة الفشل:
*   **401 Unauthorized**: إذا لم يتم إرسال الـ Token أو انتهت صلاحيته.
*   **403 Forbidden**: إذا كان المستخدم المسجل ليس "مندوب توصيل".

---

## 2. قبول مهمة (Accept Task)
لربط المندوب بالطلب وبدء التنفيذ.

*   **الرابط (URL):** `POST /v1/delivery-agent/tasks/{type}/{id}/accept`
*   **المتغيرات (Path Variables):**
    *   `type`: نوع المهمة الحالي.
    *   `id`: رقم المعرف للمهمة.

### حالة النجاح (200 OK):
```json
{
    "status": "success",
    "data": { "id": 1, "status": "in_progress", "delivery_agent_id": 5 },
    "message": "تم قبول المهمة وبدء التنفيذ"
}
```

### حالة الفشل:
*   **404 Not Found**: إذا كان الطلب غير موجود أو تم قبوله بالفعل من قبل مندوب آخر.
*   **422 Unprocessable Content**: إذا تم إرسال `type` خاطئ.

---

## 3. رفع شواهد المهمة (Upload Task Proof)
لرفع صورة الغرض أو الفاتورة.

*   **الرابط (URL):** `POST /v1/delivery-agent/tasks/{type}/{id}/proof`
*   **نوع الارسال (Body):** `form-data`
*   **الحقول:**
    *   `item_photo`: ملف صورة (اختياري).
    *   `invoice_photo`: ملف صورة (اختياري).

### حالة النجاح (200 OK):
```json
{
    "status": "success",
    "data": {
        "item_photo": "https://domain.com/storage/tasks/order/items/photo.jpg",
        "invoice_photo": "https://domain.com/storage/tasks/order/invoices/photo.jpg"
    },
    "message": "تم رفع الصور بنجاح"
}
```

---

## 4. تحديث حالة المهمة (Update Task Status)
لتغيير حالة الطلب (مثلاً من قيد التنفيذ إلى مكتمل).

*   **الرابط (URL):** `POST /v1/delivery-agent/tasks/{type}/{id}/status`
*   **المعطيات (Body - JSON):**
    ```json
    { "status": "completed" } 
    ```

### حالة النجاح (200 OK):
*   **ملاحظة**: عند الإرسال بـ `completed` يتم تسجيل وقت التسليم (`delivered_at`) آلياً.
```json
{
    "status": "success",
    "data": { "status": "completed", "delivered_at": "2026-03-25 21:00:00" },
    "message": "تم تحديث حالة المهمة"
}
```

---

## 5. عرض مهامي الحالية (Get Active Tasks)
لعرض الطلبات التي يقوم المندوب بتنفيذها حالياً.

*   **الرابط (URL):** `GET /v1/delivery-agent/tasks/active`
*   **المعطيات (Params):**
    *   `status`: (اختياري) الافتراضي هو `in_progress`. يمكن إرسال `completed` لمشاهدة السجل التاريخي.

---

### جدول ملخص الأخطاء الشائعة:
| الرمز | المعنى | السبب المحتمل | 
| :--- | :--- | :--- | 
| 401 | غير مصرح | لم تقم بإرسال الـ Token في الـ Header | 
| 403 | ممنوع | المستخدم ليس مسجلاً كمندوب توصيل | 
| 404 | غير موجود | رقم المعرف (ID) للمهمة غير صحيح أو المهمة ملغاة | 
| 422 | خطأ في البيانات | إرسال `type` غير مدعوم أو صور بحجم كبير جداً | 
