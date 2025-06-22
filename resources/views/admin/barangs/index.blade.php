<x-app-layout>
    <x-slot name="title">Barang</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Barang') }}
        </h2>
    </x-slot>

    <x-slot name="script">
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
                $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    stateSave: true,
                    order: [[0, 'desc']],
                    responsive: true,
                    pagingType: "full_numbers",
                    dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>',
                    ajax: {
                        url: '{{ route('user.barangs.index') }}',
                    },
                    language: {
                        url: '/js/id.json'
                    },
                    drawCallback: function () {
                        $('ul.pagination').addClass('flex items-center space-x-1 justify-center text-sm mt-4');
                        $('ul.pagination li').addClass('border border-gray-300 rounded');
                        $('ul.pagination li a').addClass('px-3 py-1 block text-gray-700 hover:bg-gray-200');
                        $('ul.pagination li.active a').addClass('bg-blue-500 text-white');
                        $('ul.pagination li.disabled a').addClass('text-gray-400 cursor-not-allowed');
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
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
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            });
        </script>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                    <p class="font-bold">Peringatan Kadaluarsa!</p>
                    <ul class="list-disc ml-5 mt-2">
                        @foreach ($kadaluarsaBarangs as $barang)
                            <li>{{ $barang->nama_produk }} (Kadaluarsa: {{ \Carbon\Carbon::parse($barang->expired)->format('d M Y') }})</li>
                        @endforeach
                    </ul>
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
                                <th>#</th>
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