<?php

namespace App\Http\Controllers;

use App\Models\Governorate;
use App\Models\Qualification;
use App\Models\User;
use App\Notifications\AccountStatusNotification;
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
        $messages = [
            'phone.regex' => 'صيغة رقم الهاتف غير صحيحة يجب أن تكون من 11 رقماً وتبدأ ب 077 أو 078',
            'phone.required' => 'رقم الهاتف مطلوب',
            'national_id.unique' => 'رقم البطاقة الوطنية موجود مسبقاً',
            'email.unique' => 'البريد الإلكتروني موجود مسبقاً',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ];

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
        ], $messages);

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
        $genders = ['ذكر', 'أنثى'];
        $allGovernorates = Governorate::orderBy('name')->pluck('name');
        $birthYears = User::whereNotNull('date_of_birth')->distinct()->pluck('date_of_birth')->sort();
        $graduationYears = User::whereNotNull('graduation_year')->distinct()->pluck('graduation_year')->sort();
        $qualifications = Qualification::orderBy('name')->get(['id', 'name']);
        return view('admin.users.index', compact('genders', 'allGovernorates', 'birthYears', 'graduationYears', 'qualifications'));
    }

    public function data(Request $request): JsonResponse
    {
        $columns = [
            'id', 'id', 'name', 'email', 'national_id', 'phone', 'points',
            'gender', 'governorate', 'date_of_birth', 'social_status',
            'children_count', 'qualification_id', 'graduation_year',
            'approval_status', 'status',
        ];

        $query = User::with('qualification:id,name')
            ->select([
                'id', 'name', 'first_name', 'father_name', 'grandfather_name', 'family_name',
                'mother_name', 'mother_father_name', 'mother_grandfather_name',
                'email', 'phone', 'national_id', 'governorate', 'address',
                'gender', 'social_status', 'children_count', 'age', 'date_of_birth',
                'qualification_id', 'qualification_faculty_id', 'graduation_year',
                'job_status', 'image', 'status', 'approval_status', 'role',
                'points', 'created_at', 'updated_at',
            ]);

        // Global search
        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Column filters
        if ($gender = $request->input('gender')) {
            $query->where('gender', $gender);
        }
        if ($gov = $request->input('governorate')) {
            $query->where('governorate', $gov);
        }
        if ($birth = $request->input('birth_year')) {
            $query->where('date_of_birth', $birth);
        }
        if ($social = $request->input('social_status')) {
            $query->where('social_status', $social);
        }
        if ($request->filled('children_count')) {
            $children = $request->input('children_count');
            $query->where('children_count', $children);
        }
        if ($gradYear = $request->input('graduation_year')) {
            $query->where('graduation_year', $gradYear);
        }
        if ($qualId = $request->input('qualification_id')) {
            $query->where('qualification_id', $qualId);
        }
        if ($approval = $request->input('approval_status')) {
            $query->where('approval_status', $approval);
        }
        if ($status = $request->input('active_status')) {
            $query->where('status', $status);
        }

        $recordsTotal = $query->count();

        // Order
        $orderCol = $request->input('order.0.column', 1);
        $orderDir = $request->input('order.0.dir', 'desc');
        if (isset($columns[$orderCol])) {
            $query->orderBy($columns[$orderCol], $orderDir);
        }

        // Paginate
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 50);
        $users = $query->skip($start)->take($length)->get();

        $recordsFiltered = $recordsTotal;

        $data = [];
        foreach ($users as $i => $u) {
            $govName = $u->governorate_name;
            $qualName = $u->qualification?->name ?? '—';

            $avatar = '';
            if ($u->image) {
                $avatar = '<img src="' . $u->image_url . '" class="avatar avatar-sm" style="object-fit:cover">';
            } else {
                $avatar = '<div class="avatar avatar-sm bg-gradient-to-br from-sky-500 to-indigo-600 text-white text-xs">' . htmlspecialchars(substr($u->name, 0, 2)) . '</div>';
            }

            $genderBadge = $u->gender === 'ذكر'
                ? '<span class="pill pill-blue">ذكر</span>'
                : ($u->gender === 'أنثى' ? '<span class="pill pill-rose">أنثى</span>' : '<span class="text-slate-400">—</span>');

            $approvalBadge = $u->role === 'admin'
                ? '<span class="pill pill-violet">مدير</span>'
                : ($u->approval_status === 'approved' ? '<span class="pill pill-green">مقبول</span>' : ($u->approval_status === 'rejected' ? '<span class="pill pill-rose">مرفوض</span>' : '<span class="pill pill-amber">قيد المراجعة</span>'));

            $statusBadge = $u->status === 'active'
                ? '<span class="pill pill-green">نشط</span>'
                : '<span class="pill pill-rose">غير نشط</span>';

            $actions = '<div class="flex gap-1">'
                . '<button class="btn btn-ghost py-1 px-2 text-xs" onclick="openUserModal(' . $u->id . ')" title="عرض التفاصيل">👁️</button>'
                . '<button class="btn btn-ghost py-1 px-2 text-xs" onclick="openEditModal(' . $u->id . ')" title="تعديل">✏️</button>';

            if (!$u->isAdmin()) {
                $toggleIcon = $u->status === 'active' ? '⏸️' : '▶️';
                $toggleTitle = $u->status === 'active' ? 'تعليق' : 'تفعيل';
                $actions .= '<button class="btn btn-ghost py-1 px-2 text-xs toggle-status-btn" data-url="' . route('admin.users.toggle-status', $u) . '" data-name="' . htmlspecialchars($u->name) . '" title="' . $toggleTitle . '">' . $toggleIcon . '</button>'
                    . '<button class="btn btn-danger py-1 px-2 text-xs" data-delete="' . route('admin.users.destroy', $u) . '" data-name="' . htmlspecialchars($u->name) . '">🗑️</button>';
            }
            $actions .= '</div>';

            $data[] = [
                '<input type="checkbox" class="user-checkbox" value="' . $u->id . '" onchange="updateBulkActions()"' . ($u->isAdmin() ? ' disabled' : '') . '>',
                $u->id,
                '<div class="flex items-center gap-2">' . $avatar . '<span class="font-semibold">' . htmlspecialchars($u->name) . '</span></div>',
                htmlspecialchars($u->email),
                htmlspecialchars($u->national_id ?? '—'),
                htmlspecialchars($u->phone ?? '—'),
                '<span class="pill pill-amber">' . number_format($u->points) . '</span>',
                $genderBadge,
                htmlspecialchars($govName),
                htmlspecialchars($u->date_of_birth ?? '—'),
                htmlspecialchars($u->social_status ?? '—'),
                htmlspecialchars($u->children_count ?? '—'),
                htmlspecialchars($qualName),
                htmlspecialchars($u->graduation_year ?? '—'),
                $approvalBadge,
                $statusBadge,
                $actions,
            ];
        }

        return response()->json([
            'draw' => (int) $request->input('draw', 0),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
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
        $messages = [
            'phone.regex' => 'صيغة رقم الهاتف غير صحيحة يجب أن تكون من 11 رقماً وتبدأ ب 077 أو 078',
            'national_id.unique' => 'رقم البطاقة الوطنية موجود مسبقاً',
            'email.unique' => 'البريد الإلكتروني موجود مسبقاً',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ];

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
            'governorate' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:1000',
            'qualification_id' => 'nullable|exists:qualifications,id',
            'qualification_faculty_id' => 'nullable|exists:qualification_faculties,id',
            'graduation_year' => 'required|integer|min:2000|max:2025',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:8|confirmed',
        ], $messages);

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

        // Only send email on deactivation, not on activation
        if ($newStatus === 'inactive') {
            $adminName = auth()->user()->name;
            try {
                $user->notify(new AccountStatusNotification('deactivated', $adminName));
            } catch (\Throwable $e) {
                // fail silently
            }
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
        @set_time_limit(300);

        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        $users = User::whereIn('id', $validated['ids'])
            ->where('role', '!=', 'admin')
            ->get();

        $adminName = auth()->user()->name;
        $total = $users->count();
        $processed = 0;

        // Bulk update all at once
        User::whereIn('id', $users->pluck('id'))
            ->update([
                'approval_status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'status' => 'active',
            ]);

        // Send emails individually
        foreach ($users as $user) {
            try {
                $user->notify(new AccountStatusNotification('approved', $adminName));
            } catch (\Throwable $e) {
                // fail silently
            }
            $processed++;
        }

        return response()->json([
            'success' => true,
            'message' => "تم تفعيل {$total} مستخدم بنجاح",
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
