<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SubAdminController extends Controller
{
    public function index(): View
    {
        $subAdmins = User::where('role', 'admin')
            ->where('email', '!=', 'admin@admin.com')
            ->latest()
            ->get();
        return view('admin.sub-admins.index', compact('subAdmins'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = 'admin';
        $validated['status'] = 'active';
        $validated['approval_status'] = 'approved';
        $validated['national_id'] = 'admin_' . uniqid();
        $validated['permissions'] = $validated['permissions'] ?? [];

        User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المشرف بنجاح',
        ]);
    }

    public function edit(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $user->only(['id', 'name', 'email', 'phone', 'permissions']),
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        if ($user->email === 'admin@admin.com') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تعديل صلاحيات مدير النظام الأساسي',
            ], 422);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['permissions'] = $validated['permissions'] ?? [];

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المشرف بنجاح',
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->email === 'admin@admin.com') {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف مدير النظام الأساسي',
            ], 422);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المشرف بنجاح',
        ]);
    }
}
