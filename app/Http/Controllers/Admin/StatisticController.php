<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Governorate;
use App\Models\User;

class StatisticController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')
            ->select('id', 'name', 'gender', 'graduation_year', 'governorate', 'status', 'approval_status', 'created_at')
            ->latest()
            ->get();

        // Resolve any numeric governorate IDs to names
        $govMap = Governorate::pluck('name', 'id');
        $users->each(function ($u) use ($govMap) {
            if ($u->governorate && is_numeric($u->governorate) && isset($govMap[$u->governorate])) {
                $u->governorate = $govMap[$u->governorate];
            }
        });

        $genders = User::where('role', 'user')->whereNotNull('gender')->distinct()->pluck('gender')->sort();
        $years = User::where('role', 'user')->whereNotNull('graduation_year')->distinct()->pluck('graduation_year')->sort();
        $governorates = User::where('role', 'user')->whereNotNull('governorate')->distinct()->pluck('governorate')->sort();

        // Also add governorate names from the governorates table for the filter dropdown
        $allGovernorates = Governorate::orderBy('name')->pluck('name');

        return view('admin.statistics.index', compact('users', 'genders', 'years', 'governorates', 'allGovernorates'));
    }
}
