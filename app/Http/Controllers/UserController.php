<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $rules = [
            'first_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'grandfather_name' => 'required|string|max:255',
            'family_name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'national_id' => 'required|string|max:20|unique:users',
            'graduation_year' => 'required|integer|min:1950|max:' . (date('Y') + 5),
            'job_status' => 'required|string|max:100',
            'age' => 'nullable|integer|min:1|max:150',
            'gender' => 'nullable|string|in:ذكر,أنثى',
            'address' => 'nullable|string|max:1000',
            'mother_name' => 'nullable|string|max:255',
            'social_status' => 'nullable|string|in:أعزب,متزوج (بدون أطفال),متزوج (لديه أطفال),منفصل (بدون أطفال),منفصل (لديه أطفال),أرمل (بدون أطفال),أرمل (لديه أطفال)',
            'children_count' => 'nullable|integer|min:0',
            'date_of_birth' => 'nullable|date',
            'qualification_id' => 'nullable|exists:qualifications,id',
            'qualification_faculty_id' => 'nullable|exists:qualification_faculties,id',
        ];

        if ($request->filled('governorate') || $request->has('institution_type')) {
            $rules['governorate'] = 'nullable|exists:governorates,id';
            $rules['institution_type'] = 'nullable|exists:institution_types,id';
            $rules['university_type'] = 'nullable|exists:university_types,id';
            $rules['institution_id'] = 'nullable|exists:institutions,id';
            $rules['department_id'] = 'nullable|exists:departments,id';
        }

        $validated = $request->validate($rules);

        $validated['name'] = trim("{$validated['first_name']} {$validated['father_name']} {$validated['grandfather_name']} {$validated['family_name']}");

        $validated['password'] = Hash::make($validated['password']);

        if ($request->filled('institution_id')) {
            $institution = Institution::with('governorate')->find($validated['institution_id']);
            $department = Department::find($validated['department_id']);
            $validated['governorate'] = $institution->governorate->name;
            $validated['university'] = $institution->name;
            $validated['faculty'] = $department->name;
            unset($validated['institution_type'], $validated['university_type'], $validated['institution_id'], $validated['department_id']);
        }

        // Calculate age from date_of_birth if provided
        if (!empty($validated['date_of_birth']) && empty($validated['age'])) {
            $validated['age'] = \Carbon\Carbon::parse($validated['date_of_birth'])->age;
        }

        if ($request->hasFile('id_photos')) {
            $photos = [];
            foreach ($request->file('id_photos') as $file) {
                $photos[] = $file->store('id-photos', 'public');
            }
            $validated['id_photos'] = $photos;
        }

        if ($request->hasFile('residence_proof')) {
            $proofs = [];
            foreach ($request->file('residence_proof') as $file) {
                $proofs[] = $file->store('residence-proof', 'public');
            }
            $validated['residence_proof'] = $proofs;
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
        $users = User::latest()->get();
        return view('admin.users.index', compact('users'));
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
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

        $data['name'] = trim("{$data['first_name']} {$data['father_name']} {$data['grandfather_name']} {$data['family_name']}");

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المستخدم بنجاح',
            'data' => $user->fresh(),
        ]);
    }

    public function toggleStatus(User $user): JsonResponse
    {
        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active',
        ]);

        $statusText = $user->status === 'active' ? 'نشط' : 'غير نشط';

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

        $count = User::whereIn('id', $validated['ids'])
            ->where('role', '!=', 'admin')
            ->update([
                'approval_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'status' => 'active',
            ]);

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
