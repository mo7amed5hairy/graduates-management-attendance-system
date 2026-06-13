<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\PointsTransaction;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_graduates' => User::where('role', 'user')->count(),
            'pending_graduates' => User::where('role', 'user')->where('approval_status', 'pending')->count(),
            'approved_graduates' => User::where('role', 'user')->where('approval_status', 'approved')->count(),
            'total_events' => Event::count(),
            'upcoming_events' => Event::whereIn('status', ['upcoming', 'ongoing'])->count(),
            'total_attendance' => \App\Models\EventAttendance::whereNotNull('attended_at')->count(),
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'total_points' => User::sum('points'),
            'recent_transactions' => PointsTransaction::count(),
        ];

        $recent_graduates = User::where('role', 'user')->latest()->take(5)->get();
        $top_users = User::orderBy('points', 'desc')->take(10)->get();
        $upcoming_events = Event::whereIn('status', ['upcoming', 'ongoing'])->withCount('attendances')->latest()->take(5)->get();
        $recent_transactions = PointsTransaction::with('user', 'creator')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_graduates', 'top_users', 'upcoming_events', 'recent_transactions'));
    }
}
