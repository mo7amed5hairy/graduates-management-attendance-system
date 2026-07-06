<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portal_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('portal_news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('url');
            $table->string('image')->nullable();
            $table->date('news_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('portal_videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('url');
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('portal_faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Default settings
        $settings = [
            ['hero_title', 'بوابة الخريجين العراقيين: مستقبل بيدينا'],
            ['hero_subtitle', 'نحن ليس هدفنا التظاهر .. ولكن بأيدينا نصنع المستقبل'],
            ['hero_motto', 'نحن ليس هدفنا التظاهر .. ولكن بأيدينا نصنع المستقبل'],
            ['stats_title', 'الإحصائيات والبيانات'],
            ['ticker_badge', 'شريط الأخبار'],
            ['news_section_title', 'آخر الأخبار والمستجدات'],
            ['cta_title', '✦ دعمك هو نجاحنا ✦'],
            ['cta_instagram_url', 'https://www.instagram.com/s14mv'],
            ['cta_video_url', 'https://www.instagram.com/s14mv'],
            ['footer_copyright', 'نصنع المستقبل'],
            ['footer_rights', 'جميع الحقوق محفوظة'],
        ];
        foreach ($settings as $s) {
            DB::table('portal_settings')->insert([
                'key' => $s[0],
                'value' => $s[1],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_faqs');
        Schema::dropIfExists('portal_videos');
        Schema::dropIfExists('portal_news');
        Schema::dropIfExists('portal_settings');
    }
};
