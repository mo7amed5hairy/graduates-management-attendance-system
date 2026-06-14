<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckDetailsController extends Controller
{
    public function index(Request $request): View
    {
        $user = null;

        if (auth()->check()) {
            $user = auth()->user();
        } elseif ($request->cookie('access_token')) {
            $user = User::where('access_token', $request->cookie('access_token'))->first();
        }

        return view('check-details', compact('user'));
    }

    public function show(string $token): View
    {
        $user = User::where('access_token', $token)->firstOrFail();

        return view('check-details', compact('user'));
    }
}
