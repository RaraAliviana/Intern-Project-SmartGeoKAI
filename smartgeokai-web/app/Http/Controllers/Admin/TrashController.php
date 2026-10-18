<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TrashController extends Controller
{
    /**
     * Pastikan hanya Super Admin yang bisa mengakses.
     */
    private function authorizeSuperAdmin(): void
    {
        abort_unless(
            auth()->check()
                && auth()->user()->role === 'superadmin',
            403,
            'Tong Sampah hanya dapat diakses oleh Super Admin.'
        );
    }

    /**
     * Menampilkan aset yang sudah dihapus.
     */
    public function index(Request $request)
    {
        $this->authorizeSuperAdmin();

        $assets = Asset::onlyTrashed()
            ->with([
                'province',
                'regency',
                'district',
                'createdBy',
                'images',
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('id_asset', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('asset_type', 'like', "%{$search}%");
                });
            })
            ->latest('deleted_at')
            ->paginate(15)
            ->withQueryString();

        $totalTrashed = Asset::onlyTrashed()->count();

        return view('admin.trash.index', compact('assets', 'totalTrashed'));
    }

    /**
     * Memulihkan satu aset.
     */
    public function restore($id)
    {
        $this->authorizeSuperAdmin();

        $asset = Asset::onlyTrashed()->findOrFail($id);
        $assetCode = $asset->id_asset;

        $asset->restore();

        return redirect()
            ->route('admin.trash.index')
            ->with('success', "Aset {$assetCode} berhasil dipulihkan.");
    }

    /**
     * Memulihkan seluruh aset.
     */
    public function restoreAll()
    {
        $this->authorizeSuperAdmin();

        $count = Asset::onlyTrashed()->count();

        if ($count === 0) {
            return back()->with('error', 'Tidak ada aset untuk dipulihkan.');
        }

        Asset::onlyTrashed()->restore();

        return redirect()
            ->route('admin.trash.index')
            ->with('success', "{$count} aset berhasil dipulihkan.");
    }

    /**
     * Menghapus satu aset secara permanen.
     */
    public function forceDelete($id)
    {
        $this->authorizeSuperAdmin();

        $asset = Asset::onlyTrashed()
            ->with('images')
            ->findOrFail($id);

        $assetCode = $asset->id_asset;

        DB::transaction(function () use ($asset) {
            foreach ($asset->images as $image) {
                if ($image->file_path) {
                    Storage::disk('public')->delete($image->file_path);
                }

                $image->delete();
            }

            $asset->forceDelete();
        });

        return redirect()
            ->route('admin.trash.index')
            ->with('success', "Aset {$assetCode} berhasil dihapus permanen.");
    }
}
