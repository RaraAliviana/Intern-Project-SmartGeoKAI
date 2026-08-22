<?php

namespace App\Notifications;

use App\Models\Asset;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssetReportedProblem extends Notification
{
    use Queueable;

    public function __construct(public Asset $asset) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'asset_problem',
            'title' => 'Aset dilaporkan bermasalah',
            'message' => "Aset {$this->asset->id_asset} dilaporkan bermasalah dan memerlukan tindak lanjut.",
            'asset_id' => $this->asset->id,
            'url' => route('admin.assets.show', $this->asset->id),
        ];
    }
}