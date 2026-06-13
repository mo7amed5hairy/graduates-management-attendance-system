<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstitutionType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstitutionTypeController extends Controller
{
    public function index()
    {
        $types = InstitutionType::withCount('institutions')->latest()->get();
        return view('admin.institution-types.index', compact('types'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:institution_types',
        ]);

        $type = InstitutionType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة النوع بنجاح',
            'data' => $type,
        ]);
    }

    public function update(Request $request, InstitutionType $institutionType): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:institution_types,name,' . $institutionType->id,
        ]);

        $institutionType->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث النوع بنجاح',
            'data' => $institutionType,
        ]);
    }

    public function destroy(InstitutionType $institutionType): JsonResponse
    {
        if ($institutionType->institutions()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف النوع لأنه مرتبط بمؤسسات تعليمية',
            ], 422);
        }

        $institutionType->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف النوع بنجاح',
        ]);
    }
}
