<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Models\Notification;
use App\Models\Qualification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GraduateController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'user');

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        if ($request->filled('governorate')) {
            $query->where('governorate', $request->governorate);
        }

        if ($request->filled('university')) {
            $query->where('university', 'like', '%' . $request->university . '%');
        }

        if ($request->filled('faculty')) {
            $query->where('faculty', 'like', '%' . $request->faculty . '%');
        }

        if ($request->filled('graduation_year')) {
            $query->where('graduation_year', $request->graduation_year);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('national_id', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%");
            });
        }

        $query->latest();

        $graduates = $query->get();

        $governorates = User::where('role', 'user')->whereNotNull('governorate')->distinct()->pluck('governorate')->sort();
        $governorateMap = Governorate::pluck('name', 'id')->toArray();
        $universities = User::where('role', 'user')->whereNotNull('university')->distinct()->pluck('university')->sort();
        $years = User::where('role', 'user')->whereNotNull('graduation_year')->distinct()->pluck('graduation_year')->sort();
        $genders = ['ذكر', 'أنثى'];
        $birthYears = User::where('role', 'user')->whereNotNull('date_of_birth')->distinct()->pluck('date_of_birth')->sort();
        $qualifications = Qualification::orderBy('name')->get(['id', 'name']);

        return view('admin.graduates.index', compact('graduates', 'governorates', 'governorateMap', 'universities', 'years', 'genders', 'birthYears', 'qualifications'));
    }

    public function show(User $user)
    {
        if ($user->role !== 'user') {
            abort(404);
        }

        return view('admin.graduates.show', compact('user'));
    }

    public function approve(User $user): JsonResponse|RedirectResponse
    {
        if ($user->role !== 'user') {
            abort(404);
        }

        $user->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'rejection_reason' => null,
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'approval',
            'data' => [
                'message' => 'تم اعتماد حسابك بنجاح. يمكنك الآن المشاركة في الفعاليات.',
                'title' => 'تهانينا! تم اعتماد حسابك',
            ],
        ]);

        $message = 'تم اعتماد الحساب بنجاح';

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    public function reject(Request $request, User $user): JsonResponse|RedirectResponse
    {
        if ($user->role !== 'user') {
            abort(404);
        }

        $request->validate(['reason' => 'required|string|max:1000']);

        $user->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $request->reason,
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'rejection',
            'data' => [
                'message' => 'لم يتم اعتماد حسابك. السبب: ' . $request->reason,
                'title' => 'تعذر اعتماد الحساب',
                'reason' => $request->reason,
            ],
        ]);

        $message = 'تم رفض الحساب وإرسال إشعار للمستخدم';

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    public function stats()
    {
        $total = User::where('role', 'user')->count();
        $pending = User::where('role', 'user')->where('approval_status', 'pending')->count();
        $approved = User::where('role', 'user')->where('approval_status', 'approved')->count();
        $rejected = User::where('role', 'user')->where('approval_status', 'rejected')->count();

        if (request()->ajax()) {
            return response()->json([
                'data' => compact('total', 'pending', 'approved', 'rejected')
            ]);
        }

        return view('admin.graduates.stats', compact('total', 'pending', 'approved', 'rejected'));
    }
}
