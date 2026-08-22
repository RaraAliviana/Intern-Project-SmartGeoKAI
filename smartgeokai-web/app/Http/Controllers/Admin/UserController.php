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
    // index() tidak lagi dipakai untuk ambil data (sudah dihandle Livewire UserTable),
    // tapi tetap dibutuhkan untuk menampilkan halaman index.blade.php
    public function index()
    {
        return view('admin.users.index');
    }

    public function create()
    {
        $provinces = Province::orderBy('name')->get();

        return view('admin.users.create', compact('provinces'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'   => ['required', 'string', 'max:100'],
            'username'    => ['required', 'string', 'max:50', 'unique:users,username'],
            'nip'         => ['required', 'string', 'max:20', 'unique:users,nip'],
            'password'    => ['required', 'string', 'min:8'],
            'role'        => ['required', Rule::in(['admin', 'petugas'])],
            'province_id' => ['nullable', 'exists:provinces,id'],
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'nip.unique'       => 'NIP sudah terdaftar.',
        ]);

        User::create([
            'full_name'   => $validated['full_name'],
            'username'    => $validated['username'],
            'nip'         => $validated['nip'],
            'password'    => Hash::make($validated['password']),
            'role'        => $validated['role'],
            // Admin selalu akses semua wilayah — province_id dipaksa null kalau role admin
            'province_id' => $validated['role'] === 'admin' ? null : ($validated['province_id'] ?? null),
            'is_active'   => true,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $provinces = Province::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'provinces'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'full_name'   => ['required', 'string', 'max:100'],
            'username'    => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'nip'         => ['required', 'string', 'max:20', Rule::unique('users', 'nip')->ignore($user->id)],
            'role'        => ['required', Rule::in(['admin', 'petugas'])],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'password'    => ['nullable', 'string', 'min:8'],
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'nip.unique'       => 'NIP sudah terdaftar.',
        ]);

        // Cegah admin mengubah role dirinya sendiri jadi petugas (biar tidak terkunci dari panel admin)
        if ($user->id === Auth::id() && $validated['role'] !== 'admin') {
            return back()->withErrors(['role' => 'Tidak dapat mengubah role akun sendiri.'])->withInput();
        }

        $user->full_name   = $validated['full_name'];
        $user->username    = $validated['username'];
        $user->nip          = $validated['nip'];
        $user->role         = $validated['role'];
        $user->province_id = $validated['role'] === 'admin' ? null : ($validated['province_id'] ?? null);

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }
}