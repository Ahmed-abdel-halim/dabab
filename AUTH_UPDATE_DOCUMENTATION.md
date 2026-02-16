# تحديث نظام المصادقة (Authentication System Update)

## التغييرات المطبقة

تم تحديث نظام المصادقة (OTP) ليعمل بالبريد الإلكتروني بدلاً من رقم الهاتف.

### ما تم تغييره:

#### 1. إرسال رمز التحقق (Send OTP)
**قبل:** كان يتم إرسال OTP إلى رقم الهاتف  
**بعد:** يتم إرسال OTP إلى البريد الإلكتروني

**Endpoint:** `POST /api/v1/send-otp`

**قبل التغيير:**
```json
{
  "phone": "0501234567",
  "type": "registration"
}
```

**بعد التغيير:**
```json
{
  "email": "user@example.com",
  "type": "registration" 
}
```

---

#### 2. التحقق من رمز OTP
**قبل:** كان يتم التحقق باستخدام رقم الهاتف  
**بعد:** يتم التحقق باستخدام البريد الإلكتروني

**Endpoint:** `POST /api/v1/verify-otp`

**قبل التغيير:**
```json
{
  "phone": "0501234567",
  "code": "1234"
}
```

**بعد التغيير:**
```json
{
  "email": "user@example.com",
  "code": "1234"
}
```

---

#### 3. إتمام التسجيل (Complete Registration)
**قبل:** كان يطلب البريد الإلكتروني (اختياري)  
**بعد:** يطلب رقم الهاتف (إلزامي)

**Endpoint:** `POST /api/v1/complete-profile`

**قبل التغيير:**
```json
{
  "temp_token": "abc123...",
  "name": "أحمد محمد",
  "email": "ahmed@example.com"  // اختياري
}
```

**بعد التغيير:**
```json
{
  "temp_token": "abc123...",
  "name": "أحمد محمد",
  "phone": "0501234567"  // إلزامي
}
```

---

#### 4. تسجيل الدخول (Login)
**قبل:** كان يتم تسجيل الدخول برقم الهاتف و OTP  
**بعد:** يتم تسجيل الدخول بالبريد الإلكتروني و OTP

**Endpoint:** `POST /api/v1/login`

**قبل التغيير:**
```json
{
  "phone": "0501234567",
  "code": "1234"
}
```

**بعد التغيير:**
```json
{
  "email": "user@example.com",
  "code": "1234"
}
```


## اختبار النظام الجديد

### 1. تسجيل مستخدم جديد

```bash
# الخطوة 1: طلب OTP
curl -X POST http://localhost:8000/api/v1/send-otp \
  -H "Content-Type: application/json" \
  -H "lang: ar" \
  -d '{
    "email": "test@example.com",
    "type": "registration"
  }'

# الخطوة 2: التحقق من OTP
curl -X POST http://localhost:8000/api/v1/verify-otp \
  -H "Content-Type: application/json" \
  -H "lang: ar" \
  -d '{
    "email": "test@example.com",
    "code": "RANDOM_OTP"
  }'

# الخطوة 3: إتمام التسجيل
curl -X POST http://localhost:8000/api/v1/complete-profile \
  -H "Content-Type: application/json" \
  -H "lang: ar" \
  -d '{
    "temp_token": "TOKEN_FROM_STEP_2",
    "name": "أحمد محمد",
    "phone": "0501234567"
  }'
```

### 2. تسجيل الدخول

```bash
# الخطوة 1: طلب OTP
curl -X POST http://localhost:8000/api/v1/send-otp \
  -H "Content-Type: application/json" \
  -H "lang: ar" \
  -d '{
    "email": "test@example.com",
    "type": "login"
  }'

# الخطوة 2: تسجيل الدخول
curl -X POST http://localhost:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -H "lang: ar" \
  -d '{
    "email": "test@example.com",
    "code": "RANDOM_OTP"
  }'
```

---

**تاريخ التحديث:** 2026-02-14  
**المطور:** Ahmed Abdel Halim  
**المشروع:** نظام Dabab - تحديث المصادقة
