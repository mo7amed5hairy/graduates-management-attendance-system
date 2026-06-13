<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'national_id' => 'required|string|max:20|unique:users',
            'governorate' => 'required|string|max:100',
            'university' => 'required|string|max:255',
            'faculty' => 'required|string|max:255',
            'graduation_year' => 'required|integer|min:1950|max:' . (date('Y') + 5),
            'job_status' => 'required|string|max:100',
            'address' => 'nullable|string|max:1000',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Handle ID photos
        if ($request->hasFile('id_photos')) {
            $photos = [];
            foreach ($request->file('id_photos') as $file) {
                $photos[] = $file->store('id-photos', 'public');
            }
            $validated['id_photos'] = $photos;
        }

        // Handle residence proof
        if ($request->hasFile('residence_proof')) {
            $proofs = [];
            foreach ($request->file('residence_proof') as $file) {
                $proofs[] = $file->store('residence-proof', 'public');
            }
            $validated['residence_proof'] = $proofs;
        }

        $validated['role'] = 'user';
        $validated['approval_status'] = 'pending';

        $user = User::create($validated);

        Auth::login($user);

        $redirect = route('profile.show');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم التسجيل بنجاح. حسابك قيد المراجعة.',
                'redirect' => $redirect,
            ]);
        }

        return redirect($redirect)->with('success', 'تم التسجيل بنجاح. حسابك قيد المراجعة.');
    }

    public function login(Request $request): JsonResponse|RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && $user->status === 'inactive') {
            if ($request->ajax()) {
                throw ValidationException::withMessages([
                    'email' => ['حسابك غير نشط. يرجى التواصل مع الأدمن.'],
                ]);
            }
            return back()->withErrors(['email' => 'حسابك غير نشط. يرجى التواصل مع الأدمن.'])->withInput();
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $redirect = $user->isAdmin() ? route('admin.dashboard') : route('profile.show');

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تسجيل الدخول بنجاح',
                    'redirect' => $redirect,
                ]);
            }

            return redirect()->intended($redirect);
        }

        if ($request->ajax()) {
            throw ValidationException::withMessages([
                'email' => ['بيانات الدخول غير صحيحة'],
            ]);
        }

        return back()->withErrors(['email' => 'بيانات الدخول غير صحيحة'])->withInput();
    }

    public function logout(Request $request): JsonResponse|RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الخروج بنجاح',
                'redirect' => route('login'),
            ]);
        }

        return redirect(route('login'));
    }
}
