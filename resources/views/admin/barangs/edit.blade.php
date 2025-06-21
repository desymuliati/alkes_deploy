<x-app-layout>
    <x-slot name="title">Edit Barang</x-slot>

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Edit Barang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('admin.barangs.update', $barang->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="nomor_produk_katalog" class="block text-sm font-medium text-gray-700">Nomor Produk Katalog</label>
                            <input type="text" name="nomor_produk_katalog" id="nomor_produk_katalog" value="{{ old('nomor_produk_katalog', $barang->nomor_produk_katalog) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('nomor_produk_katalog') border-red-500 @enderror" required>
                            @error('nomor_produk_katalog')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="nama_produk" class="block text-sm font-medium text-gray-700">Nama Produk</label>
                            <input type="text" name="nama_produk" id="nama_produk" value="{{ old('nama_produk', $barang->nama_produk) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('nama_produk') border-red-500 @enderror" required>
                            @error('nama_produk')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="satuan" class="block text-sm font-medium text-gray-700">Satuan</label>
                            <select name="satuan" id="satuan"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('satuan') border-red-500 @enderror" required>
                                <option value="">-- Pilih Satuan --</option>
                                <option value="Box" {{ old('satuan', $barang->satuan) == 'Box' ? 'selected' : '' }}>Box</option>
                                <option value="Pcs" {{ old('satuan', $barang->satuan) == 'Pcs' ? 'selected' : '' }}>Pcs</option>
                                <option value="Botol" {{ old('satuan', $barang->satuan) == 'Botol' ? 'selected' : '' }}>Botol</option>
                                <option value="Galon" {{ old('satuan', $barang->satuan) == 'Galon' ? 'selected' : '' }}>Galon</option>
                                <option value="Unit" {{ old('satuan', $barang->satuan) == 'Unit' ? 'selected' : '' }}>Unit</option>
                            </select>
                            @error('satuan')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="stok_awal" class="block text-sm font-medium text-gray-700">Jumlah Stok</label>
                            <input type="number" name="stok_awal" id="stok_awal" value="{{ old('stok_awal', $barang->stok_awal) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('jumlah_stok') border-red-500 @enderror" required min="0">
                            @error('stok_awal')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="harga" class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
                            <input type="number" name="harga" id="harga" value="{{ old('harga', $barang->harga) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('harga') border-red-500 @enderror" required min="0">
                            @error('harga')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="expired" class="block text-sm font-medium text-gray-700">Tanggal Expired</label>
                            <input type="date" name="expired" id="expired" value="{{ old('expired', $barang->expired ? \Carbon\Carbon::parse($barang->expired)->format('Y-m-d') : '') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('expired') border-red-500 @enderror">
                            @error('expired')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('status') border-red-500 @enderror" required>
                                <option value="">-- Pilih Status --</option>
                                <option value="Masuk" {{ old('status', $barang->status) == 'Masuk' ? 'selected' : '' }}>Masuk</option>
                                <option value="Keluar" {{ old('status', $barang->status) == 'Keluar' ? 'selected' : '' }}>Keluar</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan</label>
                            <textarea name="keterangan" id="keterangan" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('keterangan') border-red-500 @enderror">{{ old('keterangan', $barang->keterangan) }}</textarea>
                            @error('keterangan')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('admin.barangs.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                                Batal
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Ubah Barang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>