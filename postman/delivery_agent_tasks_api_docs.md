

## 6. الإعدادات وإدارة الحساب (Settings & Account Management)
هذه النقاط تسمح للمندوب بتحديث بياناته بعد اكتمال التسجيل.

### 6.1 حالة التسجيل والبروفايل (Registration Status & Profile)
*   **الرابط:** `GET /v1/delivery-agent/registration-status`
*   **ملاحظة:** حقل `completed_tasks_count` يعرض إجمالي عدد الطلبات المكتملة للمندوب.

### 6.2 تحديث البيانات الشخصية للمندوب (Update Agent Profile)
*   **الرابط:** `POST /v1/delivery-agent/settings/profile`
*   **الحقول:** `name`, `phone`, `nationality`, `national_id_number`, `birth_date`, `working_service`, `profile_photo` (file).

### 6.3 تحديث بيانات المركبة (Update Vehicle Details)
*   **الرابط:** `POST /v1/delivery-agent/settings/vehicle`
*   **الحقول:** `vehicle_type` (car, motorcycle, scooter, bicycle, other), `vehicle_brand`, `vehicle_model`, `manufacturing_year`, `license_plate_number`, `license_plate_letters`, `license_type`, `vehicle_photo` (file).

### 6.4 تحديث البيانات البنكية (Update Bank Details)
*   **الرابط:** `POST /v1/delivery-agent/settings/bank`
*   **الحقول:** `bank_name`, `account_holder_name`, `iban`.

### 6.5 رفع وثيقة جديدة (Upload Document)
*   **الرابط:** `POST /v1/delivery-agent/settings/documents`
*   **الحقول:** 
    *   `document_type`: (`id_front`, `id_back`, `driving_license`, ...)
    *   `file`: (ملف الصورة).

### 6.6 تحديث اللغة (Update Language)
*   **الرابط:** `POST /v1/update-locale`
*   **الحقول:** `locale` (ar, en).

### 6.7 حذف الحساب (Delete Account)
*   **الرابط:** `POST /v1/delete-account`

### 6.8 المحفظة (Wallet)
*   **الرابط:** `GET /v1/wallet/balance` لروية الرصيد الحالي.
*   **الرابط:** `GET /v1/wallet/transactions` لروية سجل المعاملات.

---

## 7. مركز المساعدة والمعلومات (Support & Information)

### 7.1 الأسئلة المتكررة (FAQs)
*   **الرابط:** `GET /v1/faqs?type=delivery_agent`
*   **المعطيات:** 
    *   `type`: يمكن أن يكون `customer` أو `delivery_agent`.

### 7.2 سياسة الخصوصية (Privacy Policy)
*   **الرابط:** `GET /v1/privacy-policy?type=delivery_agent`
*   **المعطيات:**
    *   `type`: يمكن أن يكون `customer` أو `delivery_agent`.

### 7.3 الشروط والأحكام (Terms and Conditions)
*   **الرابط:** `GET /v1/terms-and-conditions?type=delivery_agent`
*   **المعطيات:**
    *   `type`: يمكن أن يكون `customer` أو `delivery_agent`.
