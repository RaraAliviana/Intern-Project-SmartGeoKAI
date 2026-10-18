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
            ? ['superadmin', 'admin', 'petugas']
            : ['admin', 'petugas'];
    }

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
            'full_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'nip' => ['required', 'string', 'max:20', 'unique:users,nip'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in($this->allowedRoles())],
            'province_id' => ['nullable', 'required_if:role,petugas', 'exists:provinces,id'],
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'role.in' => 'Role yang dipilih tidak diizinkan.',
            'province_id.required_if' => 'Pilih provinsi untuk petugas.',
        ]);

        $isFieldOfficer = $validated['role'] === 'petugas';

        User::create([
            'full_name' => $validated['full_name'],
            'username' => $validated['username'],
            'nip' => $validated['nip'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'province_id' => $isFieldOfficer
                ? ($validated['province_id'] ?? null)
                : null,
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $provinces = Province::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'provinces'));
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

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100'],
            'username' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'nip' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'nip')->ignore($user->id),
            ],
            'role' => ['required', Rule::in($this->allowedRoles())],
            'province_id' => [
                'nullable',
                'required_if:role,petugas',
                'exists:provinces,id',
            ],
            'password' => ['nullable', 'string', 'min:8'],
        ], [
            'username.unique' => 'Username sudah digunakan.',
            'nip.unique' => 'NIP sudah terdaftar.',
            'role.in' => 'Role yang dipilih tidak diizinkan.',
            'province_id.required_if' => 'Pilih provinsi untuk petugas.',
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

        $user->full_name = $validated['full_name'];
        $user->username = $validated['username'];
        $user->nip = $validated['nip'];
        $user->role = $validated['role'];

        $user->province_id = $validated['role'] === 'petugas'
            ? ($validated['province_id'] ?? null)
            : null;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }
}
