@extends('layouts.app')

@section('title', 'Log Audit')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Log Audit</h1>
        <p class="text-sm text-gray-500">Riwayat aktivitas pengguna dalam sistem.</p>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Waktu</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Pengguna</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Aktivitas</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Data</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">08 Agustus 2026 09:00</td>
                        <td class="whitespace-nowrap px-5 py-4 text-sm font-medium text-gray-700">Admin</td>
                        <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">Memperbarui data aset</td>
                        <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">06.01.00001</td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">08 Agustus 2026 08:30</td>
                        <td class="whitespace-nowrap px-5 py-4 text-sm font-medium text-gray-700">Petugas 01</td>
                        <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">Menambahkan aset</td>
                        <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">06.01.00002</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection
