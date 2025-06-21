<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <a href="{{ url('/') }}" class="app-logo-container">
                <img src="{{ asset('images/logo-bsm.png') }}" alt="Logo BSM" class="app-logo">
            </a>
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

            <div class="input-group">
                <span class="icon"><i class="fa fa-user"></i></span>
                <input type="text" name="username" id="username" placeholder="Username" value="{{ old('username') }}" required autofocus autocomplete="username">
            </div>

            <div class="input-group">
                <span class="icon"><i class="fa fa-lock"></i></span>
                <input type="password" name="password" id="password" placeholder="Password" required autocomplete="current-password">
            </div>

            {{-- Remember Me --}}
            <div class="remember-me-container">
                <input type="checkbox" id="remember_me" name="remember">
                <label for="remember_me">Ingat Saya</label>
            </div>

            <div class="form-actions">
                <button type="submit" class="login-btn">
                    Login
                </button>
            </div>

            {{-- Opsional: Link Lupa Password dan Register --}}
            @if (Route::has('password.request'))
                <div class="link-item">
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                </div>
            @endif
        </form>
    </x-authentication-card>
</x-guest-layout>

{{-- PENTING: Untuk praktik terbaik, pindahkan semua gaya ini ke file CSS terpisah
     (misalnya, resources/css/app.css) dan pastikan itu di-compile oleh Laravel Mix/Vite.
     Ini hanya untuk demo cepat. --}}
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Font lebih modern */
        background: linear-gradient to right, #e0f2f7, #c9e6f2); /* Gradien background */
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh; /* Pastikan mengambil seluruh tinggi viewport */
        margin: 0;
    }

    /* Mengatur ulang beberapa default Jetstream/Tailwind agar tidak mengganggu */
    .min-h-screen {
        min-height: auto !important;
    }
    .flex-col.sm\\:justify-center.items-center.pt-6.sm\\:pt-0 {
        /* Hapus atau atur ulang properti flex/padding yang mungkin ditambahkan Jetstream */
        display: block !important; /* Agar tidak flex defaultnya Jetstream */
        padding-top: 0 !important;
    }

    /* CONTAINER KARTU AUTHENTICATION (x-authentication-card) */
    .dark\\:bg-gray-800 { /* Ini kelas Jetstream/Tailwind untuk background card. Hapus jika tidak ingin dark mode. */
        background-color: #ffffff; /* Atur background card jadi putih */
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); /* Shadow yang lebih halus */
        border-radius: 12px;
        padding: 30px 40px; /* Padding lebih proporsional */
        max-width: 450px; /* Lebar maksimal card */
        width: 100%;
        box-sizing: border-box; /* Pastikan padding tidak menambah lebar */
    }

    /* LOGO */
    .app-logo-container {
        display: block; /* Agar bisa di-margin auto */
        text-align: center; /* Untuk memposisikan gambar di tengah */
        margin-bottom: 25px; /* Ruang di bawah logo */
    }

    .app-logo {
        width: 80px;  /* Ukuran logo yang lebih proporsional */
        height: 80px; /* Ukuran logo yang lebih proporsional */
        object-fit: contain; /* Jaga rasio aspek */
        border-radius: 50%; /* Membuat logo bulat jika gambar memungkinkan */
        border: 2px solid #e0e0e0; /* Bingkai tipis */
        padding: 5px; /* Padding di dalam bingkai */
    }

    /* JUDUL LOGIN */
    .login-title {
        margin-bottom: 25px;
        color: #333;
        font-size: 2.2em;
        text-align: center;
        font-weight: 600; /* Sedikit lebih tebal */
    }

    /* INPUT GROUP (gabungan icon dan input) */
    .input-group {
        position: relative;
        width: 100%;
        margin-bottom: 20px;
    }

    .input-group .icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #888;
        font-size: 1.1em;
    }

    .input-group input {
        width: calc(100% - 50px); /* Menyesuaikan lebar input dengan ikon */
        padding: 14px 14px 14px 45px; /* Padding agar teks tidak tertutup ikon */
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1em;
        outline: none;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        box-sizing: border-box; /* Penting untuk perhitungan lebar */
    }

    .input-group input:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
    }

    /* REMEMBER ME CHECKBOX */
    .remember-me-container {
        text-align: left;
        margin-top: 10px;
        margin-bottom: 25px; /* Lebih banyak ruang */
        font-size: 0.95em;
        display: flex;
        align-items: center;
        color: #555;
    }

    .remember-me-container input[type="checkbox"] {
        margin-right: 10px;
        transform: scale(1.1); /* Sedikit membesarkan checkbox */
        accent-color: #007bff; /* Warna checkbox (beberapa browser support) */
    }

    .remember-me-container label {
        cursor: pointer;
    }

    /* TOMBOL LOGIN */
    .form-actions {
        display: flex;
        justify-content: center; /* Tombol di tengah */
        margin-top: 20px;
    }

    .login-btn {
        width: 180px; /* Lebar tombol lebih besar */
        padding: 14px 25px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.15em;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.3s ease, transform 0.2s ease;
        letter-spacing: 0.5px; /* Spasi huruf */
    }

    .login-btn:hover {
        background: #0056b3;
        transform: translateY(-2px); /* Efek sedikit naik saat hover */
    }

    .login-btn:active {
        transform: translateY(0);
    }

    /* LINK BAWAH (Lupa Password) */
    .link-item {
        margin-top: 20px; /* Ruang di bawah tombol */
        text-align: center;
        font-size: 0.9em;
    }

    .link-item a {
        color: #007bff;
        text-decoration: none; /* Hilangkan underline default */
        transition: color 0.3s ease;
    }

    .link-item a:hover {
        color: #0056b3;
        text-decoration: underline; /* Munculkan underline saat hover */
    }

    /* OVERRIDE Tailwind/Jetstream Validation Errors */
    .text-red-500 {
        color: #dc3545 !important;
        font-size: 0.9em;
        margin-bottom: 15px; /* Ruang bawah pesan error */
        text-align: center;
    }
    .text-green-600 {
        color: #28a745 !important;
        font-size: 0.9em;
        margin-bottom: 15px;
        text-align: center;
    }

    /* Tambahan untuk responsivitas dasar (opsional) */
    @media (max-width: 600px) {
        .dark\\:bg-gray-800 {
            padding: 25px 25px;
            margin: 20px; /* Agar ada jarak dari tepi layar */
        }
        .login-title {
            font-size: 1.8em;
        }
        .login-btn {
            width: 100%;
        }
    }
</style>