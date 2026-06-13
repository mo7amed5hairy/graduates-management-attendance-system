<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Models\Institution;
use App\Models\InstitutionType;
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
}
