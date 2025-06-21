<x-app-layout>
    <x-slot name="title">Edit Pengguna</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-md p-6">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="name" id="name" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            value="{{ old('name', $user->name) }}">
                        @error('name')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" name="username" id="username" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            value="{{ old('username', $user->username) }}">
                        @error('username')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password (Opsional)</label>
                        <input type="password" name="password" id="password"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <small class="text-gray-500">Kosongkan jika tidak ingin mengubah password.</small>
                        @error('password')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="roles" class="block text-sm font-medium text-gray-700">Roles</label>
                        <select name="roles" id="roles"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="Admin" {{ $user->roles == 'Admin' ? 'selected' : '' }}>Admin</option>
                            <option value="User" {{ $user->roles == 'User' ? 'selected' : '' }}>User</option>
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
</x-app-layout>