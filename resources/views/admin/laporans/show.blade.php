<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Transaksi Retur') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Informasi Retur</h3>
                        <dl class="mt-2 text-gray-700">
                            <div class="flex items-center py-2">
                                <dt class="w-1/3 font-medium">ID Retur:</dt>
                                <dd class="w-2/3">{{ $laporan->id }}</dd>
                            </div>
                            <div class="flex items-center py-2 border-t border-gray-200">
                                <dt class="w-1/3 font-medium">ID Penjualan Asal:</dt>
                                <dd class="w-2/3">
                                    @if($laporan->penjualan)
                                        {{-- Pastikan route ini ada, misalnya: admin.penjualans.show --}}
                                        <a href="{{ route('admin.penjualans.show', $laporan->penjualan->id) }}" class="text-blue-600 hover:underline">
                                            #{{ $laporan->penjualan->id }}
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </dd>
                            </div>
                            <div class="flex items-center py-2 border-t border-gray-200">
                                <dt class="w-1/3 font-medium">Barang Diretur:</dt>
                                <dd class="w-2/3">{{ $laporan->barang->nama_produk ?? 'Barang Tidak Ditemukan' }}</dd>
                            </div>
                            <div class="flex items-center py-2 border-t border-gray-200">
                                <dt class="w-1/3 font-medium">Jumlah Diretur:</dt>
                                <dd class="w-2/3">{{ $laporan->jumlah_retur }}</dd>
                            </div>
                            <div class="flex items-center py-2 border-t border-gray-200">
                                <dt class="w-1/3 font-medium">Nilai Retur:</dt>
                                {{-- Menggunakan accessor nilai_retur --}}
                                <dd class="w-2/3">Rp{{ number_format($laporan->nilai_retur, 0, ',', '.') }}</dd>
                            </div>
                            <div class="flex items-center py-2 border-t border-gray-200">
                                <dt class="w-1/3 font-medium">Tanggal Retur:</dt>
                                <dd class="w-2/3">{{ \Carbon\Carbon::parse($laporan->tanggal_retur)->format('d M Y') }}</dd>
                            </div>
                            <div class="flex items-center py-2 border-t border-gray-200">
                                <dt class="w-1/3 font-medium">Dibuat Pada:</dt>
                                <dd class="w-2/3">{{ \Carbon\Carbon::parse($laporan->created_at)->format('d M Y H:i') }}</dd>
                            </div>
                            <div class="flex items-center py-2 border-t border-gray-200">
                                <dt class="w-1/3 font-medium">Diperbarui Pada:</dt>
                                <dd class="w-2/3">{{ \Carbon\Carbon::parse($laporan->updated_at)->format('d M Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('admin.laporans.edit', $laporan->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Edit Retur') }}
                        </a>

                        <form action="{{ route('admin.laporans.destroy', $laporan->id) }}" method="POST" class="inline-block ml-3" onsubmit="return confirm('Anda yakin ingin menghapus transaksi retur ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Hapus Retur') }}
                            </button>
                        </form>

                        <a href="{{ route('admin.laporans.index') }}" class="ml-4 inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-600 focus:outline-none focus:border-gray-600 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            {{ __('Kembali ke Daftar Retur') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>