<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
    public function index()
    {
        $requests = ProfileChangeRequest::with('user', 'attachments')
            ->where('status', 'pending')
            ->latest()
            ->get();
        return view('admin.profile-change-requests.index', compact('requests'));
    }

    public function show(ProfileChangeRequest $changeRequest)
    {
        $changeRequest->load('user', 'attachments', 'reviewer');
        return view('admin.profile-change-requests.show', compact('changeRequest'));
    }

    public function approve(Request $request, ProfileChangeRequest $changeRequest): JsonResponse|RedirectResponse
    {
        if ($changeRequest->status !== 'pending') {
            return back()->with('error', 'تمت معالجة هذا الطلب مسبقاً');
        }

        $request->validate(['admin_notes' => 'required|string|max:1000']);

        $this->approveRequest($changeRequest, $request->input('admin_notes'));

        $msg = '✅ تمت الموافقة على الطلب وتحديث بيانات المستخدم';

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('admin.profile-change-requests.show', $changeRequest)->with('success', $msg);
    }

    public function approveRequest(ProfileChangeRequest $changeRequest, ?string $notes = null): void
    {
        $user = $changeRequest->user;
        $data = $changeRequest->requested_data;

        if ($data) {
            if (isset($data['_new_image'])) {
                $imagePath = $data['_new_image'];
                unset($data['_new_image']);
                if ($user->image) {
                    Storage::disk('public')->delete($user->image);
                }
                $data['image'] = $imagePath;
            }
            if (isset($data['_new_id_photos'])) {
                $idPhotoPaths = [];
                foreach ($data['_new_id_photos'] as $side => $path) {
                    $idPhotoPaths[] = $path;
                }
                unset($data['_new_id_photos']);
                // Replace old ID photos (delete old files, set new ones)
                $oldPhotos = $user->id_photos ?? [];
                foreach ($oldPhotos as $oldPhoto) {
                    Storage::disk('public')->delete($oldPhoto);
                }
                $data['id_photos'] = $idPhotoPaths;
            }
            if (isset($data['_new_graduation_attachments'])) {
                unset($data['_new_graduation_attachments']);
            }
            $user->update($data);
            $user->save();
        }

        // Handle graduation attachments replacement
        if (isset($changeRequest->requested_data['_new_graduation_attachments'])) {
            $newAttachments = $changeRequest->requested_data['_new_graduation_attachments'];
            // Delete old graduation attachment files
            $oldAttachments = $user->graduation_attachments ?? [];
            foreach ($oldAttachments as $oldAtt) {
                if (isset($oldAtt['file_path'])) {
                    Storage::disk('public')->delete($oldAtt['file_path']);
                }
            }
            // Set new attachments (replace, not append)
            $user->graduation_attachments = $newAttachments;
            $user->save();
        }

        $changeRequest->update([
            'status' => 'approved',
            'admin_notes' => $notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'profile_change_approved',
            'data' => [
                'message' => 'تمت الموافقة على طلب تعديل بياناتك',
                'title' => 'تمت الموافقة',
                'change_request_id' => $changeRequest->id,
            ],
        ]);
    }

    public function bulkApprove(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:profile_change_requests,id',
        ]);

        $count = 0;
        foreach ($validated['ids'] as $id) {
            $cr = ProfileChangeRequest::find($id);
            if ($cr && $cr->status === 'pending') {
                $this->approveRequest($cr);
                $count++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "تم قبول {$count} طلب بنجاح",
        ]);
    }

    public function reject(Request $request, ProfileChangeRequest $changeRequest): JsonResponse|RedirectResponse
    {
        if ($changeRequest->status !== 'pending') {
            return back()->with('error', 'تمت معالجة هذا الطلب مسبقاً');
        }

        $request->validate(['admin_notes' => 'required|string|max:1000']);

        // Delete uploaded image if any
        $data = $changeRequest->requested_data;
        if ($data && isset($data['_new_image'])) {
            Storage::disk('public')->delete($data['_new_image']);
        }

        // Delete uploaded ID photos if any
        if ($data && isset($data['_new_id_photos'])) {
            foreach ($data['_new_id_photos'] as $side => $path) {
                Storage::disk('public')->delete($path);
            }
        }

        // Delete uploaded graduation attachments if any
        if ($data && isset($data['_new_graduation_attachments'])) {
            foreach ($data['_new_graduation_attachments'] as $att) {
                if (isset($att['file_path'])) {
                    Storage::disk('public')->delete($att['file_path']);
                }
            }
        }

        // Also delete any old-style attachments
        foreach ($changeRequest->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        }
        $changeRequest->attachments()->delete();

        $changeRequest->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Notify user
        Notification::create([
            'user_id' => $changeRequest->user_id,
            'type' => 'profile_change_rejected',
            'data' => [
                'message' => 'لم تتم الموافقة على طلب تعديل بياناتك. السبب: ' . $request->admin_notes,
                'title' => 'تم الرفض',
                'change_request_id' => $changeRequest->id,
            ],
        ]);

        $msg = 'تم رفض الطلب';

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $msg]);
        }

        return redirect()->route('admin.profile-change-requests.show', $changeRequest)->with('success', $msg);
    }
}
