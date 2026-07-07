<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminPermission
{
    private array $permissionMap = [
        'admin.dashboard' => 'dashboard',

        'admin.governorates.index' => 'governorates.view',
        'admin.governorates.store' => 'governorates.create',
        'admin.governorates.update' => 'governorates.edit',
        'admin.governorates.destroy' => 'governorates.delete',

        'admin.institution-types.index' => 'institution-types.view',
        'admin.institution-types.store' => 'institution-types.create',
        'admin.institution-types.update' => 'institution-types.edit',
        'admin.institution-types.destroy' => 'institution-types.delete',

        'admin.university-types.index' => 'university-types.view',
        'admin.university-types.store' => 'university-types.create',
        'admin.university-types.update' => 'university-types.edit',
        'admin.university-types.destroy' => 'university-types.delete',

        'admin.institutions.index' => 'institutions.view',
        'admin.institutions.store' => 'institutions.create',
        'admin.institutions.update' => 'institutions.edit',
        'admin.institutions.destroy' => 'institutions.delete',

        'admin.departments.index' => 'departments.view',
        'admin.departments.store' => 'departments.create',
        'admin.departments.update' => 'departments.edit',
        'admin.departments.destroy' => 'departments.delete',

        'admin.statistics.index' => 'statistics.view',

        'admin.qualifications.index' => 'qualifications.view',
        'admin.qualifications.store' => 'qualifications.create',
        'admin.qualifications.update' => 'qualifications.edit',
        'admin.qualifications.destroy' => 'qualifications.delete',

        'admin.qualification-faculties.index' => 'qualification-faculties.view',
        'admin.qualification-faculties.store' => 'qualification-faculties.create',
        'admin.qualification-faculties.update' => 'qualification-faculties.edit',
        'admin.qualification-faculties.destroy' => 'qualification-faculties.delete',

        'admin.users.index' => 'users.view',
        'admin.users.store' => 'users.create',
        'admin.users.update' => 'users.edit',
        'admin.users.destroy' => 'users.delete',
        'admin.users.toggle-status' => 'users.toggle-status',
        'admin.users.bulk-activate' => 'users.bulk-activate',

        'admin.points.index' => 'points.view',
        'admin.points.store' => 'points.create',

        'admin.tasks.index' => 'tasks.view',
        'admin.tasks.store' => 'tasks.create',
        'admin.tasks.update' => 'tasks.edit',
        'admin.tasks.destroy' => 'tasks.delete',

        'admin.graduates.index' => 'graduates.view',
        'admin.graduates.show' => 'graduates.show',
        'admin.graduates.approve' => 'graduates.approve',

        'admin.events.index' => 'events.view',
        'admin.events.create' => 'events.create',
        'admin.events.store' => 'events.create',
        'admin.events.edit' => 'events.edit',
        'admin.events.update' => 'events.edit',
        'admin.events.destroy' => 'events.delete',

        'admin.attendance.index' => 'attendance.view',
        'admin.attendance.event' => 'attendance.view',
        'admin.attendance.scan' => 'attendance.scan',
        'admin.attendance.mark' => 'attendance.mark',

        'admin.import.index' => 'import.view',
        'admin.import.process' => 'import.process',

        'admin.export.users' => 'export.data',

        'admin.sub-admins.index' => 'sub-admins.view',
        'admin.sub-admins.store' => 'sub-admins.create',
        'admin.sub-admins.update' => 'sub-admins.edit',
        'admin.sub-admins.destroy' => 'sub-admins.delete',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isAdmin()) {
            abort(403, 'غير مصرح بالدخول');
        }

        $routeName = $request->route()?->getName();

        if ($routeName && isset($this->permissionMap[$routeName])) {
            $permission = $this->permissionMap[$routeName];

            if (!$user->hasPermission($permission)) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'ليس لديك صلاحية للوصول إلى هذه الصفحة',
                    ], 403);
                }
                abort(403, 'ليس لديك صلاحية للوصول إلى هذه الصفحة');
            }
        }

        return $next($request);
    }
}
