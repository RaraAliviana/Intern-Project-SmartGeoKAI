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
            'full_name'   => ['required', 'string', 'max:100'],
            'username'    => ['required', 'string', 'max:50', 'unique:users,username'],
            'nip'         => ['required', 'string', 'max:20', 'unique:users,nip'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
            'role'        => ['required', Rule::in($allowedRoles)],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'is_active'   => ['boolean'],
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'role.in' => 'Role yang dipilih tidak diizinkan.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'province_id.exists' => 'Provinsi yang dipilih tidak valid.',
        ]);

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
            'full_name'   => ['required', 'string', 'max:100'],
            'username'    => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'nip'         => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'nip')->ignore($user->id),
            ],
            'role'        => ['required', Rule::in($allowedRoles)],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'password'    => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active'   => ['boolean'],
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'role.in' => 'Role yang dipilih tidak diizinkan.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'province_id.exists' => 'Provinsi yang dipilih tidak valid.',
        ]);

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