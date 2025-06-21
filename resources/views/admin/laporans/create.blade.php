<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Transaksi Retur Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.laporans.store') }}">
                        @csrf

                        <!-- ID Penjualan Asal -->
                        <div class="mb-4">
                            <label for="id_penjualan" class="block text-sm font-medium text-gray-700">ID Penjualan Asal</label>
                            <select id="id_penjualan" name="id_penjualan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">-- Pilih Penjualan Asal --</option>
                                @foreach($penjualans as $penjualan)
                                    <option value="{{ $penjualan->id }}"
                                        {{ old('id_penjualan') == $penjualan->id ? 'selected' : '' }}>
                                        #{{ $penjualan->id }} - {{ $penjualan->barang->nama_produk ?? 'Barang tidak ditemukan' }} (Jumlah: {{ $penjualan->jumlahTerjual }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_penjualan')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Barang Diretur -->
                        <div class="mb-4">
                            <label for="id_barang" class="block text-sm font-medium text-gray-700">Barang Diretur</label>
                            <select id="id_barang" name="id_barang" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                <option value="">-- Pilih Penjualan Asal Dulu --</option>
                                {{-- Opsi barang akan diisi via AJAX --}}
                            </select>
                            @error('id_barang')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Retur -->
                        <div class="mb-4">
                            <label for="tanggal_retur" class="block text-sm font-medium text-gray-700">Tanggal Retur</label>
                            <input type="date" id="tanggal_retur" name="tanggal_retur" value="{{ old('tanggal_retur', \Carbon\Carbon::now()->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            @error('tanggal_retur')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jumlah Diretur -->
                        <div class="mb-4">
                            <label for="jumlah_retur" class="block text-sm font-medium text-gray-700">Jumlah Diretur</label>
                            <input type="number" id="jumlah_retur" name="jumlah_retur" value="{{ old('jumlah_retur') }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            @error('jumlah_retur')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tombol -->
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.laporans.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                                {{ __('Batal') }}
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Simpan Retur') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const penjualanSelect = document.getElementById('id_penjualan');
            const barangSelect = document.getElementById('id_barang');

            // Fungsi untuk memuat barang berdasarkan penjualan yang dipilih via AJAX
            function loadBarangForPenjualan(penjualanId, selectedBarangId = null) {
                // Debug: tampilkan URL
                const url = `{{ url('/admin/laporans/get-barang-by-penjualan') }}/${penjualanId}`;
                console.log('Mengambil data dari URL:', url);

                // Bersihkan dropdown barang
                barangSelect.innerHTML = '<option value="">-- Pilih Penjualan Asal Dulu --</option>';

                if (penjualanId) {
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            // Debug: tampilkan data dari server
                            console.log('Data dari server:', data);
                            if (data.length > 0) {
                                data.forEach(barang => {
                                    const option = document.createElement('option');
                                    option.value = barang.id;
                                    option.textContent = barang.nama_produk;
                                    if (selectedBarangId && barang.id == selectedBarangId) {
                                        option.selected = true;
                                    }
                                    barangSelect.appendChild(option);
                                });
                            } else {
                                const option = document.createElement('option');
                                option.value = "";
                                option.textContent = "-- Barang tidak ditemukan untuk penjualan ini --";
                                barangSelect.appendChild(option);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching barang:', error);
                            const option = document.createElement('option');
                            option.value = "";
                            option.textContent = "-- Gagal memuat barang --";
                            barangSelect.appendChild(option);
                        });
                }
            }

            // Saat dropdown penjualan berubah
            penjualanSelect.addEventListener('change', function() {
                loadBarangForPenjualan(this.value);
            });

            // Saat halaman dimuat, otomatis panggil jika ada nilai old
            const initialPenjualanId = penjualanSelect.value;
            const oldBarangId = "{{ old('id_barang') }}";

            if (initialPenjualanId) {
                loadBarangForPenjualan(initialPenjualanId, oldBarangId);
            }
        });
    </script>
    @endpush
</x-app-layout>