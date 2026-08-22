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
            $regencies = Regency::where('province_id', $request->province_id)
                ->orderBy('name')
                ->get();
        }

        if ($request->filled('regency_id')) {
            $districts = District::where('regency_id', $request->regency_id)
                ->orderBy('name')
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER ASET (DIBATASI KHUSUS WILAYAH JATENG & DIY)
        |--------------------------------------------------------------------------
        */

        $assets = Asset::with(['province', 'regency', 'district'])

            // Batasi koordinat hanya di wilayah Jawa Tengah & DIY
            ->whereBetween('latitude', [-8.80, -6.30])
            ->whereBetween('longitude', [108.50, 111.60])

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

            // Filter status Masalah yang sudah stale
            ->when($request->filled('stale_days'), function ($query) use ($request) {
                $query->where('status', 'Masalah')
                    ->where(function ($q) use ($request) {
                        $q->where('status_updated_at', '<=', now()->subDays((int) $request->stale_days))
                          ->orWhereNull('status_updated_at');
                    });
            })

            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('id_asset', 'like', '%' . $search . '%')
                      ->orWhere('description', 'like', '%' . $search . '%');
                });
            })

            ->select([
                'id', 'id_asset', 'latitude', 'longitude', 'status',
                'asset_type', 'classification', 'asset_group', 'area_m2',
                'description', 'province_id', 'regency_id', 'district_id',
            ])
            ->get();

        /*
        |--------------------------------------------------------------------------
        | STATISTIK
        |--------------------------------------------------------------------------
        */

        $stats = [
            'total'   => $assets->count(),
            'clear'   => $assets->where('status', 'Clear')->count(),
            'proses'  => $assets->where('status', 'Proses')->count(),
            'masalah' => $assets->where('status', 'Masalah')->count(),
        ];

        return view('admin.assets.map', compact('assets', 'provinces', 'regencies', 'districts', 'stats'));
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
            'images'         => ['required', 'array', 'min:1'],
            'images.*'       => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status_updated_at'] = now();

        $images = $request->file('images');
        unset($validated['images']);

        $asset = Asset::create($validated);

        foreach ($images as $image) {
            $path = $image->store('assets/' . $asset->id, 'public');
            $asset->images()->create([
                'file_path'   => $path,
                'file_name'   => $image->getClientOriginalName(),
                'file_size'   => $image->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
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
            'delete_images'  => ['nullable', 'array'],
            'delete_images.*'=> ['exists:asset_images,id'],
            'new_images'     => ['nullable', 'array'],
            'new_images.*'   => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $validated['updated_by'] = auth()->id();

        if ($asset->status !== $validated['status']) {
            $validated['status_updated_at'] = now();
        }

        if ($request->has('delete_images')) {
            $imagesToDelete = $asset->images()->whereIn('id', $request->delete_images)->get();
            foreach ($imagesToDelete as $image) {
                Storage::disk('public')->delete($image->file_path);
                $image->delete();
            }
        }

        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $path = $image->store('assets/' . $asset->id, 'public');
                $asset->images()->create([
                    'file_path'   => $path,
                    'file_name'   => $image->getClientOriginalName(),
                    'file_size'   => $image->getSize(),
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        unset($validated['delete_images'], $validated['new_images']);
        $asset->update($validated);

        return redirect()->route('admin.assets.show', $asset)->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroy(Asset $asset)
    {
        foreach ($asset->images as $image) {
            Storage::disk('public')->delete($image->file_path);
        }
        $asset->delete();
        return redirect()->route('admin.assets.index')->with('success', 'Aset berhasil dihapus.');
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