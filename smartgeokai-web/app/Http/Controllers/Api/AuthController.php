<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string'], // identitas device, mis. "Xiaomi Redmi Note 12"
        ]);

        $user = \App\Models\User::where('username', $credentials['username'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'username' => ['Akun kamu telah dinonaktifkan. Hubungi administrator.'],
            ]);
        }

        // API mobile ini KHUSUS untuk petugas — admin tetap login lewat web
        if ($user->role !== 'petugas') {
            throw ValidationException::withMessages([
                'username' => ['Aplikasi mobile ini khusus untuk Petugas.'],
            ]);
        }

        $token = $user->createToken($credentials['device_name'])->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'full_name' => $user->full_name,
                    'username' => $user->username,
                    'nip' => $user->nip,
                    'role' => $user->role,
                    'province_id' => $user->province_id,
                    'province_name' => $user->province?->name,
                ],
            ],
        ]);
    }

    public function logout(Request $request)
    {
        // Cabut hanya token yang sedang dipakai request ini (device ini saja)
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'nip' => $user->nip,
                'role' => $user->role,
                'is_active' => $user->is_active,
                'province_id' => $user->province_id,
                'province_name' => $user->province?->name,
            ],
        ]);
    }
}
