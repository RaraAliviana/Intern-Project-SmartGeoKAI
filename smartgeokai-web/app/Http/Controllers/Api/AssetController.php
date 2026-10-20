<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AssetController extends Controller
{
    /**
     * GET /api/assets
     *
     * Daftar aset sesuai provinsi wilayah kerja petugas.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Asset::with([
                'province',
                'regency',
                'district',
                'images',
            ])
            ->where('province_id', $user->province_id);

        // Pencarian berdasarkan ID aset atau keterangan.
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('id_asset', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan status.
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Batas per halaman: minimal 1 dan maksimal 500.
        $perPage = min(
            max((int) $request->input('per_page', 15), 1),
            500
        );

        $assets = $query
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $assets,
        ]);
    }

    /**
     * POST /api/assets
     *
     * Menambahkan aset baru beserta minimal satu foto.
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
                Rule::in(['Tanah', 'Bangunan']),
            ],

            'classification' => [
                'required',
                Rule::in(['ROW', 'Fasilitas', 'Operasional']),
            ],

            'asset_group' => [
                'required',
                Rule::in(['Produksi', 'Non Produksi']),
            ],

            'regency_id' => [
                'required',
                Rule::exists('regencies', 'id')
                    ->where('province_id', $user->province_id),
            ],

            'district_id' => [
                'required',
                Rule::exists('districts', 'id')
                    ->where('regency_id', $request->input('regency_id')),
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
                Rule::in(['Clear', 'Proses', 'Masalah']),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            // Minimal satu foto dan maksimal sepuluh foto.
            'photos' => [
                'required',
                'array',
                'min:1',
                'max:10',
            ],

            'photos.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Data otomatis dari user yang login
        |--------------------------------------------------------------------------
        */

        // Provinsi mengikuti wilayah kerja petugas.
        $validated['province_id'] = $user->province_id;

        // Waktu status pertama kali ditetapkan.
        $validated['status_updated_at'] = now();

        // User pembuat dan pengubah aset.
        $validated['created_by'] = $user->id;
        $validated['updated_by'] = $user->id;

        /*
        |--------------------------------------------------------------------------
        | Simpan aset dan foto
        |--------------------------------------------------------------------------
        */

        // Foto disimpan secara terpisah, bukan di tabel assets.
        unset($validated['photos']);

        $asset = Asset::create($validated);

        $this->storePhotos($request, $asset);

        return response()->json([
            'success' => true,
            'message' => 'Aset berhasil ditambahkan.',
            'data' => $asset->load([
                'province',
                'regency',
                'district',
                'images',
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
     * Memperbarui aset dan mengelola foto.
     */
    public function update(Request $request, Asset $asset)
    {
        $this->authorizeAssetAccess($request, $asset);

        $validated = $request->validate([
            'asset_type' => [
                'sometimes',
                Rule::in(['Tanah', 'Bangunan']),
            ],

            'classification' => [
                'sometimes',
                Rule::in(['ROW', 'Fasilitas', 'Operasional']),
            ],

            'asset_group' => [
                'sometimes',
                Rule::in(['Produksi', 'Non Produksi']),
            ],

            'regency_id' => [
                'sometimes',
                Rule::exists('regencies', 'id')
                    ->where('province_id', $request->user()->province_id),
            ],

            'district_id' => [
                'sometimes',
                Rule::exists('districts', 'id')
                    ->where(
                        'regency_id',
                        $request->input(
                            'regency_id',
                            $asset->regency_id
                        )
                    ),
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
                Rule::in(['Clear', 'Proses', 'Masalah']),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            // ID foto lama yang ingin dihapus.
            'delete_images' => [
                'nullable',
                'array',
            ],

            'delete_images.*' => [
                'required',
                'integer',
                'exists:asset_images,id',
            ],

            // Foto baru bersifat opsional saat edit.
            'photos' => [
                'nullable',
                'array',
                'max:10',
            ],

            'photos.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update status_updated_at
        |--------------------------------------------------------------------------
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
        | Foto: hapus yang dipilih, lalu simpan foto baru
        |--------------------------------------------------------------------------
        */

        $deleteIds = $validated['delete_images'] ?? [];
        $newCount = count($request->file('photos', []));

        // Hitung foto yang tersisa setelah foto terpilih dihapus.
        $remaining = $asset->images()
            ->whereNotIn('id', $deleteIds)
            ->count();

        if ($remaining + $newCount > 10) {
            throw ValidationException::withMessages([
                'photos' => 'Maksimal 10 foto per aset.',
            ]);
        }

        foreach ($deleteIds as $imageId) {
            // Hanya menghapus foto milik aset yang sedang diedit.
            $image = $asset->images()->find($imageId);

            if ($image) {
                Storage::disk('public')->delete($image->file_path);
                $image->delete();
            }
        }

        // Simpan foto baru jika ada.
        $this->storePhotos($request, $asset);

        // Field foto tidak boleh dikirim ke tabel assets.
        unset(
            $validated['delete_images'],
            $validated['photos']
        );

        /*
        |--------------------------------------------------------------------------
        | Simpan perubahan aset
        |--------------------------------------------------------------------------
        */

        $asset->update($validated);

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
        $this->authorizeAssetAccess($request, $asset);

        $asset->delete();

        return response()->json([
            'success' => true,
            'message' => 'Aset berhasil dihapus.',
        ]);
    }

    /**
     * Menyimpan foto ke storage publik dan tabel asset_images.
     */
    private function storePhotos(Request $request, Asset $asset): void
    {
        foreach ($request->file('photos', []) as $photo) {
            $path = $photo->store('assets/images', 'public');

            $asset->images()->create([
                'file_path' => $path,
                'file_name' => $photo->getClientOriginalName(),
                'file_size' => $photo->getSize(),
                'uploaded_by' => $request->user()->id,
            ]);
        }
    }

    /**
     * Memastikan petugas hanya dapat mengakses aset
     * di provinsi wilayah kerjanya.
     */
    private function authorizeAssetAccess(
        Request $request,
        Asset $asset
    ): void {
        if (
            (string) $asset->province_id !==
            (string) $request->user()->province_id
        ) {
            abort(
                403,
                'Kamu tidak memiliki akses ke aset di luar wilayah kerjamu.'
            );
        }
    }
}
