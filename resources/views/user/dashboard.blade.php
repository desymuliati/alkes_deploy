<x-user-layout>
    <x-slot name="title">Dashboard</x-slot>

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
                                    return '<span class="font-bold text-red-600">' + data + '</span>'; // Changed to red for low stock
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
            });
        </script>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold">List Stock</h3>
                        <a href="{{ route('user.barangs.index') }}" class="text-blue-600 hover:text-blue-800 text-sm ml-4">Lihat Semua</a>
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
        </div>
    </div>
    {{-- Footer --}}
    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 text-center text-gray-500 text-sm">
            Copyright &copy; 2025 <span class="font-semibold">PT. Borneo Sejahtera Medika</span>
        </div>
    </footer>
</x-user-layout>