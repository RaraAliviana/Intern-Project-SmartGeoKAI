<?php

namespace App\Notifications;

use App\Models\StatusApproval;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ApprovalRequestSubmitted extends Notification
{
    use Queueable;

    public function __construct(public StatusApproval $approval) {}

    public function via(object $notifiable): array
    {
        // Cuma channel 'database' — TANPA 'broadcast', karena kita tidak pakai Reverb
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'approval_request',
            'title' => 'Pengajuan perubahan status',
            'message' => "{$this->approval->requestedBy->full_name} mengajukan perubahan status aset {$this->approval->asset->id_asset} dari {$this->approval->old_status} ke {$this->approval->new_status}.",
            'asset_id' => $this->approval->asset_id,
            'approval_id' => $this->approval->id,
            'url' => route('admin.approvals.index'),
        ];
    }
}