<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\GovernorateController;
use App\Http\Controllers\Admin\GraduateController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\InstitutionController;
use App\Http\Controllers\Admin\InstitutionTypeController;
use App\Http\Controllers\Admin\ProfileChangeRequestController as AdminProfileChangeRequestController;
use App\Http\Controllers\Admin\QualificationController;
use App\Http\Controllers\Admin\QualificationFacultyController;
use App\Http\Controllers\Admin\StatisticController;
use App\Http\Controllers\Admin\SubAdminController;
use App\Http\Controllers\Admin\UniversityTypeController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckDetailsController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\Admin\PortalController as AdminPortalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\ProfileChangeRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserEventController;
use App\Http\Controllers\UserTaskController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

// Serve storage files (bypasses symlink issues on Windows/Laragon)
Route::get('/files/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*')->name('file.serve');

Route::get('/', [PortalController::class, 'index'])->name('portal.home');

// Public route - check personal details without login
Route::get('/checkmydetails', [CheckDetailsController::class, 'index'])->name('check-details');
Route::get('/checkmydetails/{token}', [CheckDetailsController::class, 'show'])->name('check-details.token');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Password reset
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// API routes for location cascading dropdowns
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/governorates', [LocationController::class, 'governorates'])->name('governorates');
    Route::get('/institution-types', [LocationController::class, 'institutionTypes'])->name('institution-types');
    Route::get('/university-types', [LocationController::class, 'universityTypes'])->name('university-types');
    Route::get('/governorates/{governorate}/institution-type/{institutionType}/university-type/{universityType}/institutions', [LocationController::class, 'institutionsByGovernorate'])->name('institutions.by-filters');
    Route::get('/institutions/{institution}/departments', [LocationController::class, 'departmentsByInstitution'])->name('departments.by-institution');
    Route::get('/qualifications', [LocationController::class, 'qualifications'])->name('qualifications');
    Route::get('/qualifications/{qualification}/faculties', [LocationController::class, 'facultiesByQualification'])->name('qualifications.faculties');
});

// Auth routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileChangeRequestController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::post('/change-request', [ProfileChangeRequestController::class, 'submit'])->name('change-request.submit');
    });

    // User home
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Admin routes
    Route::middleware([AdminMiddleware::class, 'admin.permission'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Governorates
        Route::prefix('governorates')->name('governorates.')->group(function () {
            Route::get('/', [GovernorateController::class, 'index'])->name('index');
            Route::post('/', [GovernorateController::class, 'store'])->name('store');
            Route::put('/{governorate}', [GovernorateController::class, 'update'])->name('update');
            Route::delete('/{governorate}', [GovernorateController::class, 'destroy'])->name('destroy');
        });

        // Institution Types (جامعة / معهد)
        Route::prefix('institution-types')->name('institution-types.')->group(function () {
            Route::get('/', [InstitutionTypeController::class, 'index'])->name('index');
            Route::post('/', [InstitutionTypeController::class, 'store'])->name('store');
            Route::put('/{institutionType}', [InstitutionTypeController::class, 'update'])->name('update');
            Route::delete('/{institutionType}', [InstitutionTypeController::class, 'destroy'])->name('destroy');
        });

        // University Types (حكومية / أهلية)
        Route::prefix('university-types')->name('university-types.')->group(function () {
            Route::get('/', [UniversityTypeController::class, 'index'])->name('index');
            Route::post('/', [UniversityTypeController::class, 'store'])->name('store');
            Route::put('/{universityType}', [UniversityTypeController::class, 'update'])->name('update');
            Route::delete('/{universityType}', [UniversityTypeController::class, 'destroy'])->name('destroy');
        });

        // Institutions (universities & institutes)
        Route::prefix('institutions')->name('institutions.')->group(function () {
            Route::get('/', [InstitutionController::class, 'index'])->name('index');
            Route::post('/', [InstitutionController::class, 'store'])->name('store');
            Route::put('/{institution}', [InstitutionController::class, 'update'])->name('update');
            Route::delete('/{institution}', [InstitutionController::class, 'destroy'])->name('destroy');
        });

        // Departments
        Route::prefix('departments')->name('departments.')->group(function () {
            Route::get('/', [DepartmentController::class, 'index'])->name('index');
            Route::post('/', [DepartmentController::class, 'store'])->name('store');
            Route::put('/{department}', [DepartmentController::class, 'update'])->name('update');
            Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy');
        });

        // Statistics
        Route::get('/statistics', [StatisticController::class, 'index'])->name('statistics.index');

        // Profile Change Requests
        Route::prefix('profile-change-requests')->name('profile-change-requests.')->group(function () {
            Route::get('/', [AdminProfileChangeRequestController::class, 'index'])->name('index');
            Route::get('/{changeRequest}', [AdminProfileChangeRequestController::class, 'show'])->name('show');
            Route::post('/bulk-approve', [AdminProfileChangeRequestController::class, 'bulkApprove'])->name('bulk-approve');
            Route::post('/{changeRequest}/approve', [AdminProfileChangeRequestController::class, 'approve'])->name('approve');
            Route::post('/{changeRequest}/reject', [AdminProfileChangeRequestController::class, 'reject'])->name('reject');
        });

        // Qualifications
        Route::prefix('qualifications')->name('qualifications.')->group(function () {
            Route::get('/', [QualificationController::class, 'index'])->name('index');
            Route::post('/', [QualificationController::class, 'store'])->name('store');
            Route::put('/{qualification}', [QualificationController::class, 'update'])->name('update');
            Route::delete('/{qualification}', [QualificationController::class, 'destroy'])->name('destroy');
        });

        // Qualification Faculties
        Route::prefix('qualification-faculties')->name('qualification-faculties.')->group(function () {
            Route::get('/', [QualificationFacultyController::class, 'index'])->name('index');
            Route::post('/', [QualificationFacultyController::class, 'store'])->name('store');
            Route::put('/{qualificationFaculty}', [QualificationFacultyController::class, 'update'])->name('update');
            Route::delete('/{qualificationFaculty}', [QualificationFacultyController::class, 'destroy'])->name('destroy');
        });

        // Users management (approval / suspend / delete)
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/data', [UserController::class, 'data'])->name('data');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::post('/bulk-activate', [UserController::class, 'bulkActivate'])->name('bulk-activate');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
            Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        });
        // Quick user details JSON (used in users and graduates tables)
        Route::get('/user-details/{user}', function (App\Models\User $user) {
            $user->load('qualification', 'qualificationFaculty', 'approvedBy');
            return response()->json([
                'success' => true,
                'data' => $user->toArray() + [
                    'governorate_name' => $user->governorate_name,
                    'image_url' => $user->image_url,
                    'approved_by_name' => $user->approvedBy?->name,
                    'qualification_name' => $user->qualification?->name,
                    'faculty_name' => $user->qualificationFaculty?->name,
                    'graduation_attachments' => $user->graduation_attachments ?? [],
                ],
            ]);
        })->name('user-details');

        // Points management
        Route::prefix('points')->name('points.')->group(function () {
            Route::get('/', [PointController::class, 'index'])->name('index');
            Route::post('/', [PointController::class, 'store'])->name('store');
            Route::get('/user/{user}/points', [PointController::class, 'getUserPoints'])->name('user.points');
        });

        // Tasks management
        Route::prefix('tasks')->name('tasks.')->group(function () {
            Route::get('/', [TaskController::class, 'index'])->name('index');
            Route::post('/', [TaskController::class, 'store'])->name('store');
            Route::get('/{task}', [TaskController::class, 'show'])->name('show');
            Route::put('/{task}', [TaskController::class, 'update'])->name('update');
            Route::delete('/{task}', [TaskController::class, 'destroy'])->name('destroy');
        });

        // Graduates management
        Route::prefix('graduates')->name('graduates.')->group(function () {
            Route::get('/', [GraduateController::class, 'index'])->name('index');
            Route::get('/stats', [GraduateController::class, 'stats'])->name('stats');
            Route::get('/{user}', [GraduateController::class, 'show'])->name('show');
            Route::post('/{user}/approve', [GraduateController::class, 'approve'])->name('approve');
            Route::post('/{user}/reject', [GraduateController::class, 'reject'])->name('reject');
        });

        // Events management
        Route::prefix('events')->name('events.')->group(function () {
            Route::get('/', [EventController::class, 'index'])->name('index');
            Route::get('/create', [EventController::class, 'create'])->name('create');
            Route::post('/', [EventController::class, 'store'])->name('store');
            Route::get('/{event}', [EventController::class, 'show'])->name('show');
            Route::get('/{event}/edit', [EventController::class, 'edit'])->name('edit');
            Route::put('/{event}', [EventController::class, 'update'])->name('update');
            Route::delete('/{event}', [EventController::class, 'destroy'])->name('destroy');
        });

        // Attendance
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [AttendanceController::class, 'index'])->name('index');
            Route::get('/event/{event}', [AttendanceController::class, 'event'])->name('event');
            Route::get('/event/{event}/scan', [AttendanceController::class, 'scan'])->name('scan');
            Route::post('/mark', [AttendanceController::class, 'markAttendance'])->name('mark');
            Route::get('/search-graduates', [AttendanceController::class, 'searchGraduates'])->name('search');
            Route::get('/event/{event}/attendees', [AttendanceController::class, 'getAttendees'])->name('attendees');
        });

        // Import
        Route::prefix('import')->name('import.')->group(function () {
            Route::get('/', [ImportController::class, 'index'])->name('index');
            Route::post('/', [ImportController::class, 'import'])->name('process');
        });

        // Export (JSON data — XLSX conversion is done client-side by SheetJS)
        Route::prefix('export')->name('export.')->group(function () {
            Route::get('/users', [ExportController::class, 'usersJson'])->name('users');
        });

        // Portal management
        Route::prefix('portal')->name('portal.')->group(function () {
            Route::get('/', [AdminPortalController::class, 'index'])->name('index');
            Route::post('/settings', [AdminPortalController::class, 'updateSettings'])->name('settings');
            Route::post('/news', [AdminPortalController::class, 'storeNews'])->name('news.store');
            Route::delete('/news/{portalNews}', [AdminPortalController::class, 'destroyNews'])->name('news.destroy');
            Route::get('/news/{portalNews}/toggle', [AdminPortalController::class, 'toggleNews'])->name('news.toggle');
            Route::post('/videos', [AdminPortalController::class, 'storeVideo'])->name('videos.store');
            Route::delete('/videos/{portalVideo}', [AdminPortalController::class, 'destroyVideo'])->name('videos.destroy');
            Route::get('/videos/{portalVideo}/toggle', [AdminPortalController::class, 'toggleVideo'])->name('videos.toggle');
            Route::post('/faqs', [AdminPortalController::class, 'storeFaq'])->name('faqs.store');
            Route::delete('/faqs/{portalFaq}', [AdminPortalController::class, 'destroyFaq'])->name('faqs.destroy');
        });

        // Sub-admins & permissions
        Route::prefix('sub-admins')->name('sub-admins.')->group(function () {
            Route::get('/', [SubAdminController::class, 'index'])->name('index');
            Route::post('/', [SubAdminController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [SubAdminController::class, 'edit'])->name('edit');
            Route::put('/{user}', [SubAdminController::class, 'update'])->name('update');
            Route::delete('/{user}', [SubAdminController::class, 'destroy'])->name('destroy');
        });
    });

    // User tasks
    Route::get('/tasks', [UserTaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}', [UserTaskController::class, 'show'])->name('tasks.show');

    // User events
    Route::get('/events', [UserEventController::class, 'index'])->name('events.index');
    Route::get('/events/qr/{attendance}', [UserEventController::class, 'qrCode'])->name('events.qr');

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
        Route::get('/latest', [NotificationController::class, 'latest'])->name('latest');
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    });
});
