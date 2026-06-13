<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::withCount(['attendances', 'attendees'])->latest()->get();
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        $graduates = User::where('role', 'user')->where('approval_status', 'approved')->orderBy('name')->get();
        return view('admin.events.create', compact('graduates'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:100',
            'location' => 'nullable|string|max:255',
            'event_date' => 'required|date',
            'points' => 'required|integer|min:0',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'upcoming';

        $event = Event::create($validated);

        // Register participants
        if (!empty($request->participants)) {
            foreach ($request->participants as $userId) {
                $qrCode = Str::uuid() . '-' . $event->id . '-' . $userId;
                EventAttendance::create([
                    'user_id' => $userId,
                    'event_id' => $event->id,
                    'qr_code' => $qrCode,
                    'points_awarded' => $event->points,
                ]);

                Notification::create([
                    'user_id' => $userId,
                    'type' => 'event_invitation',
                    'data' => [
                        'title' => 'دعوة لفعالية',
                        'message' => 'تمت دعوتك للمشاركة في: ' . $event->title,
                        'event_id' => $event->id,
                        'event_date' => $event->event_date->format('Y-m-d h:i A'),
                    ],
                ]);
            }
        }

        $message = 'تم إنشاء الفعالية بنجاح';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('admin.events.index'),
            ]);
        }

        return redirect()->route('admin.events.index')->with('success', $message);
    }

    public function show(Event $event)
    {
        $event->loadCount(['attendances', 'attendees']);
        $event->load(['attendances.user', 'attendees']);
        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $graduates = User::where('role', 'user')->where('approval_status', 'approved')->orderBy('name')->get();
        $participants = $event->attendances()->pluck('user_id')->toArray();
        return view('admin.events.edit', compact('event', 'graduates', 'participants'));
    }

    public function update(Request $request, Event $event): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|max:100',
            'location' => 'nullable|string|max:255',
            'event_date' => 'required|date',
            'points' => 'required|integer|min:0',
            'status' => 'required|in:upcoming,ongoing,completed,cancelled',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
        ]);

        $event->update($validated);

        // Sync participants
        $currentParticipants = $event->attendances()->pluck('user_id')->toArray();
        $newParticipants = $request->participants ?? [];

        // Remove participants that were deselected
        $toRemove = array_diff($currentParticipants, $newParticipants);
        if (!empty($toRemove)) {
            $event->attendances()->whereIn('user_id', $toRemove)->delete();
        }

        // Add new participants
        $toAdd = array_diff($newParticipants, $currentParticipants);
        foreach ($toAdd as $userId) {
            $qrCode = Str::uuid() . '-' . $event->id . '-' . $userId;
            EventAttendance::create([
                'user_id' => $userId,
                'event_id' => $event->id,
                'qr_code' => $qrCode,
                'points_awarded' => $event->points,
            ]);

            Notification::create([
                'user_id' => $userId,
                'type' => 'event_invitation',
                'data' => [
                    'title' => 'دعوة لفعالية',
                    'message' => 'تمت دعوتك للمشاركة في: ' . $event->title,
                    'event_id' => $event->id,
                    'event_date' => $event->event_date->format('Y-m-d h:i A'),
                ],
            ]);
        }

        $message = 'تم تحديث الفعالية بنجاح';

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('admin.events.index'),
            ]);
        }

        return redirect()->route('admin.events.index')->with('success', $message);
    }

    public function destroy(Event $event): JsonResponse|RedirectResponse
    {
        $event->attendances()->delete();
        $event->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'تم حذف الفعالية']);
        }

        return back()->with('success', 'تم حذف الفعالية');
    }
}
