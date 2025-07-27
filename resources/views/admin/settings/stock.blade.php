<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Stok') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <h1 class="text-2xl font-bold mb-6 text-gray-800">Pengaturan Stok</h1>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Berhasil!</strong>
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">Error!</strong>
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <form action="{{ route('admin.stock_settings.update') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Ambang Batas Stok Default -->
                    <div>
                        <label for="default_threshold" class="block text-sm font-medium text-gray-700 mb-1">Ambang Batas Stok Default:</label>
                        <input type="number" id="default_threshold" name="default_threshold"
                               value="{{ $settings['default_threshold'] ?? 100 }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        @error('default_threshold')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <h3 class="text-xl font-semibold mt-8 mb-4 text-gray-800">Ambang Batas Berdasarkan Satuan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($settings['unit_thresholds'] as $unit => $threshold)
                            <div>
                                <label for="unit_{{ $unit }}" class="block text-sm font-medium text-gray-700 mb-1">{{ ucfirst($unit) }}:</label>
                                <input type="number" id="unit_{{ $unit }}" name="unit_thresholds[{{ $unit }}]"
                                       value="{{ $threshold }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('unit_thresholds.' . $unit)
                                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    <!-- Tombol Simpan -->
                    <div class="flex justify-end mt-8">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>