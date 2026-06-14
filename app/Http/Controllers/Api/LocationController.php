<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Models\Institution;
use App\Models\InstitutionType;
use App\Models\Qualification;
use App\Models\QualificationFaculty;
use App\Models\UniversityType;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    public function governorates(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Governorate::orderBy('name')->get(),
        ]);
    }

    public function institutionTypes(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => InstitutionType::orderBy('name')->get(),
        ]);
    }

    public function universityTypes(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => UniversityType::orderBy('name')->get(),
        ]);
    }

    public function institutionsByGovernorate(Governorate $governorate, InstitutionType $institutionType, UniversityType $universityType): JsonResponse
    {
        $institutions = Institution::where('governorate_id', $governorate->id)
            ->where('institution_type_id', $institutionType->id)
            ->where('university_type_id', $universityType->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'data' => $institutions,
        ]);
    }

    public function departmentsByInstitution(Institution $institution): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $institution->departments()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function qualifications(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Qualification::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function facultiesByQualification(Qualification $qualification): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $qualification->faculties()->orderBy('name')->get(['id', 'name']),
        ]);
    }
}
