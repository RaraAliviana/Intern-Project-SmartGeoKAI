<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    /**
     * GET /api/assets
     * Daftar aset — otomatis dibatasi ke province_id milik petugas yang login.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Asset::with(['province', 'regency', 'district'])
            ->where('province_id', $user->province_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_asset', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assets = $query->orderByDesc('created_at')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $assets,
        ]);
    }

    /**
     * POST /api/assets
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'id_asset' => ['required', 'string', 'max:50', 'unique:assets,id_asset'],
            'asset_type' => ['required', 'in:Tanah,Bangunan'],
            'classification' => ['required', 'in:ROW,Fasilitas,Operasional'],
            'asset_group' => ['required', 'in:Produksi,Non Produksi'],
            'regency_id' => ['required', 'exists:regencies,id'],
            'district_id' => ['required', 'exists:districts,id'],
            'area_m2' => ['required', 'numeric', 'min:0'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'gmaps_url' => ['nullable', 'url'],
            'status' => ['required', 'in:Clear,Proses,Masalah'],
            'description' => ['nullable', 'string'],
        ]);

        // Petugas TIDAK BOLEH pilih provinsi sendiri — dipaksa sesuai wilayah kerjanya
        $validated['province_id'] = $user->province_id;
        $validated['status_updated_at'] = now();
        $validated['created_by'] = $user->id;
        $validated['updated_by'] = $user->id;

        $asset = Asset::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Aset berhasil ditambahkan.',
            'data' => $asset->load(['province', 'regency', 'district']),
        ], 201);
    }

    /**
     * GET /api/assets/{asset}
     */
    public function show(Request $request, Asset $asset)
    {
        $this->authorizeAssetAccess($request, $asset);

        return response()->json([
            'success' => true,
            'data' => $asset->load(['province', 'regency', 'district', 'images']),
        ]);
    }

    /**
     * PUT/PATCH /api/assets/{asset}
     */
    public function update(Request $request, Asset $asset)
    {
        $this->authorizeAssetAccess($request, $asset);

        $validated = $request->validate([
            'asset_type' => ['sometimes', 'in:Tanah,Bangunan'],
            'classification' => ['sometimes', 'in:ROW,Fasilitas,Operasional'],
            'asset_group' => ['sometimes', 'in:Produksi,Non Produksi'],
            'regency_id' => ['sometimes', 'exists:regencies,id'],
            'district_id' => ['sometimes', 'exists:districts,id'],
            'area_m2' => ['sometimes', 'numeric', 'min:0'],
            'latitude' => ['sometimes', 'numeric', 'between:-90,90'],
            'longitude' => ['sometimes', 'numeric', 'between:-180,180'],
            'gmaps_url' => ['nullable', 'url'],
            'status' => ['sometimes', 'in:Clear,Proses,Masalah'],
            'description' => ['nullable', 'string'],
        ]);

        // Kalau status berubah, catat waktunya (dipakai fitur "belum ditangani X hari" di web)
        if (isset($validated['status']) && $validated['status'] !== $asset->status) {
            $validated['status_updated_at'] = now();
        }

        $validated['updated_by'] = $request->user()->id;

        $asset->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Aset berhasil diperbarui.',
            'data' => $asset->fresh()->load(['province', 'regency', 'district']),
        ]);
    }

    /**
     * DELETE /api/assets/{asset}
     */
    public function destroy(Request $request, Asset $asset)
    {
        $this->authorizeAssetAccess($request, $asset);

        $asset->delete();

        return response()->json([
            'success' => true,
            'message' => 'Aset berhasil dihapus.',
        ]);
    }

    /**
     * Pastikan petugas hanya bisa akses aset di provinsi kerjanya sendiri.
     * Ini pertahanan WAJIB — tanpa ini, petugas bisa akses/edit/hapus aset
     * milik provinsi lain hanya dengan menebak ID di URL.
     */
    private function authorizeAssetAccess(Request $request, Asset $asset): void
    {
        if ($asset->province_id !== $request->user()->province_id) {
            abort(403, 'Kamu tidak memiliki akses ke aset di luar wilayah kerjamu.');
        }
    }
}
