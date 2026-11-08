<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function index()
    {
        return view('admin.profile.index', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:' . User::FULL_NAME_MAX],
            'nipp' => [
                'bail',
                'required',
                'string',
                'digits_between:1,' . User::NIPP_MAX,
                Rule::unique('users', 'nipp')->ignore($user->id),
            ],
        ], [
            'full_name.required' => 'Nama lengkap wajib diisi.',
            'full_name.max' => 'Nama lengkap maksimal ' . User::FULL_NAME_MAX . ' karakter.',
            'nipp.required' => 'NIPP wajib diisi.',
            'nipp.digits_between' => 'NIPP harus berupa angka (maksimal ' . User::NIPP_MAX . ' digit).',
            'nipp.unique' => 'NIPP ini sudah digunakan oleh pengguna lain.',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Password lama yang kamu masukkan salah.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diganti.');
    }
}