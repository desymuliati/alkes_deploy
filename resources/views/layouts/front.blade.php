<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen Barang - PT. Borneo Sejahtera Medika</title>

    {{-- Font Awesome untuk ikon (Dashboard, Login) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLPD7yXq6tp/KihR/uqVjpGbM+RofLzpsaK3/fHOK8/OQf1JTSY33W3Xz/5x0e3z/3s045781a704e6c0c2a==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        /* CSS Umum */
        body {
            margin: 0;
            font-family: 'Garamond', serif; /* Menggunakan Garamond atau fallback sans-serif */
            line-height: 1.6;
            color: #333;
            overflow-x: hidden; /* Mencegah scroll horizontal yang tidak diinginkan */
        }

        a {
            text-decoration: none;
            color: inherit; /* Mengambil warna teks dari parent */
        }

        /* Logo Box */
        .logo-box {
            display: flex; /* Untuk menengahkan gambar di dalam */
            align-items: center;
            justify-content: center;
            position: fixed; /* Fixed agar selalu terlihat saat di-scroll */
            top: 40px; /* Jarak dari atas */
            left: 40px; /* Jarak dari kiri */
            width: 80px; /* Ukuran lingkaran logo */
            height: 80px; /* Ukuran lingkaran logo */
            border-radius: 50%;
            background-color: #007bff; /* Warna biru konsisten dengan sidebar hover */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Bayangan yang lebih halus */
            z-index: 50; /* Pastikan di atas konten lain */
            overflow: hidden; /* Pastikan gambar tidak keluar dari lingkaran */
        }

        .logo-box img {
            width: 100%; /* Gambar mengisi penuh area logo box */
            height: 100%;
            object-fit: cover; /* Pastikan gambar proporsional dan mengisi area */
            border-radius: 50%; /* Agar gambar logo juga ikut melingkar */
        }

        /* Sidebar Toggle Icon (Hamburger) */
        .sidebar-toggle-icon {
            position: fixed; /* Fixed agar selalu terlihat saat di-scroll */
            top: 30px; /* Jarak dari atas */
            right: 40px; /* Jarak dari kanan */
            cursor: pointer;
            z-index: 300; /* Pastikan di atas sidebar */
        }

        .sidebar-toggle-icon div {
            background-color: #333; /* Warna hitam untuk garis */
            height: 4px;
            width: 30px;
            margin: 5px 0;
            transition: 0.3s; /* Animasi transisi */
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0; /* Mulai dari paling atas */
            right: 0;
            width: 250px; /* Lebar sidebar */
            height: 100%; /* Full height */
            background-color: #222; /* Warna latar belakang lebih gelap */
            color: white;
            transform: translateX(100%); /* Sembunyikan ke kanan */
            transition: transform 0.3s ease-in-out; /* Animasi slide */
            z-index: 200;
            padding-top: 100px; /* Ruang dari atas agar tidak nabrak ikon */
            display: flex;
            flex-direction: column;
        }

        .sidebar.active {
            transform: translateX(0); /* Tampilkan sidebar */
        }

        .sidebar a {
            padding: 15px 25px; /* Padding lebih proporsional */
            text-decoration: none;
            display: block;
            color: white;
            border-left: 5px solid transparent; /* Border lebih tebal */
            transition: background-color 0.3s, border-left-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #007bff; /* Warna hover biru */
            border-left-color: white;
        }

        .sidebar a i {
            margin-right: 10px; /* Jarak antara ikon dan teks */
        }

        /* Sidebar Overlay */
        .sidebar-overlay {
            position: fixed;
            inset: 0; /* top:0; right:0; bottom:0; left:0; */
            background-color: rgba(0, 0, 0, 0.6); /* Lebih gelap */
            display: none;
            z-index: 100;
            transition: opacity 0.3s ease-in-out;
            opacity: 0; /* Mulai dengan transparan */
        }

        .sidebar-overlay.active {
            display: block;
            opacity: 1; /* Tampilkan overlay */
        }

        /* Main Content Section (Header & Text) */
        .main-content {
            text-align: center;
            padding: 20px;
            margin-top: 150px; /* Jarak dari atas agar tidak tertutup logo/hamburger */
            max-width: 900px; /* Batasi lebar konten */
            margin-left: auto;
            margin-right: auto;
        }

        .main-content h2 {
            font-size: 2.5em; /* Ukuran font lebih besar */
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .main-content h3 {
            font-size: 1.5em;
            color: #7f8c8d;
            margin-bottom: 20px;
        }

        .main-content p {
            font-size: 1.1em;
            color: #34495e;
        }

        .main-content a {
            color: #007bff;
            text-decoration: underline;
            transition: color 0.3s;
        }

        .main-content a:hover {
            color: #0056b3;
        }

        /* Stock Image */
        .stock-image-container {
            position: relative; /* Penting: Kontainer relatif agar gambar absolut di dalamnya bisa diposisikan */
            width: 100%;
            /* Memberikan tinggi minimum agar footer tidak naik saat gambar diposisikan absolut.
               Sesuaikan nilai ini jika konten utama Anda sangat pendek */
            min-height: 400px;
        }

        .stock-image {
            position: absolute; /* Posisikan absolut di dalam container */
            right: 8%; /* Jarak dari kanan (sesuaikan sesuai kebutuhan) */
            bottom: 50px; /* Jarak dari bawah container (sesuaikan sesuai kebutuhan) */
            max-width: 450px; /* Ukuran gambar stok */
            height: auto;
            border-radius: 8px; /* Sudut sedikit membulat */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2); /* Bayangan lebih jelas */
            z-index: 40; /* Pastikan di atas konten utama tapi di bawah sidebar/logo jika ada tumpang tindih */
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 25px;
            margin-top: 50px; /* Memberikan jarak dari elemen di atasnya */
            border-top: 1px solid #eee;
            color: #777;
            font-size: 0.9em;
            background-color: #f8f8f8; /* Sedikit latar belakang */
            position: relative; /* Agar z-index bekerja jika ada overlapping */
            z-index: 10;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .logo-box {
                width: 60px;
                height: 60px;
                top: 20px;
                left: 20px;
            }

            .sidebar-toggle-icon {
                top: 20px;
                right: 20px;
            }

            .sidebar {
                width: 200px;
                padding-top: 80px;
            }

            .main-content {
                margin-top: 100px;
                font-size: 0.9em;
            }

            .main-content h2 {
                font-size: 1.8em;
            }

            .main-content h3 {
                font-size: 1.2em;
            }

            /* Di layar kecil, gambar stok kembali ke posisi statis dan di tengah */
            .stock-image-container {
                min-height: auto; /* Reset min-height */
            }
            .stock-image {
                position: static; /* Kembali ke posisi normal */
                max-width: 90%; /* Lebih responsif di mobile */
                height: auto;
                margin: 30px auto; /* Tengahkan */
                display: block; /* Agar margin auto berfungsi */
                right: auto; /* Reset properti absolut */
                bottom: auto; /* Reset properti absolut */
            }

            footer {
                padding: 15px;
            }
        }

        @media (max-width: 480px) {
            .logo-box {
                width: 50px;
                height: 50px;
                top: 15px;
                left: 15px;
            }

            .sidebar-toggle-icon {
                top: 15px;
                right: 15px;
            }

            .sidebar {
                width: 180px;
            }
        }
    </style>
</head>
<body>
    {{-- Logo (Klik kembali ke landing page) --}}
    <a href="{{ url('/') }}" class="logo-box">
        <img src="{{ asset('images/logo-bsm.png') }}" alt="Logo BSM">
    </a>

    {{-- Sidebar Toggle Icon (Hamburger) --}}
    <div class="sidebar-toggle-icon" onclick="toggleMenu()">
        <div></div>
        <div></div>
        <div></div>
    </div>

    {{-- Sidebar --}}
    <div id="sidebar" class="sidebar">
        <a href="{{ url('/') }}"><i class="fa fa-home"></i> Dashboard</a>
        <a href="{{ route('login') }}"><i class="fa fa-sign-in"></i> Login</a>
    </div>

    {{-- Sidebar Overlay --}}
    <div id="sidebarOverlay" class="sidebar-overlay" onclick="toggleMenu()"></div>

    {{-- Header Section (Konten Utama) --}}
    <main class="main-content">
        <h2>Selamat datang di Sistem Manajemen Barang</h2>
        <h3>PT. Borneo Sejahtera Medika</h3>
        <p>Silakan <a href="{{ route('login') }}">login</a> untuk melanjutkan</p>
    </main>

    {{-- Gambar Stock --}}
    <div class="stock-image-container">
        <img src="{{ asset('images/stock.jpg') }}" alt="Stock Image" class="stock-image">
    </div>

    {{-- Footer --}}
    <footer>
        Copyright &copy; 2025 PT. Borneo Sejahtera Medika
    </footer>

    {{-- JavaScript --}}
    <script>
        function toggleMenu() {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("sidebarOverlay");

            sidebar.classList.toggle("active");
            overlay.classList.toggle("active");
        }
    </script>
</body>
</html>