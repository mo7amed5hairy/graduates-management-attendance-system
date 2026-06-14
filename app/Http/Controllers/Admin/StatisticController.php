<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class StatisticController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')
            ->select('id', 'name', 'gender', 'graduation_year', 'governorate', 'status', 'approval_status', 'created_at')
            ->latest()
            ->get();

        $genders = User::where('role', 'user')->whereNotNull('gender')->distinct()->pluck('gender')->sort();
        $years = User::where('role', 'user')->whereNotNull('graduation_year')->distinct()->pluck('graduation_year')->sort();
        $governorates = User::where('role', 'user')->whereNotNull('governorate')->distinct()->pluck('governorate')->sort();

        return view('admin.statistics.index', compact('users', 'genders', 'years', 'governorates'));
    }
}
