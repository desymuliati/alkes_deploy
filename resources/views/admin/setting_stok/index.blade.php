<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pengaturan Limit Stok
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                
                @if (session('success'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('setting.stok.update') }}" method="POST">
                    @csrf

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Setting Key</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Satuan</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($settings as $setting)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">{{ $setting->setting_key }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <input type="number" name="setting_value[{{ $setting->id }}]" value="{{ $setting->setting_value }}" class="form-input rounded-md shadow-sm w-full">
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">{{ $setting->unit }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">{{ $setting->description }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        <x-primary-button>
                            Simpan Perubahan
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>