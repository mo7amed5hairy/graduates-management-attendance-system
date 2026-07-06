<?php

namespace Database\Seeders;

use App\Models\PortalNews;
use App\Models\PortalVideo;
use App\Models\PortalFaq;
use Illuminate\Database\Seeder;

class PortalSeeder extends Seeder
{
    public function run(): void
    {
        PortalNews::create([
            'title' => 'انطلاق حملة التوثيق الإلكتروني للخريجين',
            'url' => 'https://www.instagram.com/s14mv',
            'news_date' => now()->subDays(2),
            'sort_order' => 1,
        ]);

        PortalNews::create([
            'title' => 'افتتاح باب التسجيل للخريجين الجدد',
            'url' => 'https://www.instagram.com/s14mv',
            'news_date' => now()->subDays(5),
            'sort_order' => 2,
        ]);

        PortalNews::create([
            'title' => 'ورشة عمل حول آلية تحديث البيانات الشخصية',
            'url' => 'https://www.instagram.com/s14mv',
            'news_date' => now()->subDays(10),
            'sort_order' => 3,
        ]);

        PortalNews::create([
            'title' => 'إطلاق ميزة طلب تعديل البيانات إلكترونياً',
            'url' => 'https://www.instagram.com/s14mv',
            'news_date' => now()->subDays(15),
            'sort_order' => 4,
        ]);

        PortalVideo::create([
            'title' => 'كيفية التسجيل في المنصة',
            'url' => 'https://www.instagram.com/s14mv',
            'sort_order' => 1,
        ]);

        PortalVideo::create([
            'title' => 'شرح رفع المستمسكات والوثائق',
            'url' => 'https://www.instagram.com/s14mv',
            'sort_order' => 2,
        ]);

        PortalVideo::create([
            'title' => 'كيفية تعديل البيانات الشخصية',
            'url' => 'https://www.instagram.com/s14mv',
            'sort_order' => 3,
        ]);

        PortalVideo::create([
            'title' => 'شرح آلية حضور الفعاليات',
            'url' => 'https://www.instagram.com/s14mv',
            'sort_order' => 4,
        ]);

        PortalFaq::create([
            'question' => 'كيف يمكنني التسجيل في المنصة؟',
            'answer' => 'يمكنك التسجيل عبر الضغط على زر التسجيل في أعلى الصفحة، ثم تعبئة البيانات المطلوبة وإرفاق المستمسكات. سيتم مراجعة طلبك من قبل الإدارة وتفعيل حسابك.',
            'sort_order' => 1,
        ]);

        PortalFaq::create([
            'question' => 'ما هي المستمسكات المطلوبة للتسجيل؟',
            'answer' => 'المستمسكات المطلوبة هي صورة واضحة من البطاقة الوطنية أو جواز السفر، وصورة من وثيقة التخرج أو تأييد التخرج.',
            'sort_order' => 2,
        ]);

        PortalFaq::create([
            'question' => 'كيف يمكنني تعديل بياناتي بعد التسجيل؟',
            'answer' => 'يمكنك تقديم طلب تعديل بيانات من خلال صفحة البروفايل بعد تسجيل الدخول. سيتم مراجعة طلبك والموافقة عليه من قبل الإدارة.',
            'sort_order' => 3,
        ]);

        PortalFaq::create([
            'question' => 'هل يمكنني حضور الفعاليات دون تسجيل؟',
            'answer' => 'لا، يجب أن تكون مسجلاً في المنصة ومفعل حسابك لحضور الفعاليات. يتم تسجيل الحضور عبر رمز QR الخاص بك.',
            'sort_order' => 4,
        ]);

        PortalFaq::create([
            'question' => 'كيف يتم احتساب النقاط؟',
            'answer' => 'يتم منح النقاط عند حضور الفعاليات والمشاركة في الأنشطة. يمكنك متابعة رصيد نقاطك من لوحة التحكم الخاصة بك.',
            'sort_order' => 5,
        ]);
    }
}
