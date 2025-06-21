<x-user-layout>
    <x-slot name="title">Barang</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Barang') }}
        </h2>
    </x-slot>

    <x-slot name="script">
        <!-- jQuery (Pastikan selalu sebelum DataTables JS) -->
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

        <!-- DataTables Core CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <!-- DataTables Responsive CSS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.1/css/responsive.dataTables.min.css">
        <!-- DataTables Tailwind CSS Integration -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">

        <!-- DataTables Core JS -->
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
        <!-- DataTables Responsive JS -->
        <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
        <!-- DataTables Tailwind JS Integration -->
        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
        <!-- DataTables Responsive Tailwind JS Integration (Opsional, tapi melengkapi responsif) -->
        <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.tailwindcss.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    stateSave: true, // Menyimpan state tabel
                    order: [[0, 'desc']], // Urutan default
                    responsive: true, // Aktifkan responsif

                    // Penting: Sesuaikan URL ini jika route untuk user berbeda dari admin
                    ajax: {
                        // Jika Anda memiliki route terpisah untuk user (misal: 'user.barangs.index'), gunakan itu.
                        // Jika ini adalah halaman yang sama dengan admin tapi diakses user,
                        // pastikan controller Anda memiliki logika otorisasi yang sesuai.
                        url: '{{ route('user.barangs.index') }}',
                    },
                    language: {
                        url: '/js/id.json' // Pastikan file terjemahan ada di public/js/id.json
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%' },
                        { data: 'nomor_produk_katalog', name: 'nomor_produk_katalog' },
                        { data: 'nama_produk', name: 'nama_produk' },
                        { data: 'satuan', name: 'satuan' },
                        {
                            data: 'jumlah_stok',
                            name: 'jumlah_stok',
                            type: 'num',
                            render: function (data, type, row) {
                                return type === 'display' ? row.formatted_stok : data;
                            }
                        },
                        {
                            data: 'harga',
                            name: 'harga',
                            type: 'num',
                            render: function (data, type, row) {
                                return type === 'display' ? row.formatted_harga : data;
                            }
                        },
                        {
                            data: 'expired',
                            name: 'expired',
                            type: 'date',
                            render: function (data, type, row) {
                                return type === 'display' ? row.formatted_expired : data;
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
                        // Kolom 'Aksi' biasanya tidak diperlukan untuk tampilan user,
                        // karena user tidak melakukan edit/hapus langsung dari tabel ini.
                        // Jika ingin menampilkan detail, Anda bisa tambahkan kolom 'Detail' saja.
                        // {
                        //     data: 'action', // Jika ingin ada kolom aksi untuk user (misal: hanya tombol Detail)
                        //     name: 'action',
                        //     orderable: false,
                        //     searchable: false,
                        //     width: '10%',
                        //     className: 'dt-body-center dt-head-center',
                        //     render: function(data, type, row) {
                        //         // Contoh hanya tombol detail
                        //         return '<a href="/user/barangs/' + row.id + '" class="inline-flex items-center px-3 py-1 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Detail</a>';
                        //     }
                        // },
                    ],
                    // Sembunyikan kolom 'Aksi' jika tidak ada (sesuaikan jumlah kolom)
                    // Jika Anda menghapus kolom 'action' di atas, Anda juga harus menghapus <th> 'Aksi' di HTML.
                    columnDefs: [
                         // Contoh menyembunyikan kolom (jika tidak dikirim dari backend)
                        // { targets: 10, visible: false }, // Indeks kolom 'Aksi' adalah 10
                    ]
                });
            });
        </script>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Pesan Sukses (biasanya tidak ada untuk user view data) --}}
            {{-- @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Sukses!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif --}}

            {{-- Peringatan Stok Rendah --}}
            @if (isset($stokRendahBarangs) && $stokRendahBarangs->isNotEmpty())
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4 rounded-lg shadow-md" role="alert">
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

            {{-- Peringatan Barang Kadaluarsa --}}
            @if (isset($kadaluarsaBarangs) && $kadaluarsaBarangs->isNotEmpty())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg shadow-md" role="alert">
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

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 sm:px-20">
                <div class="text-2xl font-bold text-gray-800 mb-4">
                    Daftar Barang
                </div>
                <div class="overflow-x-auto">
                    <table id="dataTable" class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Produk Katalog</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Produk</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Stok</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expired</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Masuk</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keluar</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                {{-- Kolom 'Aksi' dihapus untuk tampilan user --}}
                                {{-- <th class="px-6 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th> --}}
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            {{-- Data akan diisi oleh DataTables via Ajax --}}
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
</x-user-layout>