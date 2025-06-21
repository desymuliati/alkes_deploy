<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Penjualan & Retur</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-md sm:rounded-lg">

                {{-- Header dan Tombol Tambah --}}
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <h3 class="text-xl font-semibold text-gray-800">Ringkasan Laporan</h3>
                    <a href="{{ route('admin.laporans.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:ring-green-500 transition">
                        Tambah Transaksi Retur
                    </a>
                </div>

                {{-- Form Filter Tanggal dan Unduh --}}
                <form method="GET" action="{{ route('admin.laporans.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:flex md:items-end md:space-x-4 gap-4 mb-6">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Mulai Tanggal</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div class="md:pt-6">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-indigo-700 focus:ring-indigo-500 transition">
                            Filter
                        </button>
                    </div>
                    <div class="md:pt-6">
                        <button type="button" onclick="downloadExcel()" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-gray-700 focus:ring-gray-500 transition">
                            Unduh Laporan (Detail Retur)
                        </button>
                    </div>
                </form>

                {{-- Ringkasan --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 text-sm">
                    <div class="bg-gray-100 p-4 rounded-lg text-center shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-700">Total Penjualan</h3>
                        <p class="text-3xl font-bold text-green-600">Rp{{ number_format($totalPenjualan, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg text-center shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-700">Total Nilai Retur</h3>
                        <p class="text-3xl font-bold text-red-600">Rp{{ number_format($totalNilaiRetur, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-gray-100 p-4 rounded-lg text-center shadow-sm">
                        <h3 class="text-lg font-semibold text-gray-700">Hasil Murni Penjualan</h3>
                        <p class="text-3xl font-bold text-blue-600">Rp{{ number_format($hasilMurniPenjualan, 0, ',', '.') }}</p>
                    </div>
                </div>

                {{-- Chart --}}
                <div class="mb-8 p-4 bg-gray-50 rounded-lg shadow-sm">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Penjualan Berdasarkan Produk</h3>
                    <button type="button" onclick="toggleDiagram()" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-xs font-semibold uppercase rounded-md hover:bg-blue-700 focus:ring-blue-500 transition mb-4">
                        Toggle Diagram
                    </button>
                    <div id="diagramContainer" style="max-width: 250px; margin: auto;"> {{-- Removed display: none initially --}}
                        <canvas id="penjualanChart"></canvas>
                    </div>
                    <p class="text-center text-gray-600 mt-4 text-sm" id="noDiagramData" style="display: none;">Tidak ada data penjualan produk dalam rentang tanggal ini.</p>
                </div>

                {{-- Tabel Retur --}}
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Detail Transaksi Retur</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-[1000px] divide-y divide-gray-200" id="returTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Retur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Penjualan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Retur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Diretur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai Retur</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($laporansRetur as $laporan)
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $laporan->id }}</td>
                                    <td class="px-6 py-4 text-sm text-blue-600">
                                        @if($laporan->penjualan)
                                            <a href="{{ route('admin.penjualans.show', $laporan->penjualan->id) }}" class="hover:underline">#{{ $laporan->penjualan->id }}</a>
                                        @else N/A @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ \Carbon\Carbon::parse($laporan->tanggal_retur)->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $laporan->barang->nama_produk ?? 'Barang Tidak Ditemukan' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $laporan->jumlah_retur }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">Rp{{ number_format($laporan->nilai_retur, 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 text-sm font-medium">
                                        <a href="{{ route('admin.laporans.show', $laporan->id) }}" class="text-blue-600 hover:text-blue-900 mr-2">Detail</a>
                                        <a href="{{ route('admin.laporans.edit', $laporan->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-2">Edit</a>
                                        <form action="{{ route('admin.laporans.destroy', $laporan->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" onclick="return confirm('Yakin hapus data ini?');" class="text-red-600 hover:text-red-900">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">Data retur tidak ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $laporansRetur->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Chart JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let penjualanChartInstance = null;
        const labels = {!! json_encode($penjualanByProduk->pluck('nama_produk')) !!};
        const dataValues = {!! json_encode($penjualanByProduk->pluck('total_terjual')) !!};
        const diagramContainer = document.getElementById('diagramContainer');
        const penjualanChartCanvas = document.getElementById('penjualanChart');
        const noDiagramDataElement = document.getElementById('noDiagramData');

        function initializeChart() {
            if (!penjualanChartCanvas) return;
            const ctx = penjualanChartCanvas.getContext('2d');
            if (penjualanChartInstance) penjualanChartInstance.destroy();

            if (labels.length === 0 || dataValues.length === 0 || dataValues.every(val => val === 0)) {
                penjualanChartCanvas.style.display = 'none';
                noDiagramDataElement.style.display = 'block';
                return;
            } else {
                penjualanChartCanvas.style.display = 'block';
                noDiagramDataElement.style.display = 'none';
            }

            const colors = labels.map((_, i) => `hsl(${(i * 137.5) % 360}, 70%, 50%)`);
            penjualanChartInstance = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: dataValues,
                        backgroundColor: colors,
                        borderColor: colors.map(c => c.replace('hsl', 'hsla').replace(')', ', 1)')),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.label + ': ' + tooltipItem.raw;
                                }
                            }
                        }
                    }
                }
            });
        }

        function toggleDiagram() {
            if (diagramContainer.style.display === 'none' || diagramContainer.style.display === '') {
                diagramContainer.style.display = 'block';
                initializeChart();
            } else {
                diagramContainer.style.display = 'none';
            }
        }

        function downloadExcel() {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            let exportUrl = "{{ route('admin.laporans.export') }}";
            let params = new URLSearchParams();
            if (startDate) params.append('start_date', startDate);
            if (endDate) params.append('end_date', endDate);
            window.location.href = exportUrl + '?' + params.toString();
        }

        document.addEventListener('DOMContentLoaded', () => {
            diagramContainer.style.display = 'none';
            initializeChart();
        });
    </script>

    {{-- Footer --}}
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 text-center text-gray-500 text-xs sm:text-sm">
            Copyright &copy; 2025 <span class="font-semibold">PT. Borneo Sejahtera Medika</span>
        </div>
    </footer>
</x-app-layout>