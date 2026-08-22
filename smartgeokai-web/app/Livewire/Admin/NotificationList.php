<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationList extends Component
{
    use WithPagination;

    #[Url(as: 'status')]
    public string $filter = ''; // '', 'unread', 'read'

    public function updatingFilter(): void
    {
        $this->resetPage();
    }

    public function markAsRead(string $notificationId): void
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        $notification?->markAsRead();
    }

    public function markAsUnread(string $notificationId): void
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        $notification?->markAsUnread();
    }

    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function deleteNotification(string $notificationId): void
    {
        Auth::user()->notifications()->find($notificationId)?->delete();
    }

    public function render()
    {
        $query = Auth::user()->notifications();

        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->filter === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->latest()->paginate(15);

        $unreadCount = Auth::user()->unreadNotifications()->count();

        return view('livewire.admin.notification-list', compact('notifications', 'unreadCount'));
    }
}