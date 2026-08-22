<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public bool $open = false;

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function markAsRead(string $notificationId): void
    {
        $notification = Auth::user()->unreadNotifications()->find($notificationId);
        $notification?->markAsRead();
    }

    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        return view('livewire.admin.notification-bell', [
            'notifications' => Auth::user()->notifications()->latest()->limit(8)->get(),
            'unreadCount' => Auth::user()->unreadNotifications()->count(),
        ]);
    }
}