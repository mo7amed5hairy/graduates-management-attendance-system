<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Qualification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QualificationController extends Controller
{
    public function index()
    {
        $qualifications = Qualification::withCount('faculties')->latest()->get();
        return view('admin.qualifications.index', compact('qualifications'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:qualifications',
        ]);

        $qualification = Qualification::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المؤهل بنجاح',
            'data' => $qualification,
        ]);
    }

    public function update(Request $request, Qualification $qualification): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:qualifications,name,' . $qualification->id,
        ]);

        $qualification->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المؤهل بنجاح',
            'data' => $qualification,
        ]);
    }

    public function destroy(Qualification $qualification): JsonResponse
    {
        if ($qualification->users()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف المؤهل لأنه مرتبط بمستخدمين',
            ], 422);
        }

        $qualification->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المؤهل بنجاح',
        ]);
    }
}
