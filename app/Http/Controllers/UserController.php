<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;

class UserController extends Controller
{
    public function showRegister()
    {
        return Inertia::render('Auth/Register');
    }

    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    public function register(Request $request)
    {
        $data = $request->only(['name', 'email', 'password']);
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return Inertia::location(route('dashboard'));
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (Auth::attempt($credentials)) {
            return Inertia::location(route('dashboard'));
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout()
    {
        Auth::logout();
        return Inertia::location(route('login'));
    }
}
