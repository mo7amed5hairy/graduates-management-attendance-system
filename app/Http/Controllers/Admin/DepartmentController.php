<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Institution;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::with('institution.governorate')->latest()->get();
        $institutions = Institution::with('governorate')->orderBy('name')->get();
        return view('admin.departments.index', compact('departments', 'institutions'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'institution_id' => 'required|exists:institutions,id',
        ]);

        $department = Department::create($validated);
        $department->load('institution.governorate');

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة القسم/التخصص بنجاح',
            'data' => $department,
        ]);
    }

    public function update(Request $request, Department $department): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'institution_id' => 'required|exists:institutions,id',
        ]);

        $department->update($validated);
        $department->load('institution.governorate');

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث القسم/التخصص بنجاح',
            'data' => $department,
        ]);
    }

    public function destroy(Department $department): JsonResponse
    {
        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف القسم/التخصص بنجاح',
        ]);
    }
}
