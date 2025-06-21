<x-front-layout>
    <!-- Logo (Klik kembali ke landing) -->
    <a href="{{ url('/') }}"
       class="fixed top-[60px] left-[60px] w-[100px] h-[100px] rounded-full bg-blue-600 shadow-lg z-50 flex items-center justify-center cursor-pointer">
        <img src="{{ asset('images/logo-bsm.png') }}" alt="Logo" class="w-full h-auto rounded-full">
    </a>

    <!-- Sidebar Toggle Icon -->
    <div class="fixed top-5 right-5 z-[300] cursor-pointer" onclick="toggleMenu()">
        <div class="bg-black h-[5px] w-[35px] my-[6px]"></div>
        <div class="bg-black h-[5px] w-[35px] my-[6px]"></div>
        <div class="bg-black h-[5px] w-[35px] my-[6px]"></div>
    </div>

    <!-- Sidebar -->
    <div id="sidebar"
         class="fixed top-[50px] right-0 w-[200px] h-screen bg-gray-800 text-white transform translate-x-full transition-transform duration-300 z-[200] flex flex-col pt-5">
        <a href="{{ url('/') }}"
           class="px-4 py-3 hover:bg-blue-600 border-l-4 border-transparent hover:border-white transition">
            <i class="fa fa-home mr-2"></i> Dashboard
        </a>
        <a href="{{ route('login') }}"
           class="px-4 py-3 hover:bg-blue-600 border-l-4 border-transparent hover:border-white transition">
            <i class="fa fa-sign-in mr-2"></i> Login
        </a>
    </div>

    <!-- Sidebar Overlay -->
    <div id="sidebarOverlay"
         class="fixed inset-0 bg-black bg-opacity-50 hidden z-[100]"
         onclick="toggleMenu()"></div>

    <!-- Header Section -->
    <section class="text-center mt-[200px] px-4">
        <h2 class="text-3xl font-bold text-gray-800 mb-3">
            Selamat datang di Sistem Manajemen Barang
        </h2>
        <h3 class="text-xl text-gray-600">
            PT. Borneo Sejahtera Medika
        </h3>
        <p class="mt-4">
            Silakan <a href="{{ route('login') }}" class="text-blue-600 underline hover:text-blue-800">login</a> untuk melanjutkan
        </p>
    </section>

    <!-- Gambar Stock -->
    <div class="relative w-full mt-10">
        <img src="{{ asset('images/stock.jpg') }}"
             alt="Stock Image"
             class="absolute right-[10%] top-[70%] transform -translate-y-1/2 w-[400px] h-[250px] z-50 rounded shadow-md">
    </div>

    <!-- Footer -->
    <footer class="text-center mt-[310px] py-6 border-t text-sm text-gray-500">
        Copyright © 2025 PT. Borneo Sejahtera Medika
    </footer>

    <!-- Scripts -->
    @push('scripts')
    <script>
        function toggleMenu() {
            const sidebar = document.getElementById("sidebar");
            const overlay = document.getElementById("sidebarOverlay");
            sidebar.classList.toggle("translate-x-full");
            overlay.classList.toggle("hidden");
        }
    </script>
    @endpush
</x-front-layout>