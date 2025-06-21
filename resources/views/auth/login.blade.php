<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 py-8 px-4">
        <div class="w-full max-w-md bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-center mb-4">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/logo-bsm.png') }}" alt="Logo BSM" class="w-24 h-24 object-contain">
                </a>
            </div>

            <h2 class="text-2xl font-semibold text-center text-gray-800 mb-6">Login</h2>

            <x-validation-errors class="mb-4" />

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <input type="text" name="username" id="username" placeholder="Username"
                           value="{{ old('username') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-md focus:ring focus:ring-blue-200 focus:outline-none"
                           required autofocus autocomplete="username">
                </div>

                <div class="mb-4">
                    <input type="password" name="password" id="password" placeholder="Password"
                           class="w-full px-4 py-3 border border-gray-300 rounded-md focus:ring focus:ring-blue-200 focus:outline-none"
                           required autocomplete="current-password">
                </div>

                <div class="flex items-center mb-4">
                    <input type="checkbox" id="remember_me" name="remember" class="mr-2">
                    <label for="remember_me" class="text-sm text-gray-600">Ingat Saya</label>
                </div>

                <div class="flex justify-center">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md transition duration-200">
                        Login
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>