<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UniversityType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UniversityTypeController extends Controller
{
    public function index()
    {
        $types = UniversityType::withCount('institutions')->latest()->get();
        return view('admin.university-types.index', compact('types'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:university_types',
        ]);

        $type = UniversityType::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة النوع بنجاح',
            'data' => $type,
        ]);
    }

    public function update(Request $request, UniversityType $universityType): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:university_types,name,' . $universityType->id,
        ]);

        $universityType->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث النوع بنجاح',
            'data' => $universityType,
        ]);
    }

    public function destroy(UniversityType $universityType): JsonResponse
    {
        if ($universityType->institutions()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف النوع لأنه مرتبط بمؤسسات تعليمية',
            ], 422);
        }

        $universityType->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف النوع بنجاح',
        ]);
    }
}
