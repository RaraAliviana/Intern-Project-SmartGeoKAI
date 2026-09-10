<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\Asset;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        // 1. Query Rekam Jejak Audit Log dengan Filter
        $logsQuery = AuditLog::with(['user', 'asset'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('user_id')) {
            $logsQuery->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $logsQuery->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $logsQuery->where(function ($q) use ($search) {
                $q->whereHas('asset', function ($a) use ($search) {
                    $a->where('id_asset', 'LIKE', "%{$search}%");
                })->orWhereHas('user', function ($u) use ($search) {
                    $u->where('full_name', 'LIKE', "%{$search}%");
                })->orWhere('field_name', 'LIKE', "%{$search}%");
            });
        }

        $logs = $logsQuery->paginate(15)->withQueryString();

        // 2. Query Aktivitas Petugas (Petugas + Aset Terakhir yang Ditangani)
        $officers = User::where('role', 'petugas')
            ->where('is_active', true)
            ->get()
            ->map(function ($officer) {
                // Cari aset terakhir yang dibuat/diupdate oleh petugas ini
                $lastCreatedAsset = Asset::where('created_by', $officer->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $lastAudit = AuditLog::where('user_id', $officer->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                $officer->last_asset = $lastCreatedAsset;
                $officer->last_activity_at = $lastAudit?->created_at ?? $lastCreatedAsset?->updated_at ?? $officer->updated_at;
                return $officer;
            });

        $usersList = User::orderBy('full_name')->get();

        return view('admin.log-audit.index', compact('logs', 'officers', 'usersList'));
    }
}