<x-app-layout>
    <x-slot name="title">Edit Penjualan</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.penjualans.update', $penjualan->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- Pilih Barang --}}
                        <div class="mb-4">
                            <label for="id_barang" class="block text-sm font-medium text-gray-700">Barang</label>
                            <select id="id_barang" name="id_barang"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('id_barang') border-red-500 @enderror" required>
                                <option value="">Pilih Barang</option>
                                @foreach($barangs as $barang)
                                    <option value="{{ $barang->id }}"
                                            data-harga="{{ $barang->harga }}"
                                            {{ (old('id_barang', $penjualan->id_barang) == $barang->id) ? 'selected' : '' }}>
                                        {{ $barang->nama_produk }} (Rp{{ number_format($barang->harga, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_barang')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Jumlah Terjual --}}
                        <div class="mb-4">
                            <label for="jumlahTerjual" class="block text-sm font-medium text-gray-700">Jumlah Terjual</label>
                            <input type="number" id="jumlahTerjual" name="jumlahTerjual" value="{{ old('jumlahTerjual', $penjualan->jumlahTerjual) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('jumlahTerjual') border-red-500 @enderror" required min="0">
                            @error('jumlahTerjual')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Harga Total Otomatis --}}
                        <div class="mb-4">
                            <label for="hargaTotalDisplay" class="block text-sm font-medium text-gray-700">Harga Total (Otomatis)</label>
                            <input type="text" id="hargaTotalDisplay"
                                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm" readonly>
                            {{-- Input hidden untuk kirim ke backend --}}
                            <input type="hidden" name="hargaTotal" id="hargaTotalHidden" value="{{ old('hargaTotal', $penjualan->hargaTotal ?? 0) }}">
                            @error('hargaTotal')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Waktu Terjual --}}
                        <div class="mb-4">
                            <label for="waktu_terjual" class="block text-sm font-medium text-gray-700">Waktu Terjual</label>
                            <input type="date" id="waktu_terjual" name="waktu_terjual" value="{{ old('waktu_terjual', $penjualan->waktu_terjual ? \Carbon\Carbon::parse($penjualan->waktu_terjual)->format('Y-m-d') : '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('waktu_terjual') border-red-500 @enderror" required>
                            @error('waktu_terjual')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tombol aksi --}}
                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.penjualans.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                                Batal
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Perbarui Penjualan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript untuk Perhitungan Otomatis --}}
    <x-slot name="script">
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const idBarangSelect = document.getElementById('id_barang');
                const jumlahTerjualInput = document.getElementById('jumlahTerjual');
                const hargaTotalDisplay = document.getElementById('hargaTotalDisplay');
                const hargaTotalHidden = document.getElementById('hargaTotalHidden');

                function calculateHargaTotal() {
                    const selectedOption = idBarangSelect.options[idBarangSelect.selectedIndex];
                    const hargaSatuan = parseFloat(selectedOption.dataset.harga || 0);
                    const jumlahTerjual = parseInt(jumlahTerjualInput.value) || 0;

                    const hargaTotal = hargaSatuan * jumlahTerjual;

                    hargaTotalDisplay.value = 'Rp' + new Intl.NumberFormat('id-ID').format(hargaTotal);
                    hargaTotalHidden.value = hargaTotal;
                }

                idBarangSelect.addEventListener('change', calculateHargaTotal);
                jumlahTerjualInput.addEventListener('input', calculateHargaTotal);

                // Hitung saat halaman load
                calculateHargaTotal();
            });
        </script>
    </x-slot>
</x-app-layout>