<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\GraduateController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PointController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserEventController;
use App\Http\Controllers\UserTaskController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('home');
    }
    return redirect()->route('login');
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Auth routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
    });

    // User home
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Admin routes
    Route::middleware(AdminMiddleware::class)->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Users management (keep for admin user management)
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });

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
