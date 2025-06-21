<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Penjualan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('admin.penjualans.store') }}">
                        @csrf

                        {{-- Input Tanggal Penjualan --}}
                        <div class="mb-4">
                            <label for="waktu_terjual" class="block text-sm font-medium text-gray-700">Waktu Terjual</label>
                            <input type="date" name="waktu_terjual" id="waktu_terjual"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('waktu_terjual') border-red-500 @enderror"
                                value="{{ old('waktu_terjual', date('Y-m-d')) }}" required>
                            @error('waktu_terjual')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Input Barang & Jumlah Dinamis --}}
                        <div id="barang-wrapper">
                            <div class="barang-group mb-4">
                                <label class="block text-sm font-medium text-gray-700">Barang</label>
                                <select name="id_barang[]" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 @error('id_barang.*') border-red-500 @enderror">
                                    <option value="">Pilih Barang</option>
                                    @foreach($barangs as $barang)
                                        <option value="{{ $barang->id }}">
                                            {{ $barang->nama_produk }} (Rp{{ number_format($barang->harga, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>

                                <label class="block text-sm font-medium text-gray-700 mt-2">Jumlah Terjual</label>
                                <input type="number" name="jumlahTerjual[]" min="1" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 @error('jumlahTerjual.*') border-red-500 @enderror">
                            </div>
                        </div>

                        {{-- Tombol Tambah Barang --}}
                        <button type="button" onclick="tambahBarang()" class="mb-4 px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            Tambah Barang (Max 5)
                        </button>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Buat Penjualan') }}
                            </button>
                            <a href="{{ route('admin.penjualans.index') }}" class="ml-4 inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Batal') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script Dinamis --}}
    <script>
        let count = 1;
        function tambahBarang() {
            if (count >= 5) {
                alert("Maksimal 5 barang dalam satu transaksi!");
                return;
            }

            const wrapper = document.getElementById('barang-wrapper');
            const group = wrapper.querySelector('.barang-group');
            const clone = group.cloneNode(true);

            // Kosongkan inputnya
            clone.querySelectorAll('select, input').forEach(el => el.value = '');
            wrapper.appendChild(clone);
            count++;
        }
    </script>
</x-app-layout>