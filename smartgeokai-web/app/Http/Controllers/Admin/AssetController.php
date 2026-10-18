<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
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
                    $q->where('id_asset', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
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
            'id_asset'       => ['required', 'string', 'max:20', 'unique:assets,id_asset'],
            'province_id'    => ['required', 'exists:provinces,id'],
            'regency_id'     => ['required', 'exists:regencies,id'],
            'district_id'    => ['required', 'exists:districts,id'],
            'asset_type'     => ['required', 'in:Tanah,Bangunan'],
            'classification' => ['required', 'in:ROW,Fasilitas,Operasional'],
            'asset_group'    => ['required', 'in:Produksi,Non Produksi'],
            'status'         => ['required', 'in:Clear,Proses,Masalah'],
            'area_m2'        => ['required', 'numeric', 'min:0'],
            'latitude'       => ['required', 'numeric', 'between:-90,90'],
            'longitude'      => ['required', 'numeric', 'between:-180,180'],
            'gmaps_url'      => ['nullable', 'string', 'max:500'],
            'description'    => ['nullable', 'string'],
            'photos'         => ['required', 'array', 'min:1'],
            'photos.*'       => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status_updated_at'] = now();

        unset($validated['photos']);

        $asset = Asset::create($validated);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('assets/images', 'public');
                $asset->images()->create([
                    'file_path'   => $path,
                    'file_name'   => $photo->getClientOriginalName(),
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
        $validated = $request->validate([
            'id_asset'       => ['required', 'string', 'max:20', 'unique:assets,id_asset,' . $asset->id],
            'province_id'    => ['required', 'exists:provinces,id'],
            'regency_id'     => ['required', 'exists:regencies,id'],
            'district_id'    => ['required', 'exists:districts,id'],
            'asset_type'     => ['required', 'in:Tanah,Bangunan'],
            'classification' => ['required', 'in:ROW,Fasilitas,Operasional'],
            'asset_group'    => ['required', 'in:Produksi,Non Produksi'],
            'status'         => ['required', 'in:Clear,Proses,Masalah'],
            'area_m2'        => ['required', 'numeric', 'min:0'],
            'latitude'       => ['required', 'numeric', 'between:-90,90'],
            'longitude'      => ['required', 'numeric', 'between:-180,180'],
            'gmaps_url'      => ['nullable', 'string', 'max:500'],
            'description'    => ['nullable', 'string'],
            'delete_images'   => ['nullable', 'array'],
            'delete_images.*' => ['exists:asset_images,id'],
            'photos'         => ['nullable', 'array'],
            'photos.*'       => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

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
                    'file_name'   => $photo->getClientOriginalName(),
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
            ->with('success', 'Aset dipindahkan ke tong sampah.');
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