<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * GET /api/assets
     *
     * Daftar aset.
     * Petugas hanya dapat melihat aset yang berada
     * di provinsi sesuai wilayah kerjanya.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Asset::with([
                'province',
                'regency',
                'district',
            ])
            ->where('province_id', $user->province_id);

        // Pencarian berdasarkan ID Asset atau keterangan
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('id_asset', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assets = $query
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $assets,
        ]);
    }


    /**
     * POST /api/assets
     *
     * Menambahkan aset baru dari aplikasi mobile Petugas.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'id_asset' => [
                'required',
                'string',
                'max:20',
                'unique:assets,id_asset',
            ],

            'asset_type' => [
                'required',
                'in:Tanah,Bangunan',
            ],

            'classification' => [
                'required',
                'in:ROW,Fasilitas,Operasional',
            ],

            'asset_group' => [
                'required',
                'in:Produksi,Non Produksi',
            ],

            'regency_id' => [
                'required',
                'exists:regencies,id',
            ],

            'district_id' => [
                'required',
                'exists:districts,id',
            ],

            'area_m2' => [
                'required',
                'numeric',
                'min:0',
            ],

            'latitude' => [
                'required',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'required',
                'numeric',
                'between:-180,180',
            ],

            'gmaps_url' => [
                'nullable',
                'url',
                'max:200',
            ],

            'status' => [
                'required',
                'in:Clear,Proses,Masalah',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Data otomatis dari user yang login
        |--------------------------------------------------------------------------
        */

        // Petugas tidak boleh menentukan provinsi sendiri.
        // Provinsi mengikuti wilayah kerja petugas.
        $validated['province_id'] = $user->province_id;

        // Karena aset baru dibuat dengan status tertentu,
        // waktu status pertama kali ditetapkan dicatat sekarang.
        $validated['status_updated_at'] = now();

        // User yang membuat aset
        $validated['created_by'] = $user->id;

        // User terakhir yang mengubah aset
        $validated['updated_by'] = $user->id;

        /*
        |--------------------------------------------------------------------------
        | Simpan aset
        |--------------------------------------------------------------------------
        */

        $asset = Asset::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Aset berhasil ditambahkan.',
            'data' => $asset->load([
                'province',
                'regency',
                'district',
            ]),
        ], 201);
    }


    /**
     * GET /api/assets/{asset}
     *
     * Detail aset.
     */
    public function show(Request $request, Asset $asset)
    {
        // Pastikan petugas hanya dapat melihat aset
        // di wilayah kerjanya.
        $this->authorizeAssetAccess($request, $asset);

        return response()->json([
            'success' => true,
            'data' => $asset->load([
                'province',
                'regency',
                'district',
                'images',
            ]),
        ]);
    }


    /**
     * PUT/PATCH /api/assets/{asset}
     *
     * Memperbarui data aset dari aplikasi mobile.
     */
    public function update(Request $request, Asset $asset)
    {
        // Pastikan petugas hanya dapat mengubah aset
        // di wilayah kerjanya.
        $this->authorizeAssetAccess($request, $asset);

        $validated = $request->validate([
            'asset_type' => [
                'sometimes',
                'in:Tanah,Bangunan',
            ],

            'classification' => [
                'sometimes',
                'in:ROW,Fasilitas,Operasional',
            ],

            'asset_group' => [
                'sometimes',
                'in:Produksi,Non Produksi',
            ],

            'regency_id' => [
                'sometimes',
                'exists:regencies,id',
            ],

            'district_id' => [
                'sometimes',
                'exists:districts,id',
            ],

            'area_m2' => [
                'sometimes',
                'numeric',
                'min:0',
            ],

            'latitude' => [
                'sometimes',
                'numeric',
                'between:-90,90',
            ],

            'longitude' => [
                'sometimes',
                'numeric',
                'between:-180,180',
            ],

            'gmaps_url' => [
                'nullable',
                'url',
                'max:200',
            ],

            'status' => [
                'sometimes',
                'in:Clear,Proses,Masalah',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update status_updated_at
        |--------------------------------------------------------------------------
        |
        | Kalau status berubah:
        |
        | Masalah -> Proses
        | Proses -> Clear
        | Clear -> Masalah
        |
        | maka waktu perubahan status dicatat ulang.
        |
        */

        if (
            isset($validated['status']) &&
            $validated['status'] !== $asset->status
        ) {
            $validated['status_updated_at'] = now();
        }

        /*
        |--------------------------------------------------------------------------
        | User yang melakukan update
        |--------------------------------------------------------------------------
        */

        $validated['updated_by'] = $request->user()->id;

        /*
        |--------------------------------------------------------------------------
        | Simpan perubahan
        |--------------------------------------------------------------------------
        */

        $asset->update($validated);

        /*
        |--------------------------------------------------------------------------
        | Return data terbaru
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'message' => 'Aset berhasil diperbarui.',
            'data' => $asset
                ->fresh()
                ->load([
                    'province',
                    'regency',
                    'district',
                    'images',
                ]),
        ]);
    }


    /**
     * DELETE /api/assets/{asset}
     *
     * Menghapus aset.
     */
    public function destroy(Request $request, Asset $asset)
    {
        // Pastikan petugas hanya dapat menghapus aset
        // di wilayah kerjanya.
        $this->authorizeAssetAccess($request, $asset);

        $asset->delete();

        return response()->json([
            'success' => true,
            'message' => 'Aset berhasil dihapus.',
        ]);
    }


    /**
     * Memastikan petugas hanya dapat mengakses
     * aset yang berada di provinsi wilayah kerjanya.
     */
    private function authorizeAssetAccess(
        Request $request,
        Asset $asset
    ): void {
        if (
            $asset->province_id !==
            $request->user()->province_id
        ) {
            abort(
                403,
                'Kamu tidak memiliki akses ke aset di luar wilayah kerjamu.'
            );
        }
    }
}