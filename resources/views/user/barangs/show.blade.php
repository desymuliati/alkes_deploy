<x-app-layout>
    <x-slot name="title">Detail Barang: {{ $barang->nama_produk }}</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Barang') }} - {{ $barang->nama_produk }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Barang</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Kode Barang:</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $barang->kode_barang }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nomor Produk Katalog:</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $barang->nomor_produk_katalog }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Nama Produk:</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $barang->nama_produk }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Satuan:</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $barang->satuan }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Jumlah Stok:</p>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $barang->jumlah_stok }}
                                @if ($barang->jumlah_stok < 100)
                                    <span class="text-red-600 font-bold ml-2">(Stok Rendah!)</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Harga:</p>
                            <p class="mt-1 text-sm text-gray-900">Rp {{ number_format($barang->harga, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Expired Date:</p>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($barang->expired)
                                    @php
                                        $expiredDate = \Carbon\Carbon::parse($barang->expired);
                                    @endphp
                                    {{ $expiredDate->format('d F Y') }}
                                    @if($expiredDate->isPast())
                                        <span class="text-red-600 font-bold ml-2">(Kadaluarsa)</span>
                                    @elseif($expiredDate->year === \Carbon\Carbon::now()->year)
                                        <span class="text-orange-500 ml-2">(Mendekati Kadaluarsa Tahun Ini)</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Status:</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $barang->status }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">Keterangan:</p>
                            <p class="mt-1 text-sm text-gray-900">{{ $barang->keterangan ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('user.barangs.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>