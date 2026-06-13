<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Institution;
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
            'national_id' => 'required|string|max:20|unique:users',
            'governorate' => 'required|exists:governorates,id',
            'institution_type' => 'required|exists:institution_types,id',
            'university_type' => 'required|exists:university_types,id',
            'institution_id' => 'required|exists:institutions,id',
            'department_id' => 'required|exists:departments,id',
            'graduation_year' => 'required|integer|min:1950|max:' . (date('Y') + 5),
            'job_status' => 'required|string|max:100',
            'age' => 'nullable|integer|min:1|max:150',
            'gender' => 'nullable|string|in:ذكر,أنثى',
            'address' => 'nullable|string|max:1000',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $institution = Institution::with('governorate')->find($validated['institution_id']);
        $department = Department::find($validated['department_id']);

        $validated['governorate'] = $institution->governorate->name;
        $validated['university'] = $institution->name;
        $validated['faculty'] = $department->name;

        unset($validated['institution_type'], $validated['university_type'], $validated['institution_id'], $validated['department_id']);

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
                    'user_email' => $newUser->email,
                ],
            ]);
        }

        $redirect = route('login');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'تم التسجيل بنجاح. حسابك قيد المراجعة، سيتم إشعارك عند الاعتماد.',
                'redirect' => $redirect,
            ]);
        }

        return redirect($redirect)->with('success', 'تم التسجيل بنجاح. حسابك قيد المراجعة، سيتم إشعارك عند الاعتماد.');
    }

    public function login(Request $request): JsonResponse|RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user) {
            if ($user->approval_status === 'pending') {
                $msg = 'حسابك قيد المراجعة. يرجى الانتظار حتى يتم اعتماد حسابك.';
                if ($request->ajax()) {
                    throw ValidationException::withMessages(['email' => [$msg]]);
                }
                return back()->withErrors(['email' => $msg])->withInput();
            }

            if ($user->approval_status === 'rejected') {
                $reason = $user->rejection_reason ? ' السبب: ' . $user->rejection_reason : '';
                $msg = 'تم رفض حسابك.' . $reason;
                if ($request->ajax()) {
                    throw ValidationException::withMessages(['email' => [$msg]]);
                }
                return back()->withErrors(['email' => $msg])->withInput();
            }

            if ($user->status === 'inactive') {
                $msg = 'حسابك غير نشط. يرجى التواصل مع الأدمن.';
                if ($request->ajax()) {
                    throw ValidationException::withMessages(['email' => [$msg]]);
                }
                return back()->withErrors(['email' => $msg])->withInput();
            }
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
