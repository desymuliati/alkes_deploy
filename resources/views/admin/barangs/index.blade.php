<x-app-layout>
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
                    order: [[0, 'desc']],
                    ajax: {
                        url: '{!! url()->current() !!}',
                    },
                    language: {
                        url: '/js/id.json'
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'nomor_produk_katalog', name: 'nomor_produk_katalog' },
                        { data: 'nama_produk', name: 'nama_produk' },
                        { data: 'satuan', name: 'satuan' },
                        {
                            data: 'jumlah_stok',
                            name: 'jumlah_stok',
                            type: 'num',
                            render: function (data, type, row) {
                                if (type === 'display') return row.formatted_stok;
                                return data;
                            }
                        },                        
                        {
                            data: 'harga',
                            name: 'harga',
                            type: 'num',
                            render: function (data, type, row) {
                                if (type === 'display') return row.formatted_harga;
                                return data;
                            }
                        },
                        {
                            data: 'expired',
                            name: 'expired',
                            type: 'date',
                            render: function (data, type, row) {
                                if (type === 'display') return row.formatted_expired;
                                return data;
                            }
                        },
                        {
                            data: 'stok_masuk',
                            name: 'stok_masuk',
                            render: function (data) {
                                return data ?? 0;
                            }
                        },
                        {
                            data: 'stok_keluar',
                            name: 'stok_keluar',
                            render: function (data) {
                                return data ?? 0;
                            }
                        },
                        { data: 'keterangan', name: 'keterangan' },
                        {
                            data: 'action',
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
            <div class="mb-10">
                <a href="{{ route('admin.barangs.create') }}"
                    class="px-4 py-2 font-bold text-white bg-green-500 rounded shadow-lg hover:bg-green-700">
                    + Tambah Barang
                </a>
            </div>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Sukses!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (isset($stokRendahBarangs) && $stokRendahBarangs->isNotEmpty())
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                    <p class="font-bold">Peringatan Stok Rendah!</p>
                    <p>Berikut adalah barang dengan <strong>stok di bawah 100 unit</strong>:</p>
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
                    <p>Berikut adalah barang yang <strong>akan kadaluarsa atau sudah kadaluarsa di tahun ini ({{ now()->year }})</strong>:</p>
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
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Diisi oleh DataTables via Ajax --}}
                        </tbody>
                    </table>
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
</x-app-layout>