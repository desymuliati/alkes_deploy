<x-app-layout>
    <x-slot name="title">Tambah Pengguna</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-md p-6">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="name" id="name" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            value="{{ old('name') }}">
                        @error('name')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" name="username" id="username" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            value="{{ old('username') }}">
                        @error('username')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email Input Field --}}
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            value="{{ old('email') }}">
                        @error('email')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" id="password" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @error('password')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="roles" class="block text-sm font-medium text-gray-700">Roles</label>
                        <select name="roles" id="roles"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="ADMIN" {{ old('roles') == 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                            <option value="USER" {{ old('roles') == 'USER' ? 'selected' : '' }}>USER</option>
                        </select>
                        @error('roles')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('admin.users.index') }}"
                            class="mr-2 bg-gray-500 text-white px-4 py-2 rounded shadow hover:bg-gray-700">Batal</a>
                        <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-800">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Basic Alpine.js Tabs Example (if applicable) --}}
    {{-- This section is an example of how you might structure tabs to prevent errors --}}
    <div x-data="{ activeTab: 'tab1' }" class="mt-8">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="activeTab = 'tab1'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'tab1', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'tab1' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Tab 1
                </button>
                <button @click="activeTab = 'tab2'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'tab2', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'tab2' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Tab 2
                </button>
            </nav>
        </div>

        <div x-show="activeTab === 'tab1'" class="pt-6">
            Konten untuk Tab 1.
        </div>

        <div x-show="activeTab === 'tab2'" class="pt-6">
            Konten untuk Tab 2.
        </div>
    </div>
    {{-- End of Alpine.js Tabs Example --}}

</x-app-layout>