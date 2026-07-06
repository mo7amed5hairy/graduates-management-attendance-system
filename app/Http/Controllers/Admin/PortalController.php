<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortalFaq;
use App\Models\PortalNews;
use App\Models\PortalSetting;
use App\Models\PortalVideo;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    public function index()
    {
        $settings = PortalSetting::pluck('value', 'key');
        $news = PortalNews::orderBy('sort_order')->orderByDesc('created_at')->get();
        $videos = PortalVideo::orderBy('sort_order')->orderByDesc('created_at')->get();
        $faqs = PortalFaq::orderBy('sort_order')->get();
        return view('admin.portal.index', compact('settings', 'news', 'videos', 'faqs'));
    }

    // Settings
    public function updateSettings(Request $request)
    {
        $keys = [
            'hero_title', 'hero_subtitle', 'hero_motto',
            'stats_title', 'ticker_badge', 'news_section_title',
            'cta_title', 'cta_instagram_url', 'cta_video_url',
            'footer_copyright', 'footer_rights',
        ];
        foreach ($keys as $key) {
            if ($request->has($key)) {
                PortalSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $request->input($key)]
                );
            }
        }
        return back()->with('success', 'تم تحديث الإعدادات بنجاح');
    }

    // News
    public function storeNews(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:500',
            'image' => 'nullable|string|max:255',
            'news_date' => 'nullable|date',
        ]);
        $data['sort_order'] = PortalNews::max('sort_order') + 1;
        PortalNews::create($data);
        return back()->with('success', 'تم إضافة الخبر بنجاح');
    }

    public function updateNews(Request $request, PortalNews $portalNews)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:500',
            'image' => 'nullable|string|max:255',
            'news_date' => 'nullable|date',
        ]);
        $portalNews->update($data);
        return back()->with('success', 'تم تحديث الخبر بنجاح');
    }

    public function toggleNews(PortalNews $portalNews)
    {
        $portalNews->update(['is_active' => !$portalNews->is_active]);
        return back()->with('success', 'تم تغيير حالة الخبر');
    }

    public function destroyNews(PortalNews $portalNews)
    {
        $portalNews->delete();
        return back()->with('success', 'تم حذف الخبر');
    }

    // Videos
    public function storeVideo(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:500',
            'image' => 'nullable|string|max:255',
        ]);
        $data['sort_order'] = PortalVideo::max('sort_order') + 1;
        PortalVideo::create($data);
        return back()->with('success', 'تم إضافة الفيديو بنجاح');
    }

    public function updateVideo(Request $request, PortalVideo $portalVideo)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:500',
            'image' => 'nullable|string|max:255',
        ]);
        $portalVideo->update($data);
        return back()->with('success', 'تم تحديث الفيديو بنجاح');
    }

    public function toggleVideo(PortalVideo $portalVideo)
    {
        $portalVideo->update(['is_active' => !$portalVideo->is_active]);
        return back()->with('success', 'تم تغيير حالة الفيديو');
    }

    public function destroyVideo(PortalVideo $portalVideo)
    {
        $portalVideo->delete();
        return back()->with('success', 'تم حذف الفيديو');
    }

    // FAQs
    public function storeFaq(Request $request)
    {
        $data = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);
        $data['sort_order'] = PortalFaq::max('sort_order') + 1;
        PortalFaq::create($data);
        return back()->with('success', 'تم إضافة السؤال بنجاح');
    }

    public function updateFaq(Request $request, PortalFaq $portalFaq)
    {
        $data = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
        ]);
        $portalFaq->update($data);
        return back()->with('success', 'تم تحديث السؤال بنجاح');
    }

    public function destroyFaq(PortalFaq $portalFaq)
    {
        $portalFaq->delete();
        return back()->with('success', 'تم حذف السؤال');
    }
}
