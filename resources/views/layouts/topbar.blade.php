{{-- resources/views/layouts/topbar.blade.php --}}
<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex items-center justify-between h-16 px-6">
        <!-- Page Title -->
        <div class="flex items-center">
            <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
        </div>
        
        <!-- Right Side -->
        <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <i data-lucide="bell" class="w-5 h-5"></i>
            </button>
            
            <!-- Settings -->
            <button class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                <i data-lucide="settings" class="w-5 h-5"></i>
            </button>
            
            <!-- User Menu Dropdown -->
            <div class="relative">
                <button id="userMenuButton" class="flex items-center space-x-2 p-2 text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                        <i data-lucide="user" class="w-4 h-4 text-gray-600"></i>
                    </div>
                    <span class="text-sm font-medium">{{ Auth::user()->nama_lengkap }}</span>
                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div id="userMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 hidden z-50">
                    <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i data-lucide="user" class="w-4 h-4 mr-3"></i>
                        Profile
                    </a>
                    <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i data-lucide="settings" class="w-4 h-4 mr-3"></i>
                        Pengaturan
                    </a>
                    <hr class="my-1">
                    <form action="{{ route('logout') }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i data-lucide="log-out" class="w-4 h-4 mr-3"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    // User Menu Dropdown Toggle
    document.addEventListener('DOMContentLoaded', function() {
        const userMenuButton = document.getElementById('userMenuButton');
        const userMenu = document.getElementById('userMenu');
        
        userMenuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            userMenu.classList.toggle('hidden');
        });
        
        document.addEventListener('click', function() {
            userMenu.classList.add('hidden');
        });
        
        userMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
</script>