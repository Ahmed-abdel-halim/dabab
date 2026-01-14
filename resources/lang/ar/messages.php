<?php

return [
    // Auth Messages
    'auth.verification_code_sent' => 'تم إرسال كود التحقق',
    'auth.phone_not_registered' => 'رقم الهاتف غير مسجل',
    'auth.phone_already_registered' => 'رقم الهاتف مسجل بالفعل',
    'auth.verification_code_invalid' => 'كود التحقق غير صحيح أو منتهي',
    'auth.registration_token_invalid' => 'رمز التسجيل غير صالح',
    'auth.registration_success' => 'تم التسجيل بنجاح',
    'auth.login_success' => 'تم تسجيل الدخول',
    'auth.logout_success' => 'تم تسجيل الخروج',
    'auth.profile_loaded' => 'تم تحميل الملف الشخصي',
    'auth.location_updated' => 'تم تحديث الموقع',
    'auth.location_loaded' => 'تم جلب الموقع',
    'auth.verification_success' => 'تم التحقق بنجاح',
    'auth.default_user_name' => 'مستخدم',

    // Order Messages
    'order.categories_loaded' => 'تم جلب الفئات',
    'order.created' => 'تم إنشاء الطلب بنجاح',
    'order.updated' => 'تم تحديث الطلب',
    'order.deleted' => 'تم حذف الطلب',
    'order.cancelled' => 'تم إلغاء الطلب',
    'order.loaded' => 'تم جلب الطلب',
    'order.orders_loaded' => 'تم جلب الطلبات',
    'order.all_orders_loaded' => 'تم جلب جميع الطلبات',
    'order.confirmed' => 'تم تأكيد الطلب بنجاح',
    'order.tracking_info' => 'معلومات تتبع الطلب',
    'order.estimated_time_pending' => '30 - 45 دقيقة',
    'order.estimated_time_confirmed' => '20 - 35 دقيقة',
    'order.estimated_time_in_progress' => '10 - 20 دقيقة',
    'order.reordered' => 'تم إعادة الطلب بنجاح',
    'order.cannot_reorder_pending_cancelled' => 'لا يمكن إعادة طلب في حالة pending أو cancelled',

    // Address Messages
    'address.created' => 'تم إضافة العنوان بنجاح',
    'address.updated' => 'تم تحديث العنوان',
    'address.deleted' => 'تم حذف العنوان',
    'address.loaded' => 'تم جلب العنوان',
    'address.addresses_loaded' => 'تم جلب العناوين',

    // Rental Messages
    'rental.created' => 'تم تقديم طلب الاستئجار بنجاح',
    'rental.updated' => 'تم تحديث طلب الاستئجار',
    'rental.cancelled' => 'تم إلغاء طلب الاستئجار',
    'rental.deleted' => 'تم حذف طلب الاستئجار',
    'rental.loaded' => 'تم جلب طلب الاستئجار',
    'rental.rentals_loaded' => 'تم جلب طلبات الاستئجار',
    'rental.file_upload_failed' => 'فشل في رفع الملف',
    'rental.create_error' => 'حدث خطأ أثناء إنشاء الطلب',
    'rental.update_error' => 'حدث خطأ أثناء تحديث الطلب',
    'rental.reordered' => 'تم إعادة طلب الاستئجار بنجاح',
    'rental.cannot_reorder_pending_cancelled' => 'لا يمكن إعادة طلب في حالة pending أو cancelled',
    'rental.reorder_error' => 'حدث خطأ أثناء إعادة الطلب',

    // Delivery Messages
    'delivery.created' => 'تم إنشاء طلب التوصيل بنجاح',
    'delivery.updated' => 'تم تحديث طلب التوصيل',
    'delivery.deleted' => 'تم حذف طلب التوصيل',
    'delivery.cancelled' => 'تم إلغاء طلب التوصيل',
    'delivery.loaded' => 'تم جلب طلب التوصيل',
    'delivery.deliveries_loaded' => 'تم جلب طلبات التوصيل',
    'delivery.tracking_info' => 'معلومات تتبع التوصيل',
    'delivery.estimated_time_pending' => '30 - 45 دقيقة',
    'delivery.estimated_time_in_progress' => '15 - 30 دقيقة',
    'delivery.reordered' => 'تم إعادة طلب التوصيل بنجاح',
    'delivery.cannot_reorder_pending_cancelled' => 'لا يمكن إعادة طلب في حالة pending أو cancelled',

    // Car Wash Messages
    'car_wash.created' => 'تم حجز موعد الغسيل بنجاح',
    'car_wash.updated' => 'تم تحديث موعد الغسيل',
    'car_wash.deleted' => 'تم حذف موعد الغسيل',
    'car_wash.cancelled' => 'تم إلغاء موعد الغسيل',
    'car_wash.loaded' => 'تم جلب موعد الغسيل',
    'car_wash.appointments_loaded' => 'تم جلب مواعيد الغسيل',
    'car_wash.dates_loaded' => 'تم جلب الأيام المتاحة',
    'car_wash.periods_loaded' => 'تم جلب الفترات الزمنية',
    'car_wash.reordered' => 'تم إعادة موعد الغسيل بنجاح',
    'car_wash.cannot_reorder_pending_cancelled' => 'لا يمكن إعادة موعد في حالة pending أو cancelled',

    // Reorder Messages
    'reorder.created' => 'تم إعادة الطلب بنجاح',
    'reorder.order_created' => 'تم إعادة الطلب بنجاح',
    'reorder.rental_created' => 'تم إعادة طلب الاستئجار بنجاح',
    'reorder.delivery_created' => 'تم إعادة طلب التوصيل بنجاح',
    'reorder.car_wash_created' => 'تم إعادة موعد الغسيل بنجاح',
    'reorder.not_found' => 'الطلب الأصلي غير موجود',
    'reorder.cannot_reorder_pending_cancelled' => 'لا يمكن إعادة طلب في حالة pending أو cancelled',
    'reorder.invalid_type' => 'نوع الخدمة غير صحيح',

    // Rating Messages
    'rating.created' => 'تم إضافة التقييم بنجاح',
    'rating.ratings_loaded' => 'تم جلب التقييمات',
    'rating.order_or_delivery_required' => 'يجب تحديد طلب أو توصيل',
    'rating.already_rated' => 'تم التقييم مسبقاً',

    // Common Messages
    'common.success' => 'نجح',
    'common.error' => 'خطأ',
    'common.not_found' => 'غير موجود',
    'common.unauthorized' => 'غير مصرح',
    'common.validation_error' => 'خطأ في التحقق',
    'common.server_error' => 'خطأ في الخادم',
    'invalid_service_type' => 'نوع الخدمة غير صحيح',
];
