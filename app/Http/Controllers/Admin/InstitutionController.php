<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Models\Institution;
use App\Models\InstitutionType;
use App\Models\UniversityType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    public function index()
    {
        $institutions = Institution::with(['governorate', 'institutionType', 'universityType', 'departments'])->latest()->get();
        $governorates = Governorate::orderBy('name')->get();
        $institutionTypes = InstitutionType::orderBy('name')->get();
        $universityTypes = UniversityType::orderBy('name')->get();
        return view('admin.institutions.index', compact('institutions', 'governorates', 'institutionTypes', 'universityTypes'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'institution_type_id' => 'required|exists:institution_types,id',
            'university_type_id' => 'required|exists:university_types,id',
            'governorate_id' => 'required|exists:governorates,id',
            'departments' => 'nullable|string',
        ]);

        $institution = Institution::create([
            'name' => $validated['name'],
            'institution_type_id' => $validated['institution_type_id'],
            'university_type_id' => $validated['university_type_id'],
            'governorate_id' => $validated['governorate_id'],
        ]);

        if (!empty($validated['departments'])) {
            $deptNames = array_map('trim', explode(',', $validated['departments']));
            foreach ($deptNames as $deptName) {
                if (!empty($deptName)) {
                    $institution->departments()->create(['name' => $deptName]);
                }
            }
        }

        $institution->load(['governorate', 'institutionType', 'universityType', 'departments']);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المؤسسة التعليمية بنجاح',
            'data' => $institution,
        ]);
    }

    public function update(Request $request, Institution $institution): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'institution_type_id' => 'required|exists:institution_types,id',
            'university_type_id' => 'required|exists:university_types,id',
            'governorate_id' => 'required|exists:governorates,id',
            'departments' => 'nullable|string',
        ]);

        $institution->update([
            'name' => $validated['name'],
            'institution_type_id' => $validated['institution_type_id'],
            'university_type_id' => $validated['university_type_id'],
            'governorate_id' => $validated['governorate_id'],
        ]);

        if ($request->has('departments')) {
            $institution->departments()->delete();
            $deptNames = array_map('trim', explode(',', $validated['departments']));
            foreach ($deptNames as $deptName) {
                if (!empty($deptName)) {
                    $institution->departments()->create(['name' => $deptName]);
                }
            }
        }

        $institution->load(['governorate', 'institutionType', 'universityType', 'departments']);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المؤسسة التعليمية بنجاح',
            'data' => $institution,
        ]);
    }

    public function destroy(Institution $institution): JsonResponse
    {
        $institution->departments()->delete();
        $institution->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المؤسسة التعليمية بنجاح',
        ]);
    }
}
