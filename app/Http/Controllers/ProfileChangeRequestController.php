<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\ProfileChangeRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileChangeRequestController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function submit(Request $request): JsonResponse|RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'grandfather_name' => 'required|string|max:255',
            'family_name' => 'required|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mother_father_name' => 'nullable|string|max:255',
            'mother_grandfather_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|regex:/^077\d{8}$/',
            'password' => 'nullable|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'address' => 'nullable|string|max:1000',
            'national_id' => 'nullable|string|max:20|unique:users,national_id,' . $user->id,
            'governorate' => 'nullable|string|max:100',
            'graduation_year' => 'nullable|integer|min:1950|max:' . (date('Y') + 5),
            'job_status' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|integer|min:1900|max:' . date('Y'),
            'gender' => 'nullable|string|in:ذكر,أنثى',
            'social_status' => 'nullable|string|in:أعزب,متزوج,مطلق,أرمل',
            'children_count' => 'nullable|integer|min:0|max:10',
            'qualification_id' => 'nullable|exists:qualifications,id',
            'qualification_faculty_id' => 'nullable|exists:qualification_faculties,id',
            'social_links' => 'nullable|array',
            'social_links.*' => 'nullable|url|max:500',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',
            'id_photo_front' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'id_photo_back' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Build the requested data (all submitted fields except file uploads, password, token)
        $requestedData = $request->except(['password', 'password_confirmation', 'image', 'attachments', 'id_photo_front', 'id_photo_back', '_token', '_method']);
        // Filter to only changed fields
        $changed = [];
        foreach ($requestedData as $key => $value) {
            $oldValue = $user->$key ?? null;
            if ($key === 'social_links') {
                $value = array_filter($value ?? [], fn($v) => !empty($v));
                $oldValue = array_filter($oldValue ?? [], fn($v) => !empty($v));
                if (empty($value)) $value = null;
                if (json_encode($value) !== json_encode($oldValue)) {
                    $changed[$key] = $value;
                }
            } elseif (is_array($value) || is_array($oldValue)) {
                if (json_encode($value) !== json_encode($oldValue)) {
                    $changed[$key] = $value;
                }
            } elseif ((string) $value !== (string) $oldValue) {
                $changed[$key] = $value;
            }
        }

        $hasPasswordChange = $request->filled('password');
        if ($hasPasswordChange) {
            $user->password = bcrypt($request->password);
            $user->save();
        }

        $hasProfileChanges = !empty($changed);
        $hasAttachments = $request->hasFile('attachments');
        $hasIdPhotos = $request->hasFile('id_photo_front') || $request->hasFile('id_photo_back');

        if (!$hasProfileChanges && !$hasAttachments && !$hasIdPhotos && !$hasPasswordChange) {
            $msg = 'لم تقم بإجراء أي تغييرات أو رفع مرفقات';
            return $request->ajax()
                ? response()->json(['success' => false, 'message' => $msg], 422)
                : back()->with('error', $msg);
        }

        // Create the change request
        $changeRequest = ProfileChangeRequest::create([
            'user_id' => $user->id,
            'requested_data' => $hasProfileChanges ? $changed : null,
            'status' => 'pending',
        ]);

        // Handle image upload (store temporarily)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('profile-change-images', 'public');
            $data = $changeRequest->requested_data ?? [];
            $data['_new_image'] = $path;
            $changeRequest->requested_data = $data;
            $changeRequest->save();
        }

        // Save attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('graduation-attachments/' . $user->id, 'public');
                $changeRequest->attachments()->create([
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }

        // Handle ID photo uploads (store temporarily in requested_data)
        if ($hasIdPhotos) {
            $data = $changeRequest->requested_data ?? [];
            $data['_new_id_photos'] = [];
            if ($request->hasFile('id_photo_front')) {
                $data['_new_id_photos']['front'] = $request->file('id_photo_front')->store('id-photos/' . $user->id, 'public');
            }
            if ($request->hasFile('id_photo_back')) {
                $data['_new_id_photos']['back'] = $request->file('id_photo_back')->store('id-photos/' . $user->id, 'public');
            }
            $changeRequest->requested_data = $data;
            $changeRequest->save();
        }

        // Notify admins
        $admins = User::where('role', 'admin')->get();
        $parts = [];
        if ($hasProfileChanges) $parts[] = 'تعديل بيانات';
        if ($hasPasswordChange) $parts[] = 'تغيير كلمة المرور';
        if ($hasAttachments) $parts[] = 'رفع مرفقات';
        if ($hasIdPhotos) $parts[] = 'رفع صور البطاقة';
        $changeType = implode(' و ', $parts);
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'profile_change_request',
                'data' => [
                    'message' => "المستخدم {$user->name} طلب {$changeType}",
                    'title' => 'طلب تعديل بيانات',
                    'change_request_id' => $changeRequest->id,
                ],
            ]);
        }

        $msg = 'تم إرسال طلب التعديل للمراجعة بنجاح';

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg, 'redirect' => route('profile.show')]);
        }

        return redirect()->route('profile.show')->with('success', $msg);
    }
}
