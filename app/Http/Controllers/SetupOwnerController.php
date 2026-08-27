<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SetupOwnerController extends Controller
{
    public function show()
    {
        if (User::count() > 0) {
            return redirect()->route('login');
        }

        return view('auth.setup-owner');
    }

    public function store(Request $request)
    {
        if (User::count() > 0) {
            return redirect()->route('login')->with('error', 'Setup sudah pernah dilakukan sebelumnya.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(6)],
            'bio' => ['nullable', 'string', 'max:1000'],
            'avatar_url' => ['nullable', 'string', 'max:500'],
            'tmdb_api_key' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'bio' => $validated['bio'] ?? 'Film Enthusiast & Cinephile. Mendokumentasikan ulasan dan perjalanan sinematik pribadi.',
            'avatar_url' => $validated['avatar_url'] ?? null,
            'tmdb_api_key' => $validated['tmdb_api_key'] ?? null,
            'is_setup_completed' => true,
        ]);

        Auth::login($user);

        return redirect()->route('admin.dashboard')->with('success', 'Selamat datang, ' . $user->name . '! Akun pemilik berhasil dikonfigurasi.');
    }
}
