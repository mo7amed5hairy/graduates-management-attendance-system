<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GovernorateController extends Controller
{
    public function index()
    {
        $governorates = Governorate::withCount('institutions')->latest()->get();
        return view('admin.governorates.index', compact('governorates'));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:governorates',
        ]);

        $governorate = Governorate::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المحافظة بنجاح',
            'data' => $governorate,
        ]);
    }

    public function update(Request $request, Governorate $governorate): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:governorates,name,' . $governorate->id,
        ]);

        $governorate->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث المحافظة بنجاح',
            'data' => $governorate,
        ]);
    }

    public function destroy(Governorate $governorate): JsonResponse
    {
        if ($governorate->institutions()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن حذف المحافظة لأنها تحتوي على مؤسسات تعليمية',
            ], 422);
        }

        $governorate->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف المحافظة بنجاح',
        ]);
    }
}
