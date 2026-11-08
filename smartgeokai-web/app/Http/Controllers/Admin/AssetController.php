<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Support\Search;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AssetController extends Controller
{
    /**
     * Pesan validasi (dipakai store dan update).
     */
    private function messages(): array
    {
        return [
            'id_asset.max'      => 'ID aset maksimal 11 karakter.',
            'id_asset.regex'    => 'Format ID aset harus NN.NN.NNNNN, contoh: 06.01.00001.',
            'id_asset.unique'   => 'ID aset sudah digunakan.',
            'area_m2.max'       => 'Luas maksimal 999.999.999,99 m².',
            'gmaps_url.max'     => 'Link Google Maps maksimal 255 karakter.',
            'description.max'   => 'Deskripsi maksimal 5.000 karakter.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | PETA ASET
    |--------------------------------------------------------------------------
    */

    public function map(Request $request)
    {
        $provinces = Province::orderBy('name')->get();
        $regencies = collect();
        $districts = collect();

        if ($request->filled('province_id')) {
            $regencies = Regency::where('province_id', $request->province_id)->orderBy('name')->get();
        }

        if ($request->filled('regency_id')) {
            $districts = District::where('regency_id', $request->regency_id)->orderBy('name')->get();
        }

        $assets = Asset::with(['province', 'regency', 'district', 'images'])
            ->when($request->filled('province_id'), function ($query) use ($request) {
                $query->where('province_id', $request->province_id);
            })
            ->when($request->filled('regency_id'), function ($query) use ($request) {
                $query->where('regency_id', $request->regency_id);
            })
            ->when($request->filled('district_id'), function ($query) use ($request) {
                $query->where('district_id', $request->district_id);
            })
            ->when($request->filled('asset_type'), function ($query) use ($request) {
                $query->where('asset_type', $request->asset_type);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->get();

        return view('admin.assets.map', compact('assets', 'provinces', 'regencies', 'districts'));
    }

    /*
    |--------------------------------------------------------------------------
    | DAFTAR ASET
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $provinces = Province::orderBy('name')->get();
        $regencies = collect();
        $districts = collect();

        if ($request->filled('province_id')) {
            $regencies = Regency::where('province_id', $request->province_id)->orderBy('name')->get();
        }

        if ($request->filled('regency_id')) {
            $districts = District::where('regency_id', $request->regency_id)->orderBy('name')->get();
        }

        $assets = Asset::with(['province', 'regency', 'district', 'createdBy'])
            ->when($request->filled('province_id'), function ($query) use ($request) {
                $query->where('province_id', $request->province_id);
            })
            ->when($request->filled('regency_id'), function ($query) use ($request) {
                $query->where('regency_id', $request->regency_id);
            })
            ->when($request->filled('district_id'), function ($query) use ($request) {
                $query->where('district_id', $request->district_id);
            })
            ->when($request->filled('asset_type'), function ($query) use ($request) {
                $query->where('asset_type', $request->asset_type);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', '%' . $search . '%');

                    // id_asset bercharset ascii: hanya dicari bila kata kunci ASCII
                    if (Search::isAscii($search)) {
                        $q->orWhere('id_asset', 'like', '%' . $search . '%');
                    }
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.assets.index', compact('assets', 'provinces', 'regencies', 'districts'));
    }

    /*
    |--------------------------------------------------------------------------
    | DETAIL ASET
    |--------------------------------------------------------------------------
    */

    public function show(Asset $asset)
    {
        $asset->load(['province', 'regency', 'district', 'createdBy', 'updatedBy', 'images']);
        return view('admin.assets.show', compact('asset'));
    }

    /*
    |--------------------------------------------------------------------------
    | TAMBAH ASET
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $provinces = Province::orderBy('name')->get();
        $regencies = collect();
        $districts = collect();
        return view('admin.assets.create', compact('provinces', 'regencies', 'districts'));
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN ASET
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_asset'       => ['bail', 'required', 'string', 'max:11', 'regex:' . Asset::ID_ASSET_REGEX, 'unique:assets,id_asset'],
            'province_id'    => ['required', 'integer', 'exists:provinces,id'],
            'regency_id'     => ['required', 'integer', 'exists:regencies,id'],
            'district_id'    => ['required', 'integer', 'exists:districts,id'],
            'asset_type'     => ['required', 'in:Tanah,Bangunan'],
            'classification' => ['required', 'in:ROW,Fasilitas,Operasional'],
            'asset_group'    => ['required', 'in:Produksi,Non Produksi'],
            'status'         => ['required', 'in:Clear,Proses,Masalah'],
            'area_m2'        => ['required', 'numeric', 'min:0', 'max:' . Asset::AREA_MAX],
            'latitude'       => ['required', 'numeric', 'between:-90,90'],
            'longitude'      => ['required', 'numeric', 'between:-180,180'],
            'gmaps_url'      => ['nullable', 'string', 'max:255'],
            'description'    => ['nullable', 'string', 'max:5000'],
            'photos'         => ['required', 'array', 'min:1'],
            'photos.*'       => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], $this->messages());

        // Database menyimpan koordinat 6 desimal (presisi sekitar 11 cm)
        $validated['latitude']  = round((float) $validated['latitude'], 6);
        $validated['longitude'] = round((float) $validated['longitude'], 6);

        $validated['created_by'] = auth()->id();
        $validated['status_updated_at'] = now();

        unset($validated['photos']);

        $asset = Asset::create($validated);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('assets/images', 'public');
                $asset->images()->create([
                    'file_path'   => $path,
                    'file_name'   => Str::limit($photo->getClientOriginalName(), 255, ''),
                    'file_size'   => $photo->getSize(),
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('admin.assets.show', $asset)->with('success', 'Aset dan foto berhasil ditambahkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT & UPDATE
    |--------------------------------------------------------------------------
    */

    public function edit(Asset $asset)
    {
        $asset->load('images');
        $provinces = Province::orderBy('name')->get();
        $regencies = Regency::where('province_id', $asset->province_id)->orderBy('name')->get();
        $districts = District::where('regency_id', $asset->regency_id)->orderBy('name')->get();
        return view('admin.assets.edit', compact('asset', 'provinces', 'regencies', 'districts'));
    }

    public function update(Request $request, Asset $asset)
    {
        // Aset lama berformat 10 karakter (34.01.0102) boleh tetap dipakai selama
        // ID-nya tidak diubah; kalau diubah, harus mengikuti format baru NN.NN.NNNNN.
        $idRules = ['bail', 'required', 'string', 'max:11'];
        if ($request->input('id_asset') !== $asset->id_asset) {
            $idRules[] = 'regex:' . Asset::ID_ASSET_REGEX;
        }
        $idRules[] = 'unique:assets,id_asset,' . $asset->id;

        $validated = $request->validate([
            'id_asset'       => $idRules,
            'province_id'    => ['required', 'integer', 'exists:provinces,id'],
            'regency_id'     => ['required', 'integer', 'exists:regencies,id'],
            'district_id'    => ['required', 'integer', 'exists:districts,id'],
            'asset_type'     => ['required', 'in:Tanah,Bangunan'],
            'classification' => ['required', 'in:ROW,Fasilitas,Operasional'],
            'asset_group'    => ['required', 'in:Produksi,Non Produksi'],
            'status'         => ['required', 'in:Clear,Proses,Masalah'],
            'area_m2'        => ['required', 'numeric', 'min:0', 'max:' . Asset::AREA_MAX],
            'latitude'       => ['required', 'numeric', 'between:-90,90'],
            'longitude'      => ['required', 'numeric', 'between:-180,180'],
            'gmaps_url'      => ['nullable', 'string', 'max:255'],
            'description'    => ['nullable', 'string', 'max:5000'],
            'delete_images'   => ['nullable', 'array'],
            'delete_images.*' => ['exists:asset_images,id'],
            'photos'         => ['nullable', 'array'],
            'photos.*'       => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], $this->messages());

        // Database menyimpan koordinat 6 desimal (presisi sekitar 11 cm)
        $validated['latitude']  = round((float) $validated['latitude'], 6);
        $validated['longitude'] = round((float) $validated['longitude'], 6);

        $validated['updated_by'] = auth()->id();

        if ($asset->status !== $validated['status']) {
            $validated['status_updated_at'] = now();
        }

        // Hapus foto yang dicentang
        if ($request->filled('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $image = $asset->images()->find($imageId);
                if ($image) {
                    Storage::disk('public')->delete($image->file_path);
                    $image->delete();
                }
            }
        }

        // Upload foto baru
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('assets/images', 'public');
                $asset->images()->create([
                    'file_path'   => $path,
                    'file_name'   => Str::limit($photo->getClientOriginalName(), 255, ''),
                    'file_size'   => $photo->getSize(),
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        unset($validated['delete_images'], $validated['photos']);
        $asset->update($validated);

        return redirect()->route('admin.assets.show', $asset)->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroy(Asset $asset)
    {
        // Soft delete — foto tidak dihapus dulu dari storage agar bisa di-restore di trash
        $asset->delete();

        return redirect()
            ->route('admin.assets.index')
            ->with('success', 'Aset dipindahkan ke Arsip Terhapus. Anda dapat memulihkannya dari menu Arsip Terhapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | API DYNAMIC DROPDOWN
    |--------------------------------------------------------------------------
    */

    public function getRegencies(Request $request)
    {
        $request->validate(['province_id' => ['required', 'exists:provinces,id']]);
        return response()->json(Regency::where('province_id', $request->province_id)->orderBy('name')->get(['id', 'name']));
    }

    public function getDistricts(Request $request)
    {
        $request->validate(['regency_id' => ['required', 'exists:regencies,id']]);
        return response()->json(District::where('regency_id', $request->regency_id)->orderBy('name')->get(['id', 'name']));
    }
}