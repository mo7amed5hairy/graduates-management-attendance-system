<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\PointsTransaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $events = Event::whereIn('status', ['upcoming', 'ongoing'])->latest()->get();
        return view('admin.attendance.index', compact('events'));
    }

    public function event(Event $event)
    {
        $event->load(['attendances.user', 'attendees']);
        return view('admin.attendance.event', compact('event'));
    }

    public function scan(Event $event)
    {
        return view('admin.attendance.scan', compact('event'));
    }

    public function markAttendance(Request $request): JsonResponse
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $attendance = EventAttendance::where('event_id', $request->event_id)
            ->where('user_id', $request->user_id)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'هذا العضو غير مسجل في هذه الفعالية',
            ], 404);
        }

        if ($attendance->isAttended()) {
            return response()->json([
                'success' => false,
                'message' => 'هذا العضو مسجل حضوره بالفعل',
            ], 422);
        }

        $attendance->update([
            'attended_at' => now(),
            'scanned_by' => Auth::id(),
        ]);

        // Award points
        if ($attendance->points_awarded > 0) {
            $user = User::find($request->user_id);
            $user->increment('points', $attendance->points_awarded);

            PointsTransaction::create([
                'user_id' => $request->user_id,
                'points' => $attendance->points_awarded,
                'type' => 'add',
                'reason' => 'حضور فعالية: ' . $attendance->event->title,
                'created_by' => Auth::id(),
                'event_id' => $request->event_id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الحضور بنجاح',
            'user_name' => $attendance->user->name,
        ]);
    }

    public function searchGraduates(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        $users = User::where('role', 'user')
            ->where('approval_status', 'approved')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                  ->orWhere('email', 'like', "%$query%")
                  ->orWhere('national_id', 'like', "%$query%")
                  ->orWhere('phone', 'like', "%$query%");
            })
            ->limit(20)
            ->get(['id', 'name', 'email', 'national_id']);

        return response()->json(['data' => $users]);
    }

    public function getAttendees(Event $event): JsonResponse
    {
        $event->load(['attendances.user', 'attendees']);
        return response()->json([
            'data' => [
                'attendances' => $event->attendances,
                'attendees' => $event->attendees,
                'total' => $event->attendances->count(),
                'present' => $event->attendees->count(),
            ]
        ]);
    }
}
