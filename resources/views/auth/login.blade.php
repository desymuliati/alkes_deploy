<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <a href="{{ url('/') }}" class="app-logo-container">
                <img src="{{ asset('images/logo-bsm.png') }}" alt="Logo BSM" class="app-logo">
            </a>
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

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

            <div class="remember-me-container">
                <input type="checkbox" id="remember_me" name="remember">
                <label for="remember_me">Ingat Saya</label>
            </div>

            <div class="form-actions">
                <button type="submit" class="login-btn">Login</button>
            </div>

            @if (Route::has('password.request'))
                <div class="link-item">
                    <a href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                </div>
            @endif
        </form>
    </x-authentication-card>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #e0f2f7, #c9e6f2); /* fix syntax */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .min-h-screen {
            min-height: auto !important;
        }

        .flex-col.sm\:justify-center.items-center.pt-6.sm\:pt-0 {
            display: block !important;
            padding-top: 0 !important;
        }

        .dark\:bg-gray-800 {
            background-color: #ffffff;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 30px 40px;
            max-width: 450px;
            width: 100%;
            box-sizing: border-box;
        }

        .app-logo-container {
            text-align: center;
            margin-bottom: 25px;
        }

        .app-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 50%;
            border: 2px solid #e0e0e0;
            padding: 5px;
        }

        .login-title {
            margin-bottom: 25px;
            color: #333;
            font-size: 2.2em;
            text-align: center;
            font-weight: 600;
        }

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
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            box-sizing: border-box;
        }

        .input-group input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
        }

        .remember-me-container {
            margin: 10px 0 25px;
            font-size: 0.95em;
            display: flex;
            align-items: center;
            color: #555;
        }

        .remember-me-container input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.1);
            accent-color: #007bff;
        }

        .form-actions {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .login-btn {
            width: 180px;
            padding: 14px 25px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.15em;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
            letter-spacing: 0.5px;
        }

        .login-btn:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .link-item {
            margin-top: 20px;
            text-align: center;
            font-size: 0.9em;
        }

        .link-item a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .link-item a:hover {
            color: #0056b3;
            text-decoration: underline;
        }

        .text-red-500 {
            color: #dc3545 !important;
            font-size: 0.9em;
            margin-bottom: 15px;
            text-align: center;
        }

        .text-green-600 {
            color: #28a745 !important;
            font-size: 0.9em;
            margin-bottom: 15px;
            text-align: center;
        }

        @media (max-width: 600px) {
            .dark\:bg-gray-800 {
                padding: 25px;
                margin: 20px;
            }

            .login-title {
                font-size: 1.8em;
            }

            .login-btn {
                width: 100%;
            }
        }
    </style>
</x-guest-layout>