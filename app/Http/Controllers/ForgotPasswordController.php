<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showForm(Request $request)
    {
        $email = old('email');

        if (!$email && $request->identifier) {
            $identifier = $request->identifier;
            $isEmail = str_contains($identifier, '@');
            $user = $isEmail
                ? User::where('email', $identifier)->first()
                : User::where('national_id', $identifier)->first();
            if ($user && $user->email) {
                $email = $user->email;
            }
        }

        if (!$email && $request->token) {
            $user = User::where('access_token', $request->token)->first();
            if ($user && $user->email) {
                $email = $user->email;
            }
        }

        if (!$email && $request->cookie('access_token')) {
            $user = User::where('access_token', $request->cookie('access_token'))->first();
            if ($user && $user->email) {
                $email = $user->email;
            }
        }

        return view('auth.forgot-password', compact('email'));
    }

    public function sendResetLink(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'identifier' => 'required|string|max:255',
        ]);

        $identifier = $request->identifier;
        $isEmail = str_contains($identifier, '@');

        $user = $isEmail
            ? User::where('email', $identifier)->first()
            : User::where('national_id', $identifier)->first();

        if (!$user || !$user->email) {
            $msg = $isEmail
                ? 'البريد الإلكتروني غير مسجل في النظام'
                : 'رقم البطاقة الوطنية غير مسجل في النظام';

            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->withErrors(['identifier' => $msg]);
        }

        $token = Str::random(60);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        $user->notify(new ResetPasswordNotification($token));

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني',
            ]);
        }

        return back()->with('success', 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني');
    }
}
