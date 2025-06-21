<x-app-layout>
    <x-slot name="title">Detail Penjualan</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Informasi Penjualan</h3>
                <div class="space-y-4">
                    <div>
                        <span class="font-medium text-gray-700">Barang:</span>
                        <span>{{ $penjualan->barang->nama_produk ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Jumlah Terjual:</span>
                        <span>{{ $penjualan->jumlahTerjual }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Harga Total:</span>
                        <span>Rp{{ number_format($penjualan->hargaTotal, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Waktu Terjual:</span>
                        <span>{{ \Carbon\Carbon::parse($penjualan->waktu_terjual)->format('d-m-Y') }}</span>
                    </div>
                    {{-- Jika ada field lain, tambahkan di sini --}}
                </div>
                <div class="mt-6 flex justify-end space-x-2">
                    <a href="{{ route('admin.penjualans.edit', $penjualan->id) }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-semibold">
                        Edit
                    </a>
                    <a href="{{ route('admin.penjualans.index') }}"
                       class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm font-semibold">
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>