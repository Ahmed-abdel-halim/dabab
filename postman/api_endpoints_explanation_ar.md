# شرح نقاط الاتصال (API Endpoints) - Dabab Payment & Info API

هذا الملف يحتوي على شرح لنقاط الاتصال الموجودة في مجموعة Postman المسماة "Dabab Payment & Info API". هذه المجموعة خاصة بإدارة بطاقات الدفع، المحفظة، Apple Pay، والصفحات الثابتة (الأسئلة الشائعة، سياسة الخصوصية، الشروط والأحكام).

## المتغيرات العامة (Variables)
- **baseUrl**: رابط الخادم الأساسي (مثال: `http://localhost:8000`)
- **token**: رمز التحقق (Bearer Token) المستخدم في الترويسة (Header) للمصادقة.

---

## 1. المدفوعات والبطاقات (Payment & Cards)

### 1.1 الحصول على طرق الدفع (Get Payment Methods)
- **الوصف**: جلب قائمة بطرق الدفع المتاحة.
- **الطريقة (Method)**: `GET`
- **الرابط (URL)**: `{{baseUrl}}/api/v1/payment-methods`
- **الترويسة (Headers)**:
  - `Accept`: `application/json`
  - `Authorization`: `Bearer {{token}}`

### 1.2 معالجة Apple Pay (Process Apple Pay)
- **الوصف**: معالجة عملية دفع عبر Apple Pay.
- **الطريقة (Method)**: `POST`
- **الرابط (URL)**: `{{baseUrl}}/api/v1/payment/apple-pay`
- **الترويسة (Headers)**:
  - `Accept`: `application/json`
  - `Authorization`: `Bearer {{token}}`
- **جسم الطلب (Body)** - JSON:
  ```json
  {
      "token": "pk_test_token_from_ios", // التوكن المستلم من Apple Pay
      "amount": 50.00,                   // المبلغ
      "order_id": "ORD-123"              // رقم الطلب
  }
  ```

### 1.3 عرض البطاقات المحفوظة (List Saved Cards)
- **الوصف**: عرض قائمة بطاقات الدفع المحفوظة للمستخدم الحالي.
- **الطريقة (Method)**: `GET`
- **الرابط (URL)**: `{{baseUrl}}/api/v1/cards`
- **الترويسة (Headers)**:
  - `Accept`: `application/json`
  - `Authorization`: `Bearer {{token}}`

### 1.4 إضافة بطاقة جديدة (Add New Card)
- **الوصف**: حفظ بيانات بطاقة ائتمان جديدة.
- **الطريقة (Method)**: `POST`
- **الرابط (URL)**: `{{baseUrl}}/api/v1/cards`
- **الترويسة (Headers)**:
  - `Accept`: `application/json`
  - `Authorization`: `Bearer {{token}}`
- **جسم الطلب (Body)** - JSON:
  ```json
  {
      "card_holder_name": "Wafaa Ahmed",       // اسم حامل الابطاقة
      "card_number": "1234432112344321",       // رقم البطاقة
      "expiry_date": "12/26",                  // تاريخ الانتهاء (شهر/سنة)
      "cvv": "123"                             // رمز الأمان
  }
  ```

### 1.5 حذف بطاقة (Delete Card)
- **الوصف**: حذف بطاقة محفوظة بناءً على معرفها (ID).
- **الطريقة (Method)**: `DELETE`
- **الرابط (URL)**: `{{baseUrl}}/api/v1/cards/:id`
  - `:id` هو متغير يمثل معرف البطاقة المراد حذفها.
- **الترويسة (Headers)**:
  - `Accept`: `application/json`
  - `Authorization`: `Bearer {{token}}`

---

## 2. المحفظة (Wallet)

### 2.1 معرفة رصيد المحفظة (Get Wallet Balance)
- **الوصف**: استرجاع الرصيد الحالي لمحفظة المستخدم.
- **الطريقة (Method)**: `GET`
- **الرابط (URL)**: `{{baseUrl}}/api/v1/wallet/balance`
- **الترويسة (Headers)**:
  - `Accept`: `application/json`
  - `Authorization`: `Bearer {{token}}`

### 2.2 شحن المحفظة (Charge Wallet)
- **الوصف**: إضافة رصيد للمحفظة باستخدام طريقة دفع محفوظة.
- **الطريقة (Method)**: `POST`
- **الرابط (URL)**: `{{baseUrl}}/api/v1/wallet/charge`
- **الترويسة (Headers)**:
  - `Accept`: `application/json`
  - `Authorization`: `Bearer {{token}}`
- **جسم الطلب (Body)** - JSON:
  ```json
  {
      "amount": 100,                  // المبلغ المراد شحنه
      "payment_method_id": "card_1"   // معرف طريقة الدفع المستخدمة
  }
  ```

### 2.3 معاملات المحفظة (Wallet Transactions)
- **الوصف**: عرض سجل العمليات (إيداع/سحب/دفع) التي تمت على المحفظة.
- **الطريقة (Method)**: `GET`
- **الرابط (URL)**: `{{baseUrl}}/api/v1/wallet/transactions`
- **الترويسة (Headers)**:
  - `Accept`: `application/json`
  - `Authorization`: `Bearer {{token}}`

---

## 3. المعلومات والمحتوى (Info & Content)

### 3.1 الأسئلة الشائعة (Get FAQs)
- **الوصف**: جلب قائمة الأسئلة الشائعة.
- **الطريقة (Method)**: `GET`
- **الرابط (URL)**: `{{baseUrl}}/api/v1/faqs`
- **الترويسة (Headers)**:
  - `Accept`: `application/json`

### 3.2 سياسة الخصوصية (Privacy Policy)
- **الوصف**: جلب نص سياسة الخصوصية.
- **الطريقة (Method)**: `GET`
- **الرابط (URL)**: `{{baseUrl}}/api/v1/privacy-policy`
- **الترويسة (Headers)**:
  - `Accept`: `application/json`

### 3.3 الشروط والأحكام (Terms and Conditions)
- **الوصف**: جلب نص الشروط والأحكام.
- **الطريقة (Method)**: `GET`
- **الرابط (URL)**: `{{baseUrl}}/api/v1/terms-and-conditions`
- **الترويسة (Headers)**:
  - `Accept`: `application/json`

### 3.4 تحديث لغة التطبيق (Update App Locale)
- **الوصف**: تحديث اللغة المفضلة للمستخدم في التطبيق.
- **الطريقة (Method)**: `POST`
- **الرابط (URL)**: `{{baseUrl}}/api/v1/update-locale`
- **الترويسة (Headers)**:
  - `Accept`: `application/json`
  - `Authorization`: `Bearer {{token}}`
- **جسم الطلب (Body)** - JSON:
  ```json
  {
      "locale": "ar" // رمز اللغة (مثل ar للعربية أو en للإنجليزية)
  }
  ```
