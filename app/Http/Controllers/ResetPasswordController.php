<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showForm(Request $request, string $token)
    {
        $email = $request->email;
        return view('auth.reset-password', compact('token', 'email'));
    }

    public function reset(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $record = DB::table('password_resets')
            ->where('email', $validated['email'])
            ->where('token', $validated['token'])
            ->first();

        if (!$record) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'رابط إعادة التعيين غير صالح أو منتهي الصلاحية',
                ], 422);
            }
            return back()->withErrors(['email' => 'رابط إعادة التعيين غير صالح أو منتهي الصلاحية']);
        }

        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_resets')->where('email', $validated['email'])->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'انتهت صلاحية رابط إعادة التعيين. يرجى طلب رابط جديد.',
                ], 422);
            }
            return back()->withErrors(['email' => 'انتهت صلاحية رابط إعادة التعيين. يرجى طلب رابط جديد.']);
        }

        $user = \App\Models\User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()->withErrors(['email' => 'المستخدم غير موجود']);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        DB::table('password_resets')->where('email', $validated['email'])->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إعادة تعيين كلمة المرور بنجاح',
                'redirect' => route('login'),
            ]);
        }

        return redirect()->route('login')->with('success', 'تم إعادة تعيين كلمة المرور بنجاح');
    }
}
