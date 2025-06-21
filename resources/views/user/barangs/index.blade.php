<x-user-layout>
    <x-slot name="title">Barang</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Barang') }}
        </h2>
    </x-slot>

    <x-slot name="script">
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

        <script>
            $(function () {
                $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    stateSave: true,
                    order: [[0, 'desc']], // Default order by first column (ID usually)
                    ajax: {
                        url: '{!! url()->current() !!}',
                    },
                    language: {
                        url: '/js/id.json' // Ensure this file exists
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'nomor_produk_katalog', name: 'nomor_produk_katalog' },
                        { data: 'nama_produk', name: 'nama_produk' },
                        { data: 'satuan', name: 'satuan' },
                        {
                            data: 'jumlah_stok', // Use 'jumlah_stok' here because formatted_stok returns HTML
                            name: 'jumlah_stok',
                            type: 'num',
                            render: function (data, type, row) {
                                // DataTables will automatically use the raw value for sorting/searching
                                // but for display, we use the pre-formatted HTML from the controller
                                if (type === 'display') return row.formatted_stok;
                                return data; // Return raw data for sorting/filtering
                            }
                        },
                        {
                            data: 'harga', // Use 'harga' here because formatted_harga returns HTML
                            name: 'harga',
                            type: 'num',
                            render: function (data, type, row) {
                                if (type === 'display') return row.formatted_harga;
                                return data;
                            }
                        },
                        {
                            data: 'expired', // Use 'expired' here because formatted_expired returns HTML
                            name: 'expired',
                            type: 'date',
                            render: function (data, type, row) {
                                if (type === 'display') return row.formatted_expired;
                                return data;
                            }
                        },
                        {
                            data: 'formatted_masuk', // This is a raw number from controller, no complex JS render needed
                            name: 'stok_masuk', // Actual column name for sorting/searching
                            render: function (data) {
                                return data ?? 0;
                            }
                        },
                        {
                            data: 'formatted_keluar', // This is a raw number from controller, no complex JS render needed
                            name: 'stok_keluar', // Actual column name for sorting/searching
                            render: function (data) {
                                return data ?? 0;
                            }
                        },
                        { data: 'keterangan', name: 'keterangan' },
                        {
                            data: 'action', // This matches the 'action' addColumn from the controller
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            width: '15%'
                        },
                    ]
                });
            });
        </script>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (isset($stokRendahBarangs) && $stokRendahBarangs->isNotEmpty())
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Peringatan Stok Rendah!</p>
                    <p>Berikut adalah barang dengan **stok di bawah 100 unit**:</p>
                    <ul class="list-disc ml-5 mt-2">
                        @foreach ($stokRendahBarangs as $barang)
                            <li>{{ $barang->nama_produk }} (Stok: {{ $barang->jumlah_stok }} pcs)</li>
                        @endforeach
                    </ul>
                    <p class="mt-2 text-sm">Segera lakukan restock.</p>
                </div>
            @endif

            @if (isset($kadaluarsaBarangs) && $kadaluarsaBarangs->isNotEmpty())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Peringatan Barang Kadaluarsa!</p>
                    <p>Berikut adalah barang yang **akan kadaluarsa atau sudah kadaluarsa di tahun ini ({{ now()->year }})**:</p>
                    <ul class="list-disc ml-5 mt-2">
                        @foreach ($kadaluarsaBarangs as $barang)
                            <li>{{ $barang->nama_produk }} (Kadaluarsa: {{ \Carbon\Carbon::parse($barang->expired)->format('d M Y') }})</li>
                        @endforeach
                    </ul>
                    <p class="mt-2 text-sm">Segera periksa dan ambil tindakan.</p>
                </div>
            @endif

            <div class="overflow-hidden shadow sm:rounded-md">
                <div class="px-4 py-5 bg-white sm:p-6">
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
                                <th>Aksi</th> {{-- Add header for action column --}}
                            </tr>
                        </thead>
                        <tbody>
                            {{-- DataTables will populate this via Ajax --}}
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-user-layout>