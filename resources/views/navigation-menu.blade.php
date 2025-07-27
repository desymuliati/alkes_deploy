<nav class="bg-white border-b border-gray-100 fixed w-full z-20 top-0 left-0 shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
        <div class="flex items-center space-x-4">
            <div class="cursor-pointer p-2" onclick="toggleSidebar()">
                <div class="w-6 h-0.5 bg-gray-700 mb-1"></div>
                <div class="w-6 h-0.5 bg-gray-700 mb-1"></div>
                <div class="w-6 h-0.5 bg-gray-700"></div>
            </div>
            {{-- Link Logo dinamis berdasarkan peran pengguna --}}
            @auth
                @if (Auth::user()->roles === 'ADMIN') {{-- Cek langsung kolom 'roles' --}}
                    <a href="{{ route('admin.dashboard') }}">
                @else
                    <a href="{{ route('user.dashboard') }}">
                @endif
            @else
                <a href="/"> {{-- Rute default jika belum login --}}
            @endauth
                <img src="{{ asset('images/logo-bsm.png') }}" alt="Logo" class="h-9 w-auto rounded-full shadow" />
            </a>
        </div>
        <div class="relative ml-4">
            <button onclick="toggleUserMenu()" class="flex items-center space-x-2 focus:outline-none">
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xl uppercase text-white
                    @auth
                        @if (Auth::user()->roles === 'ADMIN') {{-- Cek langsung kolom 'roles' --}}
                            bg-red-600
                        @else
                            bg-green-600
                        @endif
                    @else
                        bg-gray-500 {{-- Warna default jika belum login --}}
                    @endauth
                ">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white border rounded shadow z-50">
                <div class="px-4 py-2 text-sm text-gray-600">
                    @auth
                        {{ Auth::user()->name }}
                        @if (Auth::user()->roles === 'ADMIN') {{-- Cek langsung kolom 'roles' --}}
                            (Admin)
                        @else
                            (User)
                        @endif
                    @else
                        Tamu
                    @endauth
                </div>
                <a href="{{ route('profile.show') }}" class="block px-4 py-2 hover:bg-gray-100">Profil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left px-4 py-2 hover:bg-gray-100" type="submit">Keluar</button>
                </form>
            </div>
        </div>
    </div>
</nav>

<div id="sidebarOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-40" onclick="toggleSidebar()"></div>

<div id="sidebar" class="fixed left-0 h-full w-64 bg-gray-800 text-white transform -translate-x-full transition-transform duration-300 z-50">
    <div class="flex items-center justify-between p-4 h-16 bg-gray-900">
        {{-- Link Logo di sidebar dinamis berdasarkan peran pengguna --}}
        @auth
            @if (Auth::user()->roles === 'ADMIN') {{-- Cek langsung kolom 'roles' --}}
                <a href="{{ route('admin.dashboard') }}">
            @else
                <a href="{{ route('user.dashboard') }}">
            @endif
        @else
            <a href="/"> {{-- Rute default jika belum login --}}
        @endauth
            <img src="{{ asset('images/logo-bsm.png') }}" alt="Logo" class="h-9 w-auto rounded-full shadow" />
        </a>
        <div class="cursor-pointer p-2" onclick="toggleSidebar()">
            <div class="w-6 h-0.5 bg-white mb-1"></div>
            <div class="w-6 h-0.5 bg-white mb-1"></div>
            <div class="w-6 h-0.5 bg-white"></div>
        </div>
    </div>

    <div class="p-4 space-y-2">
        @php $currentRoute = Route::currentRouteName(); @endphp
        @auth {{-- Pastikan pengguna login --}}
            @if (Auth::user()->roles === 'ADMIN') {{-- Cek langsung kolom 'roles' --}}
                {{-- Tautan untuk Admin --}}
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 hover:bg-gray-700 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-white' : '' }}" onclick="toggleSidebar()">
                    <i class="fa fa-tachometer-alt mr-2"></i>Dashboard Admin
                </a>
                <a href="{{ route('admin.barangs.index') }}" class="block px-4 py-2 hover:bg-gray-700 rounded {{ request()->routeIs('admin.barangs.index') ? 'bg-gray-700 text-white' : '' }}" onclick="toggleSidebar()">
                    <i class="fa fa-box-open mr-2"></i>List Stock
                </a>
                <a href="{{ route('admin.penjualans.index') }}" class="block px-4 py-2 hover:bg-gray-700 rounded {{ request()->routeIs('admin.penjualans.index') ? 'bg-gray-700 text-white' : '' }}" onclick="toggleSidebar()">
                    <i class="fa fa-shopping-cart mr-2"></i>Penjualan
                </a>
                <a href="{{ route('admin.laporans.index') }}" class="block px-4 py-2 hover:bg-gray-700 rounded {{ request()->routeIs('admin.laporans.index') ? 'bg-gray-700 text-white' : '' }}" onclick="toggleSidebar()">
                    <i class="fa fa-file-alt mr-2"></i>Laporan
                </a>
                <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 hover:bg-gray-700 rounded {{ request()->routeIs('admin.users.index') ? 'bg-gray-700 text-white' : '' }}" onclick="toggleSidebar()">
                    <i class="fa fa-users mr-2"></i>Kelola Pengguna
                </a>
                {{-- DITAMBAHKAN: Link untuk Pengaturan Stok --}}
                <a href="{{ route('admin.stock_settings.index') }}" class="block px-4 py-2 hover:bg-gray-700 rounded {{ request()->routeIs('admin.stock_settings.index') ? 'bg-gray-700 text-white' : '' }}" onclick="toggleSidebar()">
                    <i class="fa fa-cog mr-2"></i>Pengaturan Stok
                </a>
            @else
                {{-- Tautan untuk User Biasa --}}
                <a href="{{ route('user.dashboard') }}" class="block px-4 py-2 hover:bg-gray-700 rounded {{ request()->routeIs('user.dashboard') ? 'bg-gray-700 text-white' : '' }}" onclick="toggleSidebar()">
                    <i class="fa fa-tachometer-alt mr-2"></i>Dashboard User
                </a>
                <a href="{{ route('user.barangs.index') }}" class="block px-4 py-2 hover:bg-gray-700 rounded {{ request()->routeIs('user.barangs.index') ? 'bg-gray-700 text-white' : '' }}" onclick="toggleSidebar()">
                    <i class="fa fa-box-open mr-2"></i>List Stock
                </a>
                {{-- Jika ada halaman lain untuk user, tambahkan di sini --}}
            @endif
        @endauth
    </div>
</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
        overlay.classList.toggle('block');
    }

    function toggleUserMenu() {
        const menu = document.getElementById('userDropdown');
        menu.classList.toggle('hidden');
    }

    document.addEventListener('click', function(e) {
        const userMenuButton = document.querySelector('button[onclick="toggleUserMenu()"]');
        const userDropdown = document.getElementById('userDropdown');
        if (userDropdown && userMenuButton) {
            if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }
        }

        const sidebar = document.getElementById('sidebar');
        const toggleButtonNav = document.querySelector('nav .cursor-pointer[onclick="toggleSidebar()"]');
        const toggleButtonSidebar = document.querySelector('#sidebar > div > .cursor-pointer[onclick="toggleSidebar()"]');

        if (sidebar) {
            if (!sidebar.classList.contains('-translate-x-full') &&
                (!toggleButtonNav || !toggleButtonNav.contains(e.target)) &&
                (!toggleButtonSidebar || !toggleButtonSidebar.contains(e.target)) &&
                !sidebar.contains(e.target) &&
                !e.target.closest('a[onclick="toggleSidebar()"]')
            ) {
                toggleSidebar();
            }
        }
    });
</script>