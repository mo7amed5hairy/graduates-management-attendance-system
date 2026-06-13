<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::with('user', 'creator', 'attachments')->latest()->get();
        $users = User::where('status', 'active')->orderBy('name')->get();
        return view('admin.tasks.index', compact('tasks', 'users'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        $task = Task::create([
            'user_id' => $validated['user_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => 'assigned',
            'created_by' => $request->user()->id,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('task-attachments', 'public');
                $task->attachments()->create([
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        $task->user->notifications()->create([
            'type' => 'task_assigned',
            'data' => ['task_id' => $task->id, 'title' => $task->title],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المهمة وتكليف المستخدم بنجاح',
            'redirect' => route('admin.tasks.index'),
        ]);
    }

    public function show(Task $task): JsonResponse
    {
        $task->load('user', 'creator', 'attachments');
        return response()->json(['success' => true, 'data' => $task]);
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:assigned,completed,incomplete',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        $task->update([
            'user_id' => $validated['user_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('task-attachments', 'public');
                $task->attachments()->create([
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المهمة بنجاح',
            'redirect' => route('admin.tasks.index'),
        ]);
    }

    public function destroy(Task $task): JsonResponse
    {
        foreach ($task->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $task->attachments()->delete();
        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المهمة بنجاح',
        ]);
    }
}
