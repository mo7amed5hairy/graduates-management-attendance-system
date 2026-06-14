<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Qualification;
use App\Models\QualificationFaculty;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QualificationFacultyController extends Controller
{
    public function index()
    {
        $faculties = QualificationFaculty::with('qualification')->latest()->get();
        $qualifications = Qualification::orderBy('name')->get();
        return view('admin.qualification-faculties.index', compact('faculties', 'qualifications'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'qualification_id' => 'required|exists:qualifications,id',
            'name' => 'required|string|max:255',
        ]);

        $faculty = QualificationFaculty::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة الكلية بنجاح',
            'data' => $faculty->load('qualification'),
        ]);
    }

    public function update(Request $request, QualificationFaculty $qualificationFaculty): JsonResponse
    {
        $validated = $request->validate([
            'qualification_id' => 'required|exists:qualifications,id',
            'name' => 'required|string|max:255',
        ]);

        $qualificationFaculty->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الكلية بنجاح',
            'data' => $qualificationFaculty->load('qualification'),
        ]);
    }

    public function destroy(QualificationFaculty $qualificationFaculty): JsonResponse
    {
        $qualificationFaculty->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الكلية بنجاح',
        ]);
    }
}
