<?php

namespace App\Http\Controllers;

use App\Models\Task;

class UserTaskController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $tasks = $user->tasks()->with('creator', 'attachments')->latest()->get();
        $taskStats = [
            'total' => $user->tasks()->count(),
            'completed' => $user->tasks()->where('status', 'completed')->count(),
            'assigned' => $user->tasks()->where('status', 'assigned')->count(),
            'incomplete' => $user->tasks()->where('status', 'incomplete')->count(),
        ];
        return view('user.tasks.index', compact('tasks', 'taskStats'));
    }

    public function show(Task $task)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && $task->user_id !== $user->id) {
            abort(403);
        }
        $task->load('user', 'creator', 'attachments');
        return view('user.tasks.show', compact('task'));
    }
}
