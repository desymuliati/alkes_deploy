<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            {{-- Anda bisa memilih salah satu logo di bawah ini, atau menggabungkan keduanya jika perlu.
                 Jika Anda ingin hanya logo kustom, hapus <x-authentication-card-logo />. --}}
            <a href="{{ url('/') }}" class="logo-box">
                <img src="{{ asset('images/logo-bsm.png') }}" alt="Logo BSM" class="w-20 h-20"> {{-- Tambahkan kelas Tailwind untuk ukuran --}}
            </a>
            {{-- <x-authentication-card-logo /> --}} {{-- Ini adalah logo default Jetstream/Breeze, uncomment jika ingin tetap menggunakannya --}}
        </x-slot>

        {{-- Bagian ini akan menampilkan error validasi dari Laravel secara otomatis --}}
        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        {{-- Form Login --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <h2 class="login-title">Login</h2>

            <div class="input-box">
                <i class="fa fa-user"></i> {{-- Mengganti ikon email menjadi user untuk username --}}
                <input type="text" name="username" id="username" placeholder="Username" value="{{ old('username') }}" required autofocus autocomplete="username">
            </div>

            <div class="input-box">
                <i class="fa fa-lock"></i>
                <input type="password" name="password" id="password" placeholder="Password" required autocomplete="current-password">
            </div>

            {{-- Remember Me --}}
            <div class="remember-me-container">
                <input type="checkbox" id="remember_me" name="remember">
                <label for="remember_me">Ingat Saya</label>
            </div>

            <div class="flex-center-col"> {{-- Menggunakan kelas kustom untuk styling fleksibel --}}
                <button type="submit" class="login-btn">
                    Login
                </button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>

{{-- Penting: Styling ini akan menimpa styling Tailwind Jetstream.
     Sebaiknya, Anda mengintegrasikan gaya ini ke dalam file CSS atau Tailwind config Anda.
     Namun, untuk demonstrasi cepat, saya akan letakkan di sini. --}}
<style>
    body {
        font-family: 'Garamond', serif;
        background: #f4f4f4;
    }

    /* Mengatur ulang beberapa default Jetstream */
    .min-h-screen {
        min-height: auto !important; /* Agar tidak mengambil seluruh tinggi layar */
    }

    /* Styles untuk form login kustom */
    .login-title {
        margin-bottom: 30px;
        color: #333;
        font-size: 2em;
        text-align: center;
    }

    .input-box {
        position: relative;
        width: 100%;
        margin-bottom: 20px;
    }

    .input-box i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #888;
        z-index: 10;
    }

    .input-box input {
        width: calc(100% - 50px); /* Menyesuaikan lebar input dengan ikon */
        padding: 12px 12px 12px 40px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1em;
        outline: none;
        transition: border-color 0.3s;
    }

    .input-box input:focus {
        border-color: #007bff !important;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25) !important;
    }

    .remember-me-container {
        text-align: left;
        margin-top: 15px;
        margin-bottom: 15px;
        font-size: 0.9em;
        display: flex;
        align-items: center;
    }

    .remember-me-container input[type="checkbox"] {
        margin-right: 8px;
        transform: scale(1.2);
    }

    .remember-me-container label {
        color: #666;
        cursor: pointer;
    }

    .login-btn {
        width: 150px;
        height: 50px;
        padding: 10px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.1em;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .login-btn:hover {
        background: #0056b3;
    }

    .flex-center-col {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin-top: 20px;
    }

    .link-item {
        margin-top: 10px;
        font-size: 0.9em;
    }

    .link-item a {
        color: #007bff;
        text-decoration: underline;
    }

    .link-item a:hover {
        color: #0056b3;
    }

    /* Overriding Jetstream's default validation error text color */
    .text-red-500 {
        color: #dc3545 !important;
    }
    .text-green-600 { /* Untuk pesan sukses session */
        color: #28a745 !important;
    }
</style>