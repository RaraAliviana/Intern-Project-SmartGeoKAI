{{--
    wire:poll.30s = data otomatis di-refresh tiap 30 detik tanpa reload halaman,
    memenuhi requirement "diperbarui secara otomatis mengikuti data terkini".
--}}
<div wire:poll.30s>

    {{-- ============================================================
        STAT CARDS
    ============================================================= --}}
    <div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-5">

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs">
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-brand-50">
                <i class="fa-solid fa-layer-group text-brand-500"></i>
            </div>
            <p class="mt-4 text-sm text-gray-500">Total Aset</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalAssets, 0, ',', '.') }}</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs">
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-success-50">
                <i class="fa-solid fa-circle-check text-success-500"></i>
            </div>
            <p class="mt-4 text-sm text-gray-500">Status Clear</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($clearCount, 0, ',', '.') }}</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs">
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-warning-50">
                <i class="fa-solid fa-spinner text-warning-500"></i>
            </div>
            <p class="mt-4 text-sm text-gray-500">Status Proses</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($prosesCount, 0, ',', '.') }}</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs">
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-error-50">
                <i class="fa-solid fa-triangle-exclamation text-error-500"></i>
            </div>
            <p class="mt-4 text-sm text-gray-500">Status Masalah</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($masalahCount, 0, ',', '.') }}</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs">
            <div class="flex h-11 w-11 items-center justify-center rounded-full bg-brand-50">
                <i class="fa-solid fa-user-check text-brand-500"></i>
            </div>
            <p class="mt-4 text-sm text-gray-500">Petugas Aktif</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($totalPetugasAktif, 0, ',', '.') }}</p>
        </div>

    </div>

    {{-- ============================================================
        CHARTS
    ============================================================= --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Pie chart: distribusi status --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs lg:col-span-1">
            <h2 class="mb-1 text-base font-semibold text-gray-800">Distribusi Status Aset</h2>
            <p class="mb-4 text-xs text-gray-500">Proporsi aset berdasarkan status saat ini</p>

            <div
                wire:key="pie-{{ $pieHash }}"
                wire:ignore
                x-data="pieChartWidget(@js($pieData))"
                x-init="render()"
            >
                <canvas x-ref="canvas" height="240"></canvas>
            </div>

            <div class="mt-4 flex flex-wrap justify-center gap-4 text-xs text-gray-600">
                <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-[#12b76a]"></span> Clear</span>
                <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-[#f79009]"></span> Proses</span>
                <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-[#f04438]"></span> Masalah</span>
            </div>
        </div>

        {{-- Bar chart: aset per provinsi --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs lg:col-span-2">
            <h2 class="mb-1 text-base font-semibold text-gray-800">Jumlah Aset per Provinsi</h2>
            <p class="mb-4 text-xs text-gray-500">Sebaran total aset di tiap provinsi</p>

            @if ($provinceLabels->isEmpty())
                <div class="flex h-[240px] flex-col items-center justify-center text-gray-400">
                    <i class="fa-solid fa-chart-column text-3xl"></i>
                    <p class="mt-2 text-sm">Belum ada data aset per provinsi.</p>
                </div>
            @else
                <div
                    wire:key="bar-{{ $barHash }}"
                    wire:ignore
                    x-data="barChartWidget(@js($provinceLabels), @js($provinceData))"
                    x-init="render()"
                >
                    <canvas x-ref="canvas" height="240"></canvas>
                </div>
            @endif
        </div>

    </div>

</div>

@once
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

    <script>
        function pieChartWidget(dataObj) {
            return {
                render() {
                    new Chart(this.$refs.canvas, {
                        type: 'doughnut',
                        data: {
                            labels: Object.keys(dataObj),
                            datasets: [{
                                data: Object.values(dataObj),
                                backgroundColor: ['#12b76a', '#f79009', '#f04438'],
                                borderWidth: 0,
                            }],
                        },
                        options: {
                            plugins: { legend: { display: false } },
                            cutout: '65%',
                        },
                    });
                },
            };
        }

        function barChartWidget(labels, data) {
            return {
                render() {
                    new Chart(this.$refs.canvas, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Jumlah Aset',
                                data: data,
                                backgroundColor: '#465fff',
                                borderRadius: 6,
                                maxBarThickness: 48,
                            }],
                        },
                        options: {
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true, ticks: { precision: 0 } },
                            },
                        },
                    });
                },
            };
        }
    </script>
@endonce