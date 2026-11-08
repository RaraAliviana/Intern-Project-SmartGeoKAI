<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menentukan role yang boleh diberikan oleh pengguna yang login.
     */
    private function allowedRoles(): array
    {
        return Auth::user()?->role === 'superadmin'
            ? ['admin', 'petugas']
            : ['petugas'];
    }

    /**
     * Pesan validasi (dipakai store dan update).
     */
    private function messages(): array
    {
        return [
            'full_name.max' => 'Nama lengkap maksimal ' . User::FULL_NAME_MAX . ' karakter.',
            'username.max' => 'Username maksimal ' . User::USERNAME_MAX . ' karakter.',
            'username.unique' => 'Username sudah digunakan.',
            'nipp.required' => 'NIPP wajib diisi.',
            'nipp.digits_between' => 'NIPP harus berupa angka (maksimal ' . User::NIPP_MAX . ' digit).',
            'nipp.unique' => 'NIPP sudah terdaftar.',
            'role.in' => 'Role yang dipilih tidak diizinkan.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'province_id.required_if' => 'Wilayah kerja wajib dipilih untuk petugas.',
            'province_id.exists' => 'Provinsi yang dipilih tidak valid.',
        ];
    }

    public function index()
    {
        return view('admin.users.index');
    }

    public function create()
    {
        // Role yang bisa dibuat tergantung siapa yang login
        $availableRoles = Auth::user()?->role === 'superadmin'
            ? ['admin' => 'Admin', 'petugas' => 'Petugas']
            : ['petugas' => 'Petugas'];

        $provinces = Province::orderBy('name')->get();

        return view('admin.users.create', compact('availableRoles', 'provinces'));
    }

    public function store(Request $request)
    {
        $allowedRoles = $this->allowedRoles();

        $validated = $request->validate([
            'full_name'   => ['required', 'string', 'max:' . User::FULL_NAME_MAX],
            'username'    => ['required', 'string', 'max:' . User::USERNAME_MAX, 'unique:users,username'],
            'nipp'        => ['bail', 'required', 'string', 'digits_between:1,' . User::NIPP_MAX, 'unique:users,nipp'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
            'role'        => ['required', Rule::in($allowedRoles)],
            'province_id' => ['nullable', 'required_if:role,petugas', 'integer', 'exists:provinces,id'],
            'is_active'   => ['boolean'],
        ], $this->messages());

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active', true);

        // Admin tidak perlu batasan wilayah
        $validated['province_id'] = $validated['role'] === 'admin'
            ? null
            : ($validated['province_id'] ?? null);

        User::create($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        // Admin biasa tidak boleh mengubah akun Super Admin.
        if (
            $user->role === 'superadmin'
            && Auth::user()?->role !== 'superadmin'
        ) {
            abort(403, 'Hanya Super Admin yang dapat mengubah akun Super Admin.');
        }

        $availableRoles = Auth::user()?->role === 'superadmin'
            ? ['admin' => 'Admin', 'petugas' => 'Petugas']
            : ['petugas' => 'Petugas'];

        $provinces = Province::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'availableRoles', 'provinces'));
    }

    public function update(Request $request, User $user)
    {
        // Admin biasa tidak boleh mengubah akun Super Admin.
        if (
            $user->role === 'superadmin'
            && Auth::user()?->role !== 'superadmin'
        ) {
            abort(403, 'Hanya Super Admin yang dapat mengubah akun Super Admin.');
        }

        $allowedRoles = $this->allowedRoles();

        $validated = $request->validate([
            'full_name'   => ['required', 'string', 'max:' . User::FULL_NAME_MAX],
            'username'    => [
                'required',
                'string',
                'max:' . User::USERNAME_MAX,
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'nipp'        => [
                'bail',
                'required',
                'string',
                'digits_between:1,' . User::NIPP_MAX,
                Rule::unique('users', 'nipp')->ignore($user->id),
            ],
            'role'        => ['required', Rule::in($allowedRoles)],
            'province_id' => ['nullable', 'required_if:role,petugas', 'integer', 'exists:provinces,id'],
            'password'    => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active'   => ['boolean'],
        ], $this->messages());

        // Akun sendiri tidak boleh berganti role.
        if (
            $user->id === Auth::id()
            && $validated['role'] !== $user->role
        ) {
            return back()
                ->withErrors([
                    'role' => 'Role akun sendiri tidak dapat diubah.',
                ])
                ->withInput();
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active', $user->is_active);

        // Admin tidak perlu batasan wilayah
        $validated['province_id'] = $validated['role'] === 'admin'
            ? null
            : ($validated['province_id'] ?? null);

        $user->update($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Akun berhasil diperbarui.');
    }
}