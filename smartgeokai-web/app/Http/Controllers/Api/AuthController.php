<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string'],
        ]);

        $user = User::where(
            'username',
            $credentials['username']
        )->first();

        if (
            ! $user ||
            ! Hash::check($credentials['password'], $user->password)
        ) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'username' => [
                    'Akun kamu telah dinonaktifkan. Hubungi administrator.',
                ],
            ]);
        }

        // API mobile khusus petugas
        if ($user->role !== 'petugas') {
            throw ValidationException::withMessages([
                'username' => [
                    'Aplikasi mobile ini khusus untuk Petugas.',
                ],
            ]);
        }

        $token = $user->createToken(
            $credentials['device_name']
        )->plainTextToken;

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

    // Logout
    public function logout(Request $request)
    {
        // Cabut token perangkat yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    // Informasi user yang sedang login
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

    // Update profil
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'full_name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'nip' => [
                'required',
                'string',
                'max:25',
                Rule::unique('users', 'nip')->ignore($user->id),
            ],
        ]);

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'user' => $user->fresh(),
        ]);
    }

    // Ganti password
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => [
                'required',
                'string',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'different:current_password',
            ],
        ]);

        $user = $request->user();

        if (
            ! Hash::check(
                $request->current_password,
                $user->password
            )
        ) {
            throw ValidationException::withMessages([
                'current_password' => [
                    'Password lama tidak sesuai.',
                ],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diganti.',
        ]);
    }
}
