<?php

namespace App\Http\Controllers;

use App\Models\EventAttendance;
use Illuminate\Support\Facades\Auth;

class UserEventController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $attendances = $user->attendances()->with('event')->latest()->get();
        return view('events.index', compact('attendances'));
    }

    public function qrCode(EventAttendance $attendance)
    {
        if ($attendance->user_id !== Auth::id()) {
            abort(403);
        }

        return view('events.qr', compact('attendance'));
    }
}
