<?php

namespace App\Http\Controllers;

use App\Models\PointsTransaction;
use App\Models\Task;
use App\Models\User;
use App\Http\Requests\PointsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PointController extends Controller
{
    public function index(Request $request)
    {
        $query = PointsTransaction::with('user', 'creator', 'task', 'event')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->get();
        $users = User::where('status', 'active')->orderBy('name')->get();
        $tasks = Task::whereIn('status', ['assigned', 'incomplete'])->with('user')->latest()->get();

        return view('admin.points.index', compact('transactions', 'users', 'tasks'));
    }

    public function store(PointsRequest $request): JsonResponse
    {
        $user = User::findOrFail($request->user_id);

        $transaction = PointsTransaction::create([
            'user_id' => $request->user_id,
            'points' => $request->points,
            'type' => $request->type,
            'reason' => $request->reason,
            'task_id' => $request->task_id,
            'created_by' => $request->user()->id,
        ]);

        if ($request->type === 'add') {
            $user->increment('points', $request->points);
            $message = 'تم إضافة ' . $request->points . ' نقطة للمستخدم ' . $user->name;
        } else {
            $user->decrement('points', $request->points);
            $message = 'تم خصم ' . $request->points . ' نقطة من المستخدم ' . $user->name;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'transaction' => $transaction->load('user', 'creator'),
                'user_points' => $user->fresh()->points,
            ],
        ]);
    }

    public function getUserPoints(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'points' => $user->points,
                'name' => $user->name,
            ],
        ]);
    }
}
