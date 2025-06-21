<x-admin-layout>
    <x-slot name="title">Dashboard Admin</x-slot>

    <x-slot name="script">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            $(function() {
                // Inisialisasi DataTables untuk "List Stock"
                var datatable = $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    stateSave: true,
                    order: [[ 0, 'asc' ]], // Urutan default berdasarkan kolom pertama (No), ascending
                    ajax: {
                        // URL AJAX untuk DataTables.
                        // Ini akan memanggil DashboardController@index yang akan menangani permintaan AJAX
                        url: '{!! url()->current() !!}',
                    },
                    language: {
                        url: '/js/id.json' // Pastikan file id.json ada di public/js
                    },
                    // Nonaktifkan fitur DataTables yang tidak terlihat di gambar
                    searching: false, // Nonaktifkan fitur pencarian
                    paging: false,    // Nonaktifkan fitur pagination
                    info: false,      // Nonaktifkan "Showing X of Y entries"
                    lengthChange: false, // Nonaktifkan "Show X entries" dropdown
                    // Pastikan tidak ada tombol aksi atau fitur lain yang tidak diinginkan di dashboard
                    // Menambahkan columnDefs untuk menyembunyikan kolom jika diperlukan
                    columnDefs: [
                         // Contoh jika ingin menyembunyikan kolom 'expired'
                         // { targets: [/* indeks kolom expired */], visible: false, searchable: false },
                         // Contoh jika ingin menyembunyikan kolom 'action'
                         // { targets: [/* indeks kolom action */], visible: false, searchable: false }
                    ],

                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }, // Kolom No
                        { data: 'nomor_produk_katalog', name: 'nomor_produk_katalog' },
                        { data: 'nama_produk', name: 'nama_produk' },
                        { data: 'satuan', name: 'satuan' },
                        {
                            data: 'formatted_stok', // Gunakan 'formatted_stok' yang sudah di-rawColumns di controller
                            name: 'jumlah_stok', // Nama asli kolom untuk sorting/searching
                            type: 'num', // Beri tahu DataTables ini adalah angka
                            render: function(data, type, row) {
                                // Aplikasikan styling untuk stok rendah jika diperlukan
                                if (type === 'display' && row.jumlah_stok < 100) {
                                    return '<span class="font-bold text-red-600">' + data + '</span>';
                                }
                                return data;
                            }
                        },
                        {
                            data: 'formatted_harga', // Gunakan 'formatted_harga' dari controller
                            name: 'harga', // Nama asli kolom untuk sorting/searching
                            type: 'num', // Beri tahu DataTables ini adalah angka
                        },
                        { data: 'status', name: 'status' }, // Kolom Status
                        { data: 'keterangan', name: 'keterangan' }, // Kolom Keterangan
                    ],
                });

                // ------------- Pie Chart for Penjualan -------------
                const salesData = @json($salesData ?? []);

                if (Object.keys(salesData).length > 0) {
                    const ctx = document.getElementById('salesPieChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: Object.keys(salesData),
                            datasets: [{
                                data: Object.values(salesData),
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.7)',
                                    'rgba(54, 162, 235, 0.7)',
                                    'rgba(255, 206, 86, 0.7)',
                                    'rgba(75, 192, 192, 0.7)',
                                    'rgba(153, 102, 255, 0.7)',
                                    'rgba(255, 159, 64, 0.7)'
                                ],
                                borderColor: [
                                    'rgba(255, 99, 132, 1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(153, 102, 255, 1)',
                                    'rgba(255, 159, 64, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        font: {
                                            size: 14
                                        }
                                    }
                                },
                                title: {
                                    display: false,
                                }
                            },
                            maintainAspectRatio: false
                        }
                    });
                } else {
                    $('#salesPieChartContainer').html('<p class="text-center text-gray-500 text-lg">Tidak ada data penjualan untuk ditampilkan.</p>');
                }
            });
        </script>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Menghapus Success Alert dari dashboard --}}
            {{-- @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Sukses!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif --}}

            {{-- Menghapus Alert untuk Barang Stok Rendah dari dashboard --}}
            {{-- @if(isset($stokRendahBarangs) && $stokRendahBarangs->isNotEmpty())
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Peringatan Stok Rendah!</p>
                    <p>Berikut adalah barang dengan **stok di bawah 100 unit**:</p>
                    <ul class="list-disc ml-5 mt-2">
                        @foreach($stokRendahBarangs as $barang)
                            <li>{{ $barang->nama_produk }} (Stok: {{ $barang->jumlah_stok }} pcs)</li>
                        @endforeach
                    </ul>
                    <p class="mt-2 text-sm">Segera lakukan restock.</p>
                </div>
            @endif --}}

            {{-- Menghapus Alert untuk Barang Kadaluarsa Tahun Ini dari dashboard --}}
            {{-- @if(isset($kadaluarsaBarangs) && $kadaluarsaBarangs->isNotEmpty())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Peringatan Barang Kadaluarsa!</p>
                    <p>Berikut adalah barang yang akan **kadaluarsa atau sudah kadaluarsa di tahun ini ({{ \Carbon\Carbon::now()->year }})**:</p>
                    <ul class="list-disc ml-5 mt-2">
                        @foreach($kadaluarsaBarangs as $barang)
                            <li>{{ $barang->nama_produk }} (Kadaluarsa: {{ \Carbon\Carbon::parse($barang->expired)->format('d M Y') }})</li>
                        @endforeach
                    </ul>
                    <p class="mt-2 text-sm">Segera periksa dan ambil tindakan.</p>
                </div>
            @endif --}}

            {{-- Bagian List Stock (Tabel DataTables) --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold">List Stock</h3>
                        {{-- Menghapus tombol "+ Tambah Barang" dari dashboard --}}
                        {{-- <a href="{{ route('admin.barangs.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                            + Tambah Barang
                        </a> --}}
                        {{-- Tetap tampilkan tombol "Lihat Semua" --}}
                        <a href="{{ route('admin.barangs.index') }}" class="text-blue-600 hover:text-blue-800 text-sm ml-4">Lihat Semua</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table id="dataTable" class="min-w-full bg-blue-500 text-white rounded-lg overflow-hidden">
                            <thead>
                                <tr class="bg-blue-600">
                                    <th class="py-3 px-4 text-left">No</th>
                                    <th class="py-3 px-4 text-left">Nomor Barang</th>
                                    <th class="py-3 px-4 text-left">Nama Barang</th>
                                    <th class="py-3 px-4 text-left">Satuan</th>
                                    <th class="py-3 px-4 text-left">Jumlah Stok</th>
                                    <th class="py-3 px-4 text-left">Harga</th>
                                    <th class="py-3 px-4 text-left">Status</th>
                                    <th class="py-3 px-4 text-left">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-800 bg-white divide-y divide-gray-200">
                                {{-- DataTables akan mengisi ini via AJAX --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Bagian Penjualan dan Pie Chart --}}
            <div class="mt-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-2xl font-bold mb-4">Penjualan</h3>
                    <div class="flex justify-center items-center h-56 relative" id="salesPieChartContainer">
                        <canvas id="salesPieChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>
    {{-- Footer --}}
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 text-center text-gray-500 text-sm">
            Copyright &copy; 2025 <span class="font-semibold">PT. Borneo Sejahtera Medika</span>
        </div>
    </footer>
</x-admin-layout>