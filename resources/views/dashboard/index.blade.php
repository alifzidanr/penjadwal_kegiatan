{{-- resources/views/dashboard/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-gray-600 mt-1">Lihat jadwal kegiatan organisasi</p>
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <i data-lucide="calendar" class="w-4 h-4"></i>
                <span>{{ date('d F Y') }}</span>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Total Kegiatan</p>
                    <p class="text-2xl font-bold text-gray-900" id="totalKegiatan">-</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="calendar-check" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Hari Ini</p>
                    <p class="text-2xl font-bold text-green-900" id="kegiatanHariIni">-</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Minggu Ini</p>
                    <p class="text-2xl font-bold text-purple-900" id="kegiatanMingguIni">-</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="calendar-days" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Bulan Ini</p>
                    <p class="text-2xl font-bold text-gray-900" id="kegiatanBulanIni">-</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="calendar-range" class="w-6 h-6 text-gray-600"></i>
                </div>
            </div>
        </div>
    </div> -->
    
    <!-- Filters Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Unit Kerja</label>
                <select id="filterUnitKerja" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Unit Kerja</option>
                    @foreach($unitKerja as $unit)
                        <option value="{{ $unit->id_unit_kerja }}">{{ $unit->nama_unit_kerja }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                <input type="date" id="filterTanggalMulai" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                <input type="date" id="filterTanggalSelesai" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-end">
                <button id="btnFilter" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i data-lucide="filter" class="w-4 h-4 inline mr-2"></i>Filter
                </button>
            </div>
        </div>
    </div>
    
    <!-- Table Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Jadwal Kegiatan</h3>
            <div class="flex items-center space-x-2">
                <!-- Fullscreen Button -->
                <a href="{{ route('dashboard.fullscreen') }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors" title="Open Fullscreen View">
                    <i data-lucide="maximize" class="w-4 h-4 mr-2"></i>
                    <span>Fullscreen</span>
                </a>
                <!-- Refresh Button -->
                <button id="btnRefresh" class="inline-flex items-center px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors" title="Refresh Data">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table id="dashboardTable" class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kegiatan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tempat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include Detail Modal -->
@include('dashboard.detail')

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#dashboardTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('dashboard.data') }}",
            data: function(d) {
                d.unit_kerja = $('#filterUnitKerja').val();
                d.tanggal_mulai = $('#filterTanggalMulai').val();
                d.tanggal_selesai = $('#filterTanggalSelesai').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'nama_kegiatan', name: 'nama_kegiatan'},
            {data: 'tanggal_formatted', name: 'tanggal'},
            {data: 'jam_formatted', name: 'jam_formatted', orderable: false},
            {data: 'nama_tempat', name: 'nama_tempat'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        language: {
            "decimal": "",
            "emptyTable": "Tidak ada jadwal kegiatan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ kegiatan",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 kegiatan",
            "infoFiltered": "(disaring dari _MAX_ total kegiatan)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Tampilkan _MENU_ kegiatan",
            "loadingRecords": "Memuat...",
            "processing": "Memproses...",
            "search": "Cari kegiatan:",
            "zeroRecords": "Tidak ditemukan kegiatan yang sesuai",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            }
        },
        pageLength: 10,
        responsive: true,
        order: [[2, 'asc']], // Order by tanggal
        dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>',
        drawCallback: function() {
            lucide.createIcons();
            updateStats();
        }
    });
    
    // Filter button
    $('#btnFilter').click(function() {
        table.ajax.reload();
    });
    
    // Refresh button
    $('#btnRefresh').click(function() {
        table.ajax.reload();
        
        // Show refresh notification
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });

        Toast.fire({
            icon: 'success',
            title: 'Data refreshed!'
        });
    });
    
    // Update stats after table load
    function updateStats() {
        // This is a simple implementation, you could make it more sophisticated
        // by calling a separate API endpoint for stats
        const info = table.page.info();
        $('#totalKegiatan').text(info.recordsTotal);
        
        // For demo purposes, using random numbers for other stats
        // In real implementation, you'd calculate these from actual data
        $('#kegiatanHariIni').text(Math.floor(Math.random() * 5) + 1);
        $('#kegiatanMingguIni').text(Math.floor(Math.random() * 15) + 5);
        $('#kegiatanBulanIni').text(Math.floor(info.recordsTotal * 0.8));
    }
    
    // Detail button click handler
    $(document).on('click', '.btn-detail', function() {
        const id = $(this).data('id');
        showDetailModal(id);
    });
    
    // Initialize stats on page load
    setTimeout(updateStats, 1000);
    
    // Keyboard shortcut for fullscreen (F11 or Ctrl+Shift+F)
    $(document).keydown(function(e) {
        if (e.key === 'F11' || (e.ctrlKey && e.shiftKey && e.key === 'F')) {
            e.preventDefault();
            window.location.href = "{{ route('dashboard.fullscreen') }}";
        }
    });
    
    // Show tooltip for fullscreen shortcut
    $('a[href="{{ route('dashboard.fullscreen') }}"]').hover(function() {
        $(this).attr('title', 'Open Fullscreen View (F11 or Ctrl+Shift+F)');
    });
});

// Detail modal function (implement based on your existing modal)
function showDetailModal(id) {
    // Your existing detail modal logic here
    console.log('Show detail for ID:', id);
}
</script>
@endpush