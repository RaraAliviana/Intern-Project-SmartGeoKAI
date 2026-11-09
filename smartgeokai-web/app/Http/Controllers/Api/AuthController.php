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
    /**
     * Login khusus pengguna dengan role petugas.
     */
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

        // API mobile hanya dapat digunakan oleh petugas.
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
                    'nipp' => $user->nipp,
                    'role' => $user->role,
                    'is_active' => $user->is_active,
                    'province_id' => $user->province_id,
                    'province_name' => $user->province?->name,
                ],
            ],
        ]);
    }

    /**
     * Logout dan menghapus token perangkat yang sedang digunakan.
     */
    public function logout(Request $request)
    {
        $request->user()
            ->currentAccessToken()
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * Mengambil informasi profil pengguna yang sedang login.
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'message' => 'Data profil berhasil diambil.',
            'data' => [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'nipp' => $user->nipp,
                'role' => $user->role,
                'is_active' => $user->is_active,
                'province_id' => $user->province_id,
                'province_name' => $user->province?->name,
            ],
        ]);
    }

    /**
     * Memperbarui nama lengkap dan NIPP pengguna.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'full_name' => [
                'required',
                'string',
                'min:3',
                'max:' . User::FULL_NAME_MAX,
            ],

            'nipp' => [
                'bail',
                'required',
                'string',
                'digits_between:1,' . User::NIPP_MAX,
                Rule::unique('users', 'nipp')->ignore($user->id),
            ],
        ], [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'full_name.min' => 'Nama lengkap minimal 3 karakter.',
            'full_name.max' => 'Nama lengkap maksimal ' .
                User::FULL_NAME_MAX . ' karakter.',

            'nipp.required' => 'NIPP wajib diisi.',
            'nipp.digits_between' => 'NIPP harus berupa angka dengan maksimal ' .
                User::NIPP_MAX . ' digit.',
            'nipp.unique' => 'NIPP sudah digunakan oleh pengguna lain.',
        ]);

        $user->update($data);

        // Ambil ulang data terbaru dari database.
        $updatedUser = $user->fresh();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'user' => [
                'id' => $updatedUser->id,
                'full_name' => $updatedUser->full_name,
                'username' => $updatedUser->username,
                'nipp' => $updatedUser->nipp,
                'role' => $updatedUser->role,
                'is_active' => $updatedUser->is_active,
                'province_id' => $updatedUser->province_id,
                'province_name' => $updatedUser->province?->name,
            ],
        ]);
    }

    /**
     * Mengganti password pengguna.
     */
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
