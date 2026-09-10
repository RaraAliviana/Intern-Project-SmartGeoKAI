<?php

namespace App\Observers;

use App\Models\Asset;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class AssetObserver
{
    /**
     * Catat log saat Aset baru dibuat.
     */
    public function created(Asset $asset): void
    {
        AuditLog::create([
            'user_id'    => Auth::id(),
            'asset_id'   => $asset->id,
            'action'     => 'create',
            'field_name' => 'ALL_FIELDS',
            'new_value'  => "Asset {$asset->id_asset} berhasil dibuat.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Catat log perubahan per-field saat Aset diupdate.
     */
    public function updated(Asset $asset): void
    {
        $dirty = $asset->getDirty();

        foreach ($dirty as $field => $newValue) {
            // Abaikan timestamps
            if (in_array($field, ['updated_at', 'created_at'])) {
                continue;
            }

            $oldValue = $asset->getOriginal($field);

            AuditLog::create([
                'user_id'    => Auth::id(),
                'asset_id'   => $asset->id,
                'action'     => 'update',
                'field_name' => $field,
                'old_value'  => is_array($oldValue) ? json_encode($oldValue) : (string) $oldValue,
                'new_value'  => is_array($newValue) ? json_encode($newValue) : (string) $newValue,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }

    /**
     * Catat log saat Aset dihapus.
     */
    public function deleted(Asset $asset): void
    {
        AuditLog::create([
            'user_id'    => Auth::id(),
            'asset_id'   => $asset->id,
            'action'     => 'delete',
            'field_name' => 'ALL_FIELDS',
            'old_value'  => "Asset {$asset->id_asset} dihapus.",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}