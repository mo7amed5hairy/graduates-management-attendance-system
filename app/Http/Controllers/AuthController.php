<?php

namespace App\Http\Controllers;

use App\Models\Governorate;
use App\Models\Notification;
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
            'first_name' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:255',
            'grandfather_name' => 'nullable|string|max:255',
            'family_name' => 'required|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mother_father_name' => 'nullable|string|max:255',
            'mother_grandfather_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|regex:/^077\d{8}$/',
            'national_id' => 'required|string|max:20|unique:users',
            'governorate' => 'required|exists:governorates,id',
            'date_of_birth' => 'nullable|integer|min:1900|max:' . date('Y'),
            'graduation_year' => 'required|integer|min:1950|max:' . (date('Y') + 5),
            'job_status' => 'required|string|max:100',
            'gender' => 'nullable|string|in:ذكر,أنثى',
            'address' => 'required|string|max:1000',
            'qualification_id' => 'required|exists:qualifications,id',
            'qualification_faculty_id' => 'required|exists:qualification_faculties,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Concatenate name parts
        $validated['name'] = trim("{$validated['first_name']} {$validated['father_name']} {$validated['grandfather_name']} {$validated['family_name']}" . ($validated['mother_name'] ? " ({$validated['mother_name']} {$validated['mother_father_name']} {$validated['mother_grandfather_name']})" : ''));

        // Calculate age from date_of_birth (year only)
        if (!empty($validated['date_of_birth'])) {
            $validated['date_of_birth'] = $validated['date_of_birth'] . '-01-01';
            $validated['age'] = \Carbon\Carbon::parse($validated['date_of_birth'])->age;
        }

        $validated['role'] = 'user';
        $validated['approval_status'] = 'pending';
        $validated['access_token'] = bin2hex(random_bytes(32));

        $newUser = User::create($validated);

        // Notify all admins about new registration
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'new_registration',
                'data' => [
                    'title' => 'تسجيل خريج جديد',
                    'message' => "قام الخريج {$newUser->name} بالتسجيل. يرجى مراجعة الحساب.",
                    'user_id' => $newUser->id,
                    'user_name' => $newUser->name,
                    'user_email' => $newUser->email ?? '',
                ],
            ]);
        }

        $redirect = route('login');

        $accessLink = url('/checkmydetails/' . $newUser->access_token);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم التسجيل بنجاح. حسابك قيد المراجعة، سيتم إشعارك عند الاعتماد.',
                'redirect' => $redirect,
                'access_link' => $accessLink,
            ]);
        }

        return redirect($redirect)->with('success', 'تم التسجيل بنجاح. حسابك قيد المراجعة، سيتم إشعارك عند الاعتماد.');
    }

    public function login(Request $request): JsonResponse|RedirectResponse
    {
        $credentials = $request->validate([
            'national_id' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $credentials['national_id'];
        $isEmail = str_contains($login, '@');
        $field = $isEmail ? 'email' : 'national_id';

        $user = User::where($field, $login)->first();

        if ($user) {
            if ($user->approval_status === 'pending') {
                $msg = 'حسابك قيد المراجعة. يرجى الانتظار حتى يتم اعتماد حسابك.';
                if ($request->ajax()) {
                    throw ValidationException::withMessages(['national_id' => [$msg]]);
                }
                return back()->withErrors(['national_id' => $msg])->withInput();
            }

            if ($user->approval_status === 'rejected') {
                $reason = $user->rejection_reason ? ' السبب: ' . $user->rejection_reason : '';
                $msg = 'تم رفض حسابك.' . $reason;
                if ($request->ajax()) {
                    throw ValidationException::withMessages(['national_id' => [$msg]]);
                }
                return back()->withErrors(['national_id' => $msg])->withInput();
            }

            if ($user->status === 'inactive') {
                $msg = 'حسابك غير نشط. يرجى التواصل مع الأدمن.';
                if ($request->ajax()) {
                    throw ValidationException::withMessages(['national_id' => [$msg]]);
                }
                return back()->withErrors(['national_id' => $msg])->withInput();
            }
        }

        if (Auth::attempt([$field => $login, 'password' => $credentials['password']], $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if (!$user->access_token) {
                $user->access_token = bin2hex(random_bytes(32));
                $user->save();
            }

            $redirect = $user->isAdmin() ? route('admin.dashboard') : route('profile.show');

            $cookie = cookie('access_token', $user->access_token, 60 * 24 * 365);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تسجيل الدخول بنجاح',
                    'redirect' => $redirect,
                    'access_token' => $user->access_token,
                ])->cookie($cookie);
            }

            return redirect()->intended($redirect)->withCookie($cookie);
        }

        if ($request->ajax()) {
            throw ValidationException::withMessages([
                'national_id' => ['بيانات الدخول غير صحيحة'],
            ]);
        }

        return back()->withErrors(['national_id' => 'بيانات الدخول غير صحيحة'])->withInput();
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
