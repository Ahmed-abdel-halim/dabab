<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;
use App\Models\Page;

class DeliveryAgentInfoSeeder extends Seeder
{
    /**
     * Run the database seeds for Delivery Agents.
     */
    public function run(): void
    {
        // 1. Seed FAQs for Delivery Agents
        $faqs = [
            [
                'question_ar' => 'كيف أبدأ في استقبال الطلبات؟',
                'question_en' => 'How do I start receiving orders?',
                'answer_ar' => 'يجب عليك أولاً إكمال پروفايلك ورفع كافة المستندات المطلوبة، بمجرد موافقة الإدارة ستظهر لك المهام المتاحة في صفحة "المهام المتاحة".',
                'answer_en' => 'First, complete your profile and upload all required documents. Once approved by admin, available tasks will appear on the "Available Tasks" page.',
                'category_ar' => 'البداية',
                'category_en' => 'Getting Started',
                'type' => 'delivery_agent',
                'sort_order' => 1,
            ],
            [
                'question_ar' => 'كيف يتم احتساب الأرباح؟',
                'question_en' => 'How are earnings calculated?',
                'answer_ar' => 'يتم احتساب الأرباح بناءً على سعر التوصيل لكل طلب، مخصوماً منها عمولة التطبيق المتفق عليها.',
                'answer_en' => 'Earnings are calculated based on the delivery price for each order, minus the agreed application commission.',
                'category_ar' => 'الأرباح',
                'category_en' => 'Earnings',
                'type' => 'delivery_agent',
                'sort_order' => 2,
            ],
            [
                'question_ar' => 'ماذا أفعل إذا واجهت مشكلة مع الطلب؟',
                'question_en' => 'What should I do if I face an issue with an order?',
                'answer_ar' => 'يمكنك استخدام خيار "مركز المساعدة" في الإعدادات أو التواصل مباشرة مع الدعم الفني عبر الواتساب.',
                'answer_en' => 'You can use the "Support Center" option in settings or contact technical support directly via WhatsApp.',
                'category_ar' => 'المساعدة',
                'category_en' => 'Support',
                'type' => 'delivery_agent',
                'sort_order' => 3,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                ['question_ar' => $faq['question_ar'], 'type' => 'delivery_agent'],
                $faq
            );
        }

        // 2. Seed Pages (Privacy, Terms) for Delivery Agents
        Page::updateOrCreate(
            ['slug' => 'terms-and-conditions', 'type' => 'delivery_agent'],
            [
                'title_ar' => 'الشروط والأحكام الخاصة بالمندوبين',
                'title_en' => 'Terms and Conditions for Delivery Agents',
                'content_ar' => 'يجب على المندوب الالتزام بجميع القوانين والأنظمة المحلية عند استخدام التطبيق لتوصيل الطلبات. كما يجب الحفاظ على جودة الخدمة والمواعيد.',
                'content_en' => 'The agent must comply with all local laws and regulations when using the app to deliver orders. Service quality and timings must be maintained.',
            ]
        );

        Page::updateOrCreate(
            ['slug' => 'privacy-policy', 'type' => 'delivery_agent'],
            [
                'title_ar' => 'سياسة الخصوصية الخاصة بالمندوبين',
                'title_en' => 'Privacy Policy for Delivery Agents',
                'content_ar' => 'نحن نحترم خصوصية بيانات المندوبين. يتم جمع بيانات الموقع الجغرافي فقط أثناء تشغيل التطبيق وتواجد المندوب في وضع "نشط" لتوزيع الطلبات القريبة.',
                'content_en' => 'We respect agent privacy. Location data is collected only while the app is running and the agent is "active" to distribute nearby orders.',
            ]
        );
    }
}
