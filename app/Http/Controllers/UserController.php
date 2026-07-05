<?php

namespace App\Http\Controllers;

use App\Models\Governorate;
use App\Models\Qualification;
use App\Models\User;
use App\Notifications\AccountStatusNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function create(): JsonResponse
    {
        return response()->json(['success' => true]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'grandfather_name' => 'required|string|max:255',
            'family_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'mother_father_name' => 'required|string|max:255',
            'mother_grandfather_name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|regex:/^07[78]\d{8}$/',
            'national_id' => 'required|string|max:12|unique:users',
            'date_of_birth' => 'required|integer|min:1970|max:2015',
            'gender' => 'required|string|in:ذكر,أنثى',
            'job_status' => 'required|string|max:100',
            'social_status' => 'required|string|in:أعزب,متزوج,مطلق,أرمل',
            'children_count' => 'nullable|integer|min:0|max:20',
            'governorate' => 'required|string|max:100',
            'address' => 'required|string|max:1000',
            'qualification_id' => 'required|exists:qualifications,id',
            'qualification_faculty_id' => 'required|exists:qualification_faculties,id',
            'graduation_year' => 'required|integer|min:2000|max:2025',
        ]);

        $motherName = trim(($validated['mother_name'] ?? '') . ' ' . ($validated['mother_father_name'] ?? '') . ' ' . ($validated['mother_grandfather_name'] ?? ''));
        $validated['name'] = trim("{$validated['first_name']} {$validated['father_name']} {$validated['grandfather_name']} {$validated['family_name']}" . ($motherName ? " ($motherName)" : ''));

        $validated['password'] = Hash::make($validated['password']);

        // Calculate age from date_of_birth (year only)
        if (!empty($validated['date_of_birth'])) {
            $validated['age'] = (int) date('Y') - (int) $validated['date_of_birth'];
        }

        $validated['role'] = 'user';
        $validated['approval_status'] = 'approved';
        $validated['approved_at'] = now();
        $validated['approved_by'] = auth()->id();
        $validated['access_token'] = bin2hex(random_bytes(32));

        // Auto-generate email if not provided
        if (empty($validated['email'])) {
            $validated['email'] = 'user_' . $validated['national_id'] . '@system.local';
        }

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المستخدم بنجاح',
            'data' => $user->fresh(),
        ]);
    }

    public function index()
    {
        $cacheKey = 'users_index_' . md5(request()->fullUrl());
        $cacheTtl = 60; // seconds

        $data = Cache::remember($cacheKey, $cacheTtl, function () {
            $users = User::with('qualification:id,name', 'qualificationFaculty:id,name')
                ->select([
                    'id', 'name', 'first_name', 'father_name', 'grandfather_name', 'family_name',
                    'mother_name', 'mother_father_name', 'mother_grandfather_name',
                    'email', 'phone', 'national_id', 'governorate', 'address',
                    'gender', 'social_status', 'children_count', 'age', 'date_of_birth',
                    'qualification_id', 'qualification_faculty_id', 'graduation_year',
                    'job_status', 'image', 'status', 'approval_status', 'role',
                    'points', 'created_at', 'updated_at',
                ])
                ->latest()
                ->get();

            $genders = ['ذكر', 'أنثى'];
            $governorates = User::whereNotNull('governorate')->distinct()->pluck('governorate')->sort();
            $allGovernorates = Governorate::orderBy('name')->pluck('name');
            $birthYears = User::whereNotNull('date_of_birth')->distinct()->pluck('date_of_birth')->sort();
            $graduationYears = User::whereNotNull('graduation_year')->distinct()->pluck('graduation_year')->sort();
            $qualifications = Qualification::orderBy('name')->get(['id', 'name']);

            return compact('users', 'genders', 'governorates', 'allGovernorates', 'birthYears', 'graduationYears', 'qualifications');
        });

        return view('admin.users.index', $data);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'grandfather_name' => 'required|string|max:255',
            'family_name' => 'required|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mother_father_name' => 'nullable|string|max:255',
            'mother_grandfather_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|regex:/^07[78]\d{8}$/',
            'national_id' => 'required|string|max:12|unique:users,national_id,' . $user->id,
            'date_of_birth' => 'nullable|integer|min:1970|max:2015',
            'gender' => 'nullable|string|in:ذكر,أنثى',
            'job_status' => 'required|string|max:100',
            'social_status' => 'nullable|string|in:أعزب,متزوج,مطلق,أرمل',
            'children_count' => 'nullable|integer|min:0|max:20',
            'governorate' => 'required|string|max:100',
            'address' => 'nullable|string|max:1000',
            'qualification_id' => 'nullable|exists:qualifications,id',
            'qualification_faculty_id' => 'nullable|exists:qualification_faculties,id',
            'graduation_year' => 'required|integer|min:2000|max:2025',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $motherName = trim(($data['mother_name'] ?? '') . ' ' . ($data['mother_father_name'] ?? '') . ' ' . ($data['mother_grandfather_name'] ?? ''));
        $data['name'] = trim("{$data['first_name']} {$data['father_name']} {$data['grandfather_name']} {$data['family_name']}" . ($motherName ? " ($motherName)" : ''));

        // Calculate age from date_of_birth (year only)
        if (!empty($data['date_of_birth'])) {
            $data['age'] = (int) date('Y') - (int) $data['date_of_birth'];
        }

        // Auto-generate email if not provided
        if (empty($data['email'])) {
            $data['email'] = 'user_' . $data['national_id'] . '@system.local';
        }

        // Only hash password if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المستخدم بنجاح',
            'data' => $user->fresh(),
        ]);
    }

    public function toggleStatus(User $user): JsonResponse
    {
        $oldStatus = $user->status;
        $newStatus = $oldStatus === 'active' ? 'inactive' : 'active';

        $user->update(['status' => $newStatus]);

        $action = $newStatus === 'active' ? 'activated' : 'deactivated';
        $adminName = auth()->user()->name;
        try {
            $user->notify(new AccountStatusNotification($action, $adminName));
        } catch (\Throwable $e) {
            // fail silently — email is non-blocking
        }

        $statusText = $newStatus === 'active' ? 'نشط' : 'غير نشط';

        return response()->json([
            'success' => true,
            'message' => "تم تغيير حالة المستخدم إلى $statusText",
            'data' => $user->fresh(),
        ]);
    }

    public function bulkActivate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        $users = User::whereIn('id', $validated['ids'])
            ->where('role', '!=', 'admin')
            ->get();

        $adminName = auth()->user()->name;

        foreach ($users as $user) {
            $user->update([
                'approval_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'status' => 'active',
            ]);
            try {
                $user->notify(new AccountStatusNotification('approved', $adminName));
            } catch (\Throwable $e) {
                // fail silently
            }
        }

        $count = $users->count();

        return response()->json([
            'success' => true,
            'message' => "تم تفعيل {$count} مستخدم بنجاح",
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف حساب أدمن',
            ], 422);
        }

        $adminName = auth()->user()->name;
        try {
            $user->notify(new AccountStatusNotification('deleted', $adminName));
        } catch (\Throwable $e) {
            // fail silently
        }

        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المستخدم بنجاح',
        ]);
    }
}
