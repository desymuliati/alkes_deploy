<x-app-layout>
    <x-slot name="title">Barang</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Barang') }}
        </h2>
    </x-slot>

    <x-slot name="script">
        <!-- jQuery & DataTables -->
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.tailwindcss.min.js"></script>

        <script>
            $(document).ready(function () {
                const table = $('#dataTable').DataTable({
                    processing: true,
                    serverSide: false, // NON-server-side
                    responsive: true,
                    pagingType: "full_numbers",
                    dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>',
                    ajax: {
                        url: '{{ route('admin.barangs.index') }}',
                        dataSrc: ''
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'nomor_produk_katalog', name: 'nomor_produk_katalog' },
                        { data: 'nama_produk', name: 'nama_produk' },
                        { data: 'satuan', name: 'satuan' },
                        {
                            data: 'jumlah_stok',
                            render: function (data, type, row) {
                                return row.formatted_stok;
                            }
                        },
                        {
                            data: 'harga',
                            render: function (data, type, row) {
                                return row.formatted_harga;
                            }
                        },
                        {
                            data: 'expired',
                            render: function (data, type, row) {
                                return row.formatted_expired;
                            }
                        },
                        { data: 'stok_masuk', render: data => data ?? 0 },
                        { data: 'stok_keluar', render: data => data ?? 0 },
                        { data: 'keterangan' },
                        { data: 'action', orderable: false, searchable: false, className: 'text-center' },
                    ],
                    initComplete: function () {
                        $('#loading-spinner').hide();
                        $('#dataTable').show();
                    }
                });

                // Sembunyikan dulu tabel sebelum data load
                $('#dataTable').hide();
            });
        </script>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-10">
                <a href="{{ route('admin.barangs.create') }}"
                   class="px-4 py-2 font-bold text-white bg-green-500 rounded-lg shadow hover:bg-green-700 transition">
                    + Tambah Barang
                </a>
            </div>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <strong class="font-bold">Sukses!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (isset($stokRendahBarangs) && $stokRendahBarangs->isNotEmpty())
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded-lg shadow-md">
                    <p class="font-bold">Peringatan Stok Rendah!</p>
                    <ul class="list-disc ml-5 mt-2">
                        @foreach ($stokRendahBarangs as $barang)
                            <li>{{ $barang->nama_produk }} (Stok: {{ $barang->jumlah_stok }} pcs)</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (isset($kadaluarsaBarangs) && $kadaluarsaBarangs->isNotEmpty())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg shadow-md">
                    <p class="font-bold">Peringatan Barang Kadaluarsa!</p>
                    <ul class="list-disc ml-5 mt-2">
                        @foreach ($kadaluarsaBarangs as $barang)
                            <li>{{ $barang->nama_produk }} (Kadaluarsa: {{ \Carbon\Carbon::parse($barang->expired)->format('d M Y') }})</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 sm:px-20">
                <div class="text-2xl font-bold text-gray-800 mb-4">Daftar Barang</div>

                <!-- Spinner loading -->
                <div id="loading-spinner" class="flex justify-center items-center py-10">
                    <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    <span class="ml-2 text-gray-500 text-sm">Memuat data barang...</span>
                </div>

                <div class="overflow-x-auto">
                    <table id="dataTable" class="min-w-full divide-y divide-gray-200">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Nomor Produk Katalog</th>
                            <th>Nama Produk</th>
                            <th>Satuan</th>
                            <th>Jumlah Stok</th>
                            <th>Harga</th>
                            <th>Expired</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Keterangan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        {{-- Diisi oleh DataTables --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 text-center text-gray-500 text-sm">
            Copyright &copy; 2025 <span class="font-semibold">PT. Borneo Sejahtera Medika</span>
        </div>
    </footer>
</x-app-layout>