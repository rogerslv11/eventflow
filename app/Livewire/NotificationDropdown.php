<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Notificações')]
class NotificationDropdown extends Component
{
    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);
        $this->dispatch('toast', type: 'success', title: 'Notificações', message: 'Marcadas como lidas.');
    }

    public function markAsRead(int $id): void
    {
        $notification = Auth::user()->notifications()->find($id);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function render()
    {
        $notifications = Auth::user()->notifications()->latest()->limit(8)->get();
        $unreadCount = Auth::user()->unreadNotifications()->count();

        return view('livewire.notification-dropdown', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}
