<?php

namespace App\Http\Controllers;

use App\Models\PortalFaq;
use App\Models\PortalNews;
use App\Models\PortalSetting;
use App\Models\PortalVideo;
use App\Models\User;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    public function index()
    {
        $settings = PortalSetting::pluck('value', 'key');
        $news = PortalNews::where('is_active', true)->orderBy('sort_order')->orderByDesc('created_at')->get();
        $videos = PortalVideo::where('is_active', true)->orderBy('sort_order')->orderByDesc('created_at')->get();
        $faqs = PortalFaq::orderBy('sort_order')->get();

        $totalUsers = User::count();
        $maleCount = User::where('gender', 'ذكر')->count();
        $femaleCount = User::where('gender', 'أنثى')->count();
        $approvedCount = User::where('approval_status', 'approved')->count();
        $pendingCount = User::where('approval_status', 'pending')->count();
        $governoratesCount = \App\Models\Governorate::count();

        return view('portal.index', compact(
            'settings', 'news', 'videos', 'faqs',
            'totalUsers', 'maleCount', 'femaleCount',
            'approvedCount', 'pendingCount', 'governoratesCount'
        ));
    }
}
