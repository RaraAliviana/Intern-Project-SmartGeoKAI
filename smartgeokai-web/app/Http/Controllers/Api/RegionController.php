<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Untuk petugas, biasanya cuma perlu 1 provinsi (wilayah kerjanya sendiri),
     * tapi tetap dikembalikan sebagai list untuk konsistensi format response.
     */
    public function provinces(Request $request)
    {
        $user = $request->user();

        $provinces = Province::where('id', $user->province_id)->get();

        return response()->json(['success' => true, 'data' => $provinces]);
    }

    public function regencies(Request $request)
    {
        $request->validate(['province_id' => 'required|exists:provinces,id']);

        $regencies = Regency::where('province_id', $request->province_id)
            ->orderBy('name')
            ->get();

        return response()->json(['success' => true, 'data' => $regencies]);
    }

    public function districts(Request $request)
    {
        $request->validate(['regency_id' => 'required|exists:regencies,id']);

        $districts = District::where('regency_id', $request->regency_id)
            ->orderBy('name')
            ->get();

        return response()->json(['success' => true, 'data' => $districts]);
    }
}