<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Support\Search;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UserTable extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'role')]
    public string $roleFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function toggleStatus(User $user): void
    {
        if ($user->id === Auth::id()) {
            session()->flash('error', 'Tidak dapat mengubah status akun sendiri.');
            return;
        }

        // Admin tidak boleh menonaktifkan/mengaktifkan akun Super Admin.
        if (! $user->canBeManagedBy(Auth::user())) {
            session()->flash('error', 'Hanya Super Admin yang dapat mengubah status akun Super Admin.');
            return;
        }

        $user->update(['is_active' => ! $user->is_active]);
        session()->flash('success', $user->is_active ? 'Akun diaktifkan.' : 'Akun dinonaktifkan.');
    }

    public function deleteUser(User $user): void
    {
        if ($user->id === Auth::id()) {
            session()->flash('error', 'Tidak dapat menghapus akun sendiri.');
            return;
        }

        // Admin tidak boleh menghapus akun Super Admin.
        if (! $user->canBeManagedBy(Auth::user())) {
            session()->flash('error', 'Hanya Super Admin yang dapat menghapus akun Super Admin.');
            return;
        }

        $user->delete();
        session()->flash('success', 'Akun berhasil dihapus.');
    }

    public function render()
    {
        $search = trim($this->search);

        $users = User::query()
            ->with('province')
            ->when($search !== '', function ($query) use ($search) {
                $escaped = str_replace(['%', '_'], ['\%', '\_'], $search);

                $query->where(function ($q) use ($escaped, $search) {
                    $q->where('full_name', 'like', '%' . $escaped . '%')
                        ->orWhere('username', 'like', '%' . $escaped . '%');

                    // nipp bercharset ascii: hanya dicari bila kata kunci ASCII
                    if (Search::isAscii($search)) {
                        $q->orWhere('nipp', 'like', '%' . $escaped . '%');
                    }
                });
            })
            ->when($this->roleFilter !== '', function ($query) {
                $query->where('role', $this->roleFilter);
            })
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('livewire.admin.user-table', compact('users'));
    }
}