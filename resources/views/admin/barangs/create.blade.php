<x-app-layout>
  <x-slot name="title">Tambah Barang</x-slot>

  <x-slot name="header">
    <h2 class="text-xl font-semibold leading-tight text-gray-800">
      {{ __('Tambah Barang') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6">
          <form action="{{ route('admin.barangs.store') }}" method="POST">
            @csrf

            <div class="mb-4">
              <label for="nomor_produk_katalog" class="block font-medium text-gray-700">Nomor Produk Katalog</label>
              <input type="text" name="nomor_produk_katalog" id="nomor_produk_katalog" value="{{ old('nomor_produk_katalog') }}"
                     class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
            </div>

            <div class="mb-4">
              <label for="nama_produk" class="block font-medium text-gray-700">Nama Produk</label>
              <input type="text" name="nama_produk" id="nama_produk" value="{{ old('nama_produk') }}"
                     class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
            </div>

            <div class="mb-4">
              <label for="satuan" class="block font-medium text-gray-700">Satuan</label>
              <select name="satuan" id="satuan" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                <option value="">-- Pilih Satuan --</option>
                <option value="Box" {{ old('satuan') == 'Box' ? 'selected' : '' }}>Box</option>
                <option value="Pcs" {{ old('satuan') == 'Pcs' ? 'selected' : '' }}>Pcs</option>
                <option value="Botol" {{ old('satuan') == 'Botol' ? 'selected' : '' }}>Botol</option>
                <option value="Galon" {{ old('satuan') == 'Galon' ? 'selected' : '' }}>Galon</option>
                <option value="Unit" {{ old('satuan') == 'Unit' ? 'selected' : '' }}>Unit</option>
              </select>
            </div>

            <div class="mb-4">
              <label for="stok_awal" class="block font-medium text-gray-700">Jumlah Stok</label>
              <input type="number" name="stok_awal" id="stok_awal" value="{{ old('stok_awal') }}"
                     class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
            </div>

            <div class="mb-4">
              <label for="harga" class="block font-medium text-gray-700">Harga (Rp)</label>
              <input type="number" name="harga" id="harga" value="{{ old('harga') }}"
                     class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
            </div>

            <div class="mb-4">
              <label for="expired" class="block font-medium text-gray-700">Tanggal Expired</label>
              <input type="date" name="expired" id="expired" value="{{ old('expired') }}"
                     class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
            </div>

            <div class="mb-4">
              <label for="status" class="block font-medium text-gray-700">Status</label>
              <select name="status" id="status" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                <option value="">-- Pilih Status --</option>
                <option value="Masuk" {{ old('status') == 'Masuk' ? 'selected' : '' }}>Masuk</option>
                <option value="Keluar" {{ old('status') == 'Keluar' ? 'selected' : '' }}>Keluar</option>
              </select>
            </div>

            <div class="mb-4">
              <label for="keterangan" class="block font-medium text-gray-700">Keterangan</label>
              <textarea name="keterangan" id="keterangan"
                        class="w-full mt-1 border-gray-300 rounded-md shadow-sm">{{ old('keterangan') }}</textarea>
            </div>

            <div class="flex justify-end">
              <a href="{{ route('admin.barangs.index') }}"
                 class="px-4 py-2 mr-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300">Batal</a>
              <button type="submit"
                      class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">Simpan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>