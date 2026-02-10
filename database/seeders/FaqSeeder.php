<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faqs = [
            [
                'question_ar' => 'كيف يمكنني إنشاء حساب؟',
                'question_en' => 'How can I create an account?',
                'answer_ar' => 'يمكنك إنشاء حساب عن طريق النقر على زر "تسجيل" في الصفحة الرئيسية وملء البيانات المطلوبة.',
                'answer_en' => 'You can create an account by clicking on the "Sign Up" button on the homepage and filling in the required information.',
                'category_ar' => 'الحساب',
                'category_en' => 'Account',
                'sort_order' => 1,
            ],
            [
                'question_ar' => 'ما هي طرق الدفع المتاحة؟',
                'question_en' => 'What payment methods are available?',
                'answer_ar' => 'نحن نقبل الدفع عبر البطاقات الائتمانية (فيزا، ماستركارد)، أبل باي، والدفع عند الاستلام.',
                'answer_en' => 'We accept payments via credit cards (Visa, MasterCard), Apple Pay, and Cash on Delivery.',
                'category_ar' => 'المدفوعات',
                'category_en' => 'Payments',
                'sort_order' => 2,
            ],
            [
                'question_ar' => 'كيف يمكنني تتبع طلبي؟',
                'question_en' => 'How can I track my order?',
                'answer_ar' => 'يمكنك تتبع طلبك من خلال الذهاب إلى قسم "طلباتي" في حسابك والنقر على الطلب الذي تريد تتبعه.',
                'answer_en' => 'You can track your order by going to the "My Orders" section in your account and clicking on the order you want to track.',
                'category_ar' => 'الطلبات',
                'category_en' => 'Orders',
                'sort_order' => 3,
            ],
            [
                'question_ar' => 'هل يمكنني إلغاء طلبي؟',
                'question_en' => 'Can I cancel my order?',
                'answer_ar' => 'نعم، يمكنك إلغاء طلبك إذا لم يتم شحنه بعد. يرجى التواصل مع خدمة العملاء للمساعدة.',
                'answer_en' => 'Yes, you can cancel your order if it has not been shipped yet. Please contact customer service for assistance.',
                'category_ar' => 'الطلبات',
                'category_en' => 'Orders',
                'sort_order' => 4,
            ],
            [
                'question_ar' => 'كيف أتواصل مع خدمة العملاء؟',
                'question_en' => 'How do I contact customer support?',
                'answer_ar' => 'يمكنك التواصل معنا عبر صفحة "اتصل بنا" أو من خلال الدردشة المباشرة في التطبيق.',
                'answer_en' => 'You can contact us via the "Contact Us" page or through the live chat in the app.',
                'category_ar' => 'عام',
                'category_en' => 'General',
                'sort_order' => 5,
            ],
        ];

        foreach ($faqs as $faqData) {
            Faq::create($faqData);
        }
    }
}
