<?php

namespace App\Observers;

use App\Models\StatusApproval;
use App\Models\User;
use App\Notifications\ApprovalRequestSubmitted;
use App\Notifications\AssetReportedProblem;
use Illuminate\Support\Facades\Notification;

class StatusApprovalObserver
{
    public function created(StatusApproval $approval): void
    {
        $admins = User::admins()->get();

        // Notifikasi 1: setiap ada pengajuan perubahan status, selalu dikirim
        Notification::send($admins, new ApprovalRequestSubmitted($approval));

        // Notifikasi 2: khusus kalau status baru yang diajukan = "Masalah"
        if ($approval->new_status === 'Masalah') {
            Notification::send($admins, new AssetReportedProblem($approval->asset));
        }
    }
}