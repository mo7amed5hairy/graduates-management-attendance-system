<?php

namespace App\Http\Controllers;

use App\Models\Governorate;
use App\Models\ProfileChangeRequest;
use App\Models\Qualification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $transactions = $user->pointsTransactions()->with('creator', 'task', 'event')->latest()->take(20)->get();
        $governorates = Governorate::orderBy('name')->pluck('name');
        $qualifications = Qualification::orderBy('name')->get();
        $pendingRequest = ProfileChangeRequest::where('user_id', $user->id)->latest()->first();
        return view('profile.show', compact('user', 'transactions', 'governorates', 'qualifications', 'pendingRequest'));
    }

    public function edit()
    {
        $user = Auth::user();

        if ($user->isApproved()) {
            return redirect()->route('profile.show')
                ->with('error', 'بياناتك معتمدة ولا يمكن تعديلها. يرجى التواصل مع الإدارة.');
        }

        return view('profile.edit', compact('user'));
    }

    public function update(Request $request): JsonResponse|RedirectResponse
    {
        $user = Auth::user();

        if ($user->isApproved()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بياناتك معتمدة ولا يمكن تعديلها',
                ], 403);
            }
            return redirect()->route('profile.show')
                ->with('error', 'بياناتك معتمدة ولا يمكن تعديلها');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'grandfather_name' => 'required|string|max:255',
            'family_name' => 'required|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mother_father_name' => 'nullable|string|max:255',
            'mother_grandfather_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|regex:/^07[78]\d{8}$/',
            'password' => 'nullable|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'address' => 'nullable|string|max:1000',
            'national_id' => 'nullable|string|max:12|unique:users,national_id,' . $user->id,
            'governorate' => 'nullable|string|max:100',
            'university' => 'nullable|string|max:255',
            'faculty' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|integer|min:2000|max:2025',
            'job_status' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|integer|min:1970|max:2015',
            'gender' => 'nullable|string|in:ذكر,أنثى',
            'social_status' => 'nullable|string|in:أعزب,متزوج,مطلق,أرمل',
            'children_count' => 'nullable|integer|min:0|max:20',
            'social_links' => 'nullable|array',
            'social_links.*' => 'nullable|url|max:500',
        ]);

        $validated['name'] = trim("{$validated['first_name']} {$validated['father_name']} {$validated['grandfather_name']} {$validated['family_name']}" . (!empty($validated['mother_name']) ? " ({$validated['mother_name']} {$validated['mother_father_name']} {$validated['mother_grandfather_name']})" : ''));

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if ($request->hasFile('image')) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $validated['image'] = $request->file('image')->store('users', 'public');
        }

        // Handle ID photos
        if ($request->hasFile('id_photos')) {
            $photos = $user->id_photos ?? [];
            foreach ($request->file('id_photos') as $file) {
                $photos[] = $file->store('id-photos', 'public');
            }
            $validated['id_photos'] = $photos;
        }

        // Handle residence proof
        if ($request->hasFile('residence_proof')) {
            $proofs = $user->residence_proof ?? [];
            foreach ($request->file('residence_proof') as $file) {
                $proofs[] = $file->store('residence-proof', 'public');
            }
            $validated['residence_proof'] = $proofs;
        }

        // Filter out empty social links
        if (isset($validated['social_links'])) {
            $validated['social_links'] = array_filter($validated['social_links'], fn($v) => !empty($v));
            if (empty($validated['social_links'])) {
                $validated['social_links'] = null;
            }
        }

        $user->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث البيانات بنجاح',
                'redirect' => route('profile.show'),
            ]);
        }

        return redirect()->route('profile.show');
    }
}
