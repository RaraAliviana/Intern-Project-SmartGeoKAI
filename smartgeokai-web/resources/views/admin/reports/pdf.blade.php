<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Formulir Laporan Data Aset - PT Kereta Api Indonesia (Persero)</title>

    <style>
        @page {
            margin: 20px 20px 25px 20px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            color: #000;
            line-height: 1.2;
        }

        /* UTILITY */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }

        /* KOP HEADER FORMULIR (GRID) */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .kop-table td {
            border: 1.5px solid #000;
            padding: 6px 4px;
            vertical-align: middle;
        }

        .logo-box {
            width: 20%;
            text-align: center;
            height: 52px;
        }

        .logo-box img {
            max-height: 48px;
            width: auto;
            object-fit: contain;
        }

        .title-box {
            width: 60%;
            text-align: center;
        }

        .title-box h1 {
            margin: 0;
            font-size: 13px; /* Diperbesar */
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .title-box h2 {
            margin: 3px 0 0;
            font-size: 10px; /* Diperbesar */
            font-weight: normal;
        }

        .title-box h3 {
            margin: 4px 0 0;
            font-size: 13px; /* Diperbesar */
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        .empty-box {
            width: 20%;
        }

        /* METADATA FILTER TABLE (DIPERBESAR) */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .meta-table td {
            border: 1px solid #000;
            padding: 4px 8px; /* Padding diperbesar */
            font-size: 9.5px; /* Font metadata diperbesar */
            vertical-align: top;
        }

        /* TABEL DATA UTAMA */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .data-table th {
            background-color: #c6d9f1; /* Biru Muda KAI */
            border: 1px solid #000;
            padding: 6px 2px;
            text-align: center;
            font-size: 8.5px;
            font-weight: bold;
        }

        .data-table td {
            border: 1px solid #000;
            padding: 4px 3px;
            font-size: 8px;
            vertical-align: top;
            word-wrap: break-word;
        }

        /* CATATAN BOX */
        .section-title {
            font-weight: bold;
            font-size: 9px;
            margin-top: 10px;
            margin-bottom: 3px;
        }

        .catatan-box {
            width: 100%;
            border: 1.5px solid #000;
            height: 35px;
            padding: 5px;
            font-size: 8.5px;
        }

        /* FOOTER / KETERANGAN */
        .footer-table {
            width: 100%;
            margin-top: 8px;
            border-collapse: collapse;
        }

        .footer-table td {
            vertical-align: top;
            font-size: 8.5px;
        }
    </style>
</head>

<body>

    {{-- KOP HEADER FORMULIR --}}
    <table class="kop-table">
        <tr>
            <td class="logo-box">
                @php
                    $logoPath = public_path('images/logo-kai.png');
                @endphp
                @if(file_exists($logoPath))
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents($logoPath)) }}" alt="KAI Logo">
                @else
                    {{-- Alternatif Vector SVG jika modul PHP GD belum aktif --}}
                    <svg width="120" height="40" viewBox="0 0 200 65" xmlns="http://www.w3.org/2000/svg">
                        <path d="M 10,10 L 45,10 L 25,55 L 10,55 Z" fill="#2D2A70" />
                        <path d="M 32,10 L 67,10 L 47,55 L 32,55 Z" fill="#ED1C24" />
                        <path d="M 52,10 L 87,10 L 67,55 L 52,55 Z" fill="#2D2A70" />
                        <path d="M 60,30 L 105,10 L 105,25 L 75,38 Z" fill="#ED1C24" />
                        <path d="M 90,10 L 125,10 L 105,55 L 90,55 Z" fill="#2D2A70" />
                        <path d="M 120,10 L 135,10 L 115,55 L 100,55 Z" fill="#2D2A70" />
                        <path d="M 130,10 L 175,10 L 170,22 L 140,22 L 135,32 L 165,32 L 160,44 L 130,44 L 125,55 L 110,55 Z" fill="#2D2A70" />
                        <path d="M 175,10 L 190,10 L 170,55 L 155,55 Z" fill="#2D2A70" />
                    </svg>
                @endif
            </td>
            <td class="title-box">
                <h1 class="uppercase">PT. KERETA API INDONESIA (PERSERO)</h1>
                <h2>Sistem Informasi Management & Monitoring Aset (SmartGeo)</h2>
            </td>
            <td class="empty-box"></td>
        </tr>
        <tr>
            <td class="logo-box"></td>
            <td class="title-box">
                <h3 class="uppercase">FORMULIR LAPORAN DATA ASET</h3>
            </td>
            <td class="empty-box"></td>
        </tr>
    </table>

    {{-- METADATA / INFORMASI FILTER --}}
    <table class="meta-table">
        <tr>
            <td class="font-bold" style="width: 110px;">Provinsi</td>
            <td>: {{ $provinceName }}</td>
            <td class="font-bold" style="width: 100px;">Status Aset</td>
            <td>: {{ $status }}</td>
        </tr>
        <tr>
            <td class="font-bold">Kabupaten/Kota</td>
            <td>: {{ $regencyName }}</td>
            <td class="font-bold">Jenis Aset</td>
            <td>: {{ $assetType }}</td>
        </tr>
        <tr>
            <td class="font-bold">Kecamatan</td>
            <td>: {{ $districtName }}</td>
            <td class="font-bold">Tanggal Cetak</td>
            <td>: {{ \Carbon\Carbon::parse($generatedAt)->format('d/m/Y H:i') }} WIB</td>
        </tr>
    </table>

    {{-- TABEL DATA UTAMA --}}
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 3%;">NO</th>
                <th style="width: 8%;">ID ASET</th>
                <th style="width: 6%;">STATUS</th>
                <th style="width: 10%;">PROVINSI</th>
                <th style="width: 11%;">KABUPATEN/KOTA</th>
                <th style="width: 11%;">KECAMATAN</th>
                <th style="width: 9%;">JENIS ASET</th>
                <th style="width: 9%;">KLASIFIKASI</th>
                <th style="width: 9%;">KELOMPOK</th>
                <th style="width: 7%;">LUAS (m²)</th>
                <th style="width: 9%;">INPUT OLEH</th>
                <th style="width: 8%;">NOTE</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($assets as $index => $asset)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-bold">{{ $asset->id_asset ?? '-' }}</td>
                    <td class="text-center font-bold">{{ $asset->status ?? '-' }}</td>
                    <td>{{ $asset->province_name ?? '-' }}</td>
                    <td>{{ $asset->regency_name ?? '-' }}</td>
                    <td>{{ $asset->district_name ?? '-' }}</td>
                    <td>{{ $asset->asset_type ?? '-' }}</td>
                    <td>{{ $asset->classification ?? '-' }}</td>
                    <td>{{ $asset->asset_group ?? '-' }}</td>
                    <td class="text-right">{{ number_format($asset->area_m2 ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $asset->creator_name ?? '-' }}</td>
                    <td>{{ $asset->description ?? '' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="text-center" style="padding: 15px;">
                        Tidak ada data aset yang sesuai dengan kriteria filter.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- CATATAN --}}
    <div class="section-title">Catatan :</div>
    <div class="catatan-box">
        Total data tercatat pada laporan ini sebanyak {{ number_format($assets->count(), 0, ',', '.') }} data aset.
    </div>

    {{-- KETERANGAN STATUS --}}
    <table class="footer-table">
        <tr>
            <td style="width: 100%;">
                <div class="font-bold">Keterangan Status :</div>
                <div>Clear : Bebas Masalah / Clean</div>
                <div>Proses : Dalam Penanganan / Sengketa Berjalan</div>
                <div>Masalah : Bermasalah / Sengketa</div>
            </td>
        </tr>
    </table>

</body>

</html>