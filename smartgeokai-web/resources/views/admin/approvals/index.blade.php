@extends('layouts.app')

@section('title', 'Approval')

@section('content')

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Approval Aset</h1>
        <p class="text-sm text-gray-500">Daftar aset yang menunggu persetujuan.</p>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-theme-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">ID Aset</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Nama Aset</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Petugas</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Tanggal</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                        <th class="px-5 py-3 text-xs font-semibold uppercase tracking-wide text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-5 py-4 text-sm font-medium text-gray-700">06.01.00001</td>
                        <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">Gedung Perkantoran</td>
                        <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">Petugas Demo</td>
                        <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">08 Agustus 2026</td>
                        <td class="whitespace-nowrap px-5 py-4">
                            <span class="inline-flex items-center rounded-full bg-warning-50 px-2.5 py-1 text-xs font-medium text-warning-600">
                                Menunggu
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-5 py-4">
                            <div class="flex gap-2">
                                <button type="button" class="rounded-lg bg-success-500 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-success-600">
                                    Setujui
                                </button>
                                <button type="button" class="rounded-lg bg-error-500 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-error-600">
                                    Tolak
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

@endsection
