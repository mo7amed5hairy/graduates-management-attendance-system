<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Models\User;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function usersJson(Request $request)
    {
        $query = User::select([
            'id', 'first_name', 'father_name', 'grandfather_name', 'family_name',
            'mother_name', 'mother_father_name', 'mother_grandfather_name',
            'email', 'phone', 'national_id', 'governorate', 'address',
            'gender', 'social_status', 'children_count', 'age', 'date_of_birth',
            'qualification_id', 'qualification_faculty_id', 'graduation_year',
            'job_status', 'status', 'approval_status', 'role', 'points', 'created_at',
        ])
            ->with('qualification:id,name')
            ->with('qualificationFaculty:id,name')
            ->orderBy('id');

        $this->applyFilters($query, $request);

        $users = $query->get();

        $data = [];
        foreach ($users as $i => $u) {
            $data[] = [
                '#' => $i + 1,
                'المستخدم' => $u->name,
                'اسم الأم' => $u->mother_full_name,
                'البريد الإلكتروني' => $u->email ?? '—',
                'رقم البطاقة الوطنية' => $u->national_id ?? '—',
                'الهاتف' => $u->phone ?? '—',
                'النقاط' => $u->points,
                'الجنس' => $u->gender ?? '—',
                'المحافظة' => $u->governorate_name,
                'سنة الميلاد' => $u->date_of_birth ?? '—',
                'الحالة الاجتماعية' => $u->social_status ?? '—',
                'عدد الأولاد' => $u->children_count ?? '—',
                'المؤهل' => $u->qualification?->name ?? '—',
                'الكلية' => $u->qualificationFaculty?->name ?? '—',
                'سنة التخرج' => $u->graduation_year ?? '—',
                'حالة الاعتماد' => $u->approval_status,
                'الحالة' => $u->status === 'active' ? 'نشط' : 'غير نشط',
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => count($data),
        ]);
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('governorate')) {
            $gov = $request->governorate;
            $query->where(function ($q) use ($gov) {
                $q->where('governorate', $gov);
                $govModel = Governorate::find((int) $gov);
                if ($govModel) {
                    $q->orWhere('governorate', $govModel->name);
                }
            });
        }
        if ($request->filled('birth_year')) {
            $query->where('date_of_birth', $request->birth_year);
        }
        if ($request->filled('social_status')) {
            $query->where('social_status', $request->social_status);
        }
        if ($request->filled('children_count')) {
            $query->where('children_count', $request->children_count);
        }
        if ($request->filled('graduation_year')) {
            $query->where('graduation_year', $request->graduation_year);
        }
        if ($request->filled('qualification_id')) {
            $query->where('qualification_id', $request->qualification_id);
        }
        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }
        if ($request->filled('active_status')) {
            $query->where('status', $request->active_status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('national_id', 'like', "%{$s}%");
            });
        }
    }
}
