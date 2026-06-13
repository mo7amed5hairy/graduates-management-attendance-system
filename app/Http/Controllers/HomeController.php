<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        $transactions = $user->pointsTransactions()->with('creator', 'task', 'event')->latest()->take(10)->get();

        $taskStats = [
            'total' => $user->tasks()->count(),
            'completed' => $user->tasks()->where('status', 'completed')->count(),
            'pending' => $user->tasks()->whereIn('status', ['assigned', 'incomplete'])->count(),
        ];

        $upcomingEvents = $user->attendances()->with('event')
            ->whereHas('event', function ($q) {
                $q->where('status', 'upcoming');
            })
            ->latest()
            ->take(5)
            ->get();

        return view('home', compact('user', 'transactions', 'taskStats', 'upcomingEvents'));
    }
}
