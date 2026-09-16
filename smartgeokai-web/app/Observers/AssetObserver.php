<?php

namespace App\Observers;

use App\Models\Asset;
use App\Models\User;
use App\Notifications\AssetReportedProblem;
use Illuminate\Support\Facades\Notification;

class AssetObserver
{
    /**
     * Terpicu setiap kali ada aset baru yang langsung dibuat dengan status Masalah.
     */
    public function created(Asset $asset): void
    {
        if ($asset->status === 'Masalah') {
            $this->notifyAdmins($asset);
        }
    }

    /**
     * Terpicu setiap kali status aset berubah MENJADI Masalah
     * (baik diedit dari web oleh admin, maupun dari mobile oleh petugas).
     */
    public function updated(Asset $asset): void
    {
        if ($asset->isDirty('status') && $asset->status === 'Masalah') {
            $this->notifyAdmins($asset);
        }
    }

    private function notifyAdmins(Asset $asset): void
    {
        $admins = User::admins()->get();

        Notification::send($admins, new AssetReportedProblem($asset));
    }
}