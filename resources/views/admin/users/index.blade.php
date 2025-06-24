<x-app-layout>
    <x-slot name="title">Kelola Pengguna</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Pengguna') }}
        </h2>
    </x-slot>

    <x-slot name="script">
        <!-- DataTables + Tailwind -->
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
        <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

        <script>
            $(function () {
                const table = $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    stateSave: true,
                    order: [[0, 'desc']],
                    ajax: '{!! url()->current() !!}',
                    language: {
                        url: '/js/id.json'
                    },
                    drawCallback: function () {
                        // Ambil pagination dan pindahkan ke div custom
                        const paginate = $('.dataTables_paginate');
                        $('#custom-pagination').html(paginate.html());

                        // Tambah styling Tailwind ke pagination
                        $('#custom-pagination ul.pagination')
                            .addClass('flex justify-center space-x-2 mt-6 text-sm');

                        $('#custom-pagination ul.pagination li.paginate_button').each(function () {
                            const li = $(this);
                            const a = li.find('a');

                            a.addClass('px-3 py-1 border rounded transition');

                            if (li.hasClass('active')) {
                                a.addClass('bg-blue-600 text-white');
                            } else if (li.hasClass('disabled')) {
                                a.addClass('bg-gray-200 text-gray-500 cursor-not-allowed');
                            } else {
                                a.addClass('bg-yellow-100 text-gray-700 hover:bg-yellow-300');
                            }
                        });

                        // Style search dan length
                        $('div.dataTables_filter input')
                            .addClass('border border-gray-300 rounded px-3 py-2 text-sm ml-2')
                            .attr('placeholder', 'Cari pengguna...');

                        $('div.dataTables_length select')
                            .addClass('border border-gray-300 rounded px-2 py-1 text-sm');
                    },
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'id', name: 'id' },
                        { data: 'name', name: 'name' },
                        { data: 'username', name: 'username' },
                        { data: 'raw_password', name: 'raw_password', orderable: false, searchable: false },
                        { data: 'roles', name: 'roles' },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                    ]
                });
            });
        </script>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <a href="{{ route('admin.users.create') }}"
                   class="px-4 py-2 font-bold text-white bg-blue-600 rounded shadow hover:bg-blue-800 transition">
                    + Tambah Pengguna
                </a>
            </div>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Sukses!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="overflow-hidden shadow sm:rounded-md bg-white">
                <div class="px-4 py-5 bg-white sm:p-6">
                    <div class="overflow-x-auto">
                        <table id="dataTable" class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Password</th>
                                    <th>Roles</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Diisi oleh DataTables --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ✅ Pagination di luar script -->
            <div id="custom-pagination" class="mt-6 flex justify-end"></div>
        </div>
    </div>

    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 text-center text-gray-500 text-sm">
            Copyright &copy; 2025 <span class="font-semibold">PT. Borneo Sejahtera Medika</span>
        </div>
    </footer>
</x-app-layout>