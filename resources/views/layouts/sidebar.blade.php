{{-- resources/views/layouts/sidebar.blade.php --}}
<aside class="w-64 bg-white shadow-sm border-r border-gray-200">
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 px-4 border-b border-gray-200">
            <h1 class="text-xl font-bold text-gray-800">Penjadwalan</h1>
        </div>
        
     <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('dashboard.index') }}" 
               class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors group {{ request()->routeIs('dashboard.*') ? 'sidebar-active text-white' : '' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 mr-3"></i>
                <span class="font-medium">Dashboard</span>
            </a>
            
            <!-- Dashboard/Jadwal -->
            <a href="{{ route('jadwal.index') }}" 
               class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors group {{ request()->routeIs('jadwal.*') ? 'sidebar-active text-white' : '' }}">
                <i data-lucide="calendar" class="w-5 h-5 mr-3"></i>
                <span class="font-medium">Jadwal Kegiatan</span>
            </a>
            
            <!-- Unit Kerja -->
            <a href="{{ route('unit-kerja.index') }}" 
               class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors group {{ request()->routeIs('unit-kerja.*') ? 'sidebar-active text-white' : '' }}">
                <i data-lucide="building" class="w-5 h-5 mr-3"></i>
                <span class="font-medium">Unit Kerja</span>
            </a>
            
            <!-- Anggota -->
            <a href="{{ route('anggota.index') }}" 
               class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors group {{ request()->routeIs('anggota.*') ? 'sidebar-active text-white' : '' }}">
                <i data-lucide="users" class="w-5 h-5 mr-3"></i>
                <span class="font-medium">Anggota</span>
            </a>
            
            <!-- <a href="#" class="flex items-center px-4 py-3 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors group">
                <i data-lucide="bar-chart-3" class="w-5 h-5 mr-3"></i>
                <span class="font-medium">Laporan</span>
            </a> -->
        </nav>
        
        <!-- User Profile -->
        <div class="px-4 py-4 border-t border-gray-200">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                        <i data-lucide="user" class="w-4 h-4 text-gray-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-700">{{ Auth::user()->nama_lengkap }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->role }}</p>
                </div>
            </div>
        </div>
    </div>
</aside>