<?php

namespace App\Observers;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\User;
use App\Notifications\AssetReportedProblem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class AssetObserver
{
    /**
     * Saat aset baru dibuat.
     */
    public function created(Asset $asset): void
    {
        $userId = Auth::id() ?? $asset->created_by;

        AuditLog::create([
            'user_id'    => $userId,
            'asset_id'   => $asset->id,
            'action'     => 'create',
            'field_name' => null,
            'old_value'  => null,
            'new_value'  => $this->toJson($this->getRelevantFields($asset)),
            'ip_address' => request()?->ip(),
            'user_agent' => $this->userAgent(),
            'created_at' => now(),
        ]);

        if ($asset->status === 'Masalah') {
            $this->notifyAdmins($asset);
        }
    }

    /**
     * Saat aset berhasil diperbarui.
     *
     * Pada event "updated", Eloquent belum menyinkronkan nilai asli, jadi
     * getOriginal() masih berisi nilai LAMA dan getChanges() berisi field yang
     * berubah. Nilai lama TIDAK boleh disimpan di properti observer, karena
     * Laravel membuat instance observer baru untuk setiap event.
     */
    public function updated(Asset $asset): void
    {
        $changes = $asset->getChanges();

        $skipFields = [
            'updated_at',
            'updated_by',
            'status_updated_at',
        ];

        $userId = Auth::id()
            ?? $asset->updated_by
            ?? $asset->created_by;

        foreach (array_keys($changes) as $field) {
            if (in_array($field, $skipFields, true)) {
                continue;
            }

            // Keduanya diambil lewat cast model, supaya format angka desimal seragam
            $oldValue = $this->stringify($asset->getOriginal($field));
            $newValue = $this->stringify($asset->getAttribute($field));

            if ($oldValue === $newValue) {
                continue;
            }

            AuditLog::create([
                'user_id'    => $userId,
                'asset_id'   => $asset->id,
                'action'     => 'update',
                'field_name' => $field,
                'old_value'  => $oldValue,
                'new_value'  => $newValue,
                'ip_address' => request()?->ip(),
                'user_agent' => $this->userAgent(),
                'created_at' => now(),
            ]);
        }

        // Pertahankan notifikasi ketika status berubah menjadi Masalah.
        if (
            array_key_exists('status', $changes)
            && $asset->status === 'Masalah'
        ) {
            $this->notifyAdmins($asset);
        }
    }

    /**
     * Saat aset dihapus, termasuk soft delete.
     */
    public function deleted(Asset $asset): void
    {
        AuditLog::create([
            'user_id'    => Auth::id()
                ?? $asset->updated_by
                ?? $asset->created_by,
            'asset_id'   => $asset->id,
            'action'     => 'delete',
            'field_name' => null,
            'old_value'  => $this->toJson($this->getRelevantFields($asset)),
            'new_value'  => null,
            'ip_address' => request()?->ip(),
            'user_agent' => $this->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Saat aset dipulihkan dari soft delete.
     */
    public function restored(Asset $asset): void
    {
        AuditLog::create([
            'user_id'    => Auth::id()
                ?? $asset->updated_by
                ?? $asset->created_by,
            'asset_id'   => $asset->id,
            'action'     => 'update',
            'field_name' => 'restored',
            'old_value'  => null,
            'new_value'  => 'Aset dipulihkan',
            'ip_address' => request()?->ip(),
            'user_agent' => $this->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Ambil informasi aset yang relevan.
     */
    private function getRelevantFields(Asset $asset): array
    {
        return [
            'id_asset'       => $asset->id_asset,
            'asset_type'     => $asset->asset_type,
            'classification' => $asset->classification,
            'asset_group'    => $asset->asset_group,
            'province_id'    => $asset->province_id,
            'regency_id'     => $asset->regency_id,
            'district_id'    => $asset->district_id,
            'status'         => $asset->status,
            'area_m2'        => $asset->area_m2,
            'latitude'       => $asset->latitude,
            'longitude'      => $asset->longitude,
            'description'    => $asset->description,
        ];
    }

    /**
     * Ubah nilai atribut menjadi teks untuk disimpan di log (null tetap null).
     */
    private function stringify(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return (string) $value;
    }

    /**
     * Kolom audit_logs.user_agent hanya 255 karakter.
     */
    private function userAgent(): ?string
    {
        $agent = request()?->userAgent();

        return $agent === null ? null : Str::limit($agent, 255, '');
    }

    private function toJson(array $data): string
    {
        return json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Kirim notifikasi kepada admin.
     */
    private function notifyAdmins(Asset $asset): void
    {
        $admins = User::admins()->get();

        if ($admins->isNotEmpty()) {
            Notification::send(
                $admins,
                new AssetReportedProblem($asset)
            );
        }
    }
}