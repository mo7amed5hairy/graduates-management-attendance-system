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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'national_id' => 'required|string|max:20|unique:users',
            'governorate' => 'required|exists:governorates,id',
            'institution_type' => 'required|exists:institution_types,id',
            'university_type' => 'required|exists:university_types,id',
            'institution_id' => 'required|exists:institutions,id',
            'department_id' => 'required|exists:departments,id',
            'graduation_year' => 'required|integer|min:1950|max:' . (date('Y') + 5),
            'job_status' => 'required|string|max:100',
            'age' => 'nullable|integer|min:1|max:150',
            'gender' => 'nullable|string|in:ذكر,أنثى',
            'address' => 'nullable|string|max:1000',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $institution = Institution::with('governorate')->find($validated['institution_id']);
        $department = Department::find($validated['department_id']);

        $validated['governorate'] = $institution->governorate->name;
        $validated['university'] = $institution->name;
        $validated['faculty'] = $department->name;

        unset($validated['institution_type'], $validated['university_type'], $validated['institution_id'], $validated['department_id']);

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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
        ]);

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
