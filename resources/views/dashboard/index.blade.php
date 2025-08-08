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
                <!-- Auto Archive Button -->
                <button id="btnAutoArchive" class="inline-flex items-center px-3 py-2 bg-yellow-600 text-white text-sm rounded-lg hover:bg-yellow-700 transition-colors" title="Archive Outdated Schedules">
                    <i data-lucide="archive" class="w-4 h-4 mr-2"></i>
                    <span>Auto Archive</span>
                </button>
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
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kegiatan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tempat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Anggota</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include Detail Modal -->
<div id="modalDetail" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Detail Jadwal Kegiatan</h3>
            <button id="closeDetailModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="p-6">
            <div id="detailContent">
                <!-- Content will be loaded here -->
                <div class="text-center py-8">
                    <i data-lucide="loader-2" class="w-8 h-8 mx-auto mb-4 animate-spin text-blue-600"></i>
                    <p class="text-gray-600">Memuat detail kegiatan...</p>
                </div>
            </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="flex items-center justify-end p-6 border-t border-gray-200 bg-gray-50 space-x-3">
            <button id="btnCloseDetail" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

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
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%'},
            {data: 'tanggal_mulai_formatted', name: 'tanggal_mulai', width: '10%'},
            {data: 'tanggal_selesai_formatted', name: 'tanggal_selesai', width: '10%'},
            {data: 'nama_kegiatan', name: 'nama_kegiatan', width: '20%'},
            {data: 'nama_tempat', name: 'nama_tempat', width: '12%'},
            {data: 'jam_formatted', name: 'jam_formatted', orderable: false, width: '10%'},
            {data: 'person_in_charge', name: 'person_in_charge', width: '12%'},
            {data: 'anggota', name: 'anggota', width: '11%'},
            {data: 'action', name: 'action', orderable: false, searchable: false, width: '10%'}
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
        order: [[1, 'asc']], // Order by tanggal_mulai (earliest first)
        dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>',
        drawCallback: function() {
            lucide.createIcons();
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
    
    // Auto Archive button
    $('#btnAutoArchive').click(function() {
        Swal.fire({
            title: 'Auto Archive Outdated Schedules?',
            text: 'This will archive all schedules with dates before today',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d97706',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, Archive!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    text: 'Archiving outdated schedules',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    url: "{{ route('dashboard.auto-archive') }}",
                    method: 'POST',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                timer: 3000,
                                showConfirmButton: false
                            });
                            table.ajax.reload();
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to archive outdated schedules'
                        });
                    }
                });
            }
        });
    });
    
    // Detail button click handler
    $(document).on('click', '.btn-detail', function() {
        const id = $(this).data('id');
        showDetailModal(id);
    });
    
    // Close detail modal
    $(document).on('click', '#closeDetailModal, #btnCloseDetail', function() {
        $('#modalDetail').addClass('hidden');
    });
    
    // Close modal when clicking outside
    $('#modalDetail').click(function(e) {
        if (e.target === this) {
            $(this).addClass('hidden');
        }
    });
    
    // Keyboard shortcuts
    $(document).keydown(function(e) {
        if (e.key === 'F11' || (e.ctrlKey && e.shiftKey && e.key === 'F')) {
            e.preventDefault();
            window.location.href = "{{ route('dashboard.fullscreen') }}";
        }
        
        // Ctrl+Shift+A for auto archive
        if (e.ctrlKey && e.shiftKey && e.key === 'A') {
            e.preventDefault();
            $('#btnAutoArchive').click();
        }
        
        // ESC to close modals
        if (e.key === 'Escape') {
            $('#modalDetail').addClass('hidden');
        }
    });
    
    // Show tooltip for shortcuts
    $('a[href="{{ route('dashboard.fullscreen') }}"]').hover(function() {
        $(this).attr('title', 'Open Fullscreen View (F11 or Ctrl+Shift+F)');
    });
});

// Detail modal function
function showDetailModal(id) {
    // Show loading state
    $('#modalDetail').removeClass('hidden');
    $('#detailContent').html(`
        <div class="text-center py-8">
            <i data-lucide="loader-2" class="w-8 h-8 mx-auto mb-4 animate-spin text-blue-600"></i>
            <p class="text-gray-600">Memuat detail kegiatan...</p>
        </div>
    `);
    lucide.createIcons();
    
    // Fetch detail data
    $.get(`{{ url('dashboard') }}/${id}`, function(data) {
        // Format tanggal mulai
        const tanggalMulaiFormatted = data.tanggal_mulai ? new Date(data.tanggal_mulai).toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }) : '-';
        
        // Format tanggal selesai
        const tanggalSelesaiFormatted = data.tanggal_selesai ? new Date(data.tanggal_selesai).toLocaleDateString('id-ID', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        }) : '-';
        
        // Format waktu
        const jamMulai = data.jam_mulai ? data.jam_mulai.substring(0, 5) : '';
        const jamSelesai = data.jam_selesai ? data.jam_selesai.substring(0, 5) : '';
        const waktuFormatted = jamMulai && jamSelesai ? `${jamMulai} - ${jamSelesai}` : 
                              jamMulai ? `${jamMulai}` : 
                              jamSelesai ? `Sampai ${jamSelesai}` : '-';
        
        // Unit kerja info
        const unitKerjaInfo = data.unit_kerja ? data.unit_kerja.nama_unit_kerja : '-';
        
        // Anggota list
        const anggotaList = data.anggota ? data.anggota.split(', ') : [];
        const anggotaHtml = anggotaList.length > 0 ? 
            anggotaList.map(anggota => `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${anggota}</span>`).join(' ') :
            '<span class="text-gray-500">Tidak ada anggota terdaftar</span>';
        
        // Update modal content
        $('#detailContent').html(`
            <div class="space-y-6">
                <!-- Nama Kegiatan -->
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-2">${data.nama_kegiatan || '-'}</h4>
                    <div class="flex items-center text-sm text-gray-600 space-x-4">
                        <div class="flex items-center">
                            <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                            <span>Mulai: ${tanggalMulaiFormatted}</span>
                        </div>
                        <div class="flex items-center">
                            <i data-lucide="calendar-check" class="w-4 h-4 mr-2"></i>
                            <span>Selesai: ${tanggalSelesaiFormatted}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- PIC -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <i data-lucide="user" class="w-4 h-4 text-gray-500 mr-2"></i>
                            <span class="text-sm font-medium text-gray-700">Person in Charge</span>
                        </div>
                        <p class="text-gray-900">${data.person_in_charge || '-'}</p>
                    </div>
                    
                    <!-- Unit Kerja -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <i data-lucide="building" class="w-4 h-4 text-gray-500 mr-2"></i>
                            <span class="text-sm font-medium text-gray-700">Unit Kerja</span>
                        </div>
                        <p class="text-gray-900">${unitKerjaInfo}</p>
                    </div>
                    
                    <!-- Waktu -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <i data-lucide="clock" class="w-4 h-4 text-gray-500 mr-2"></i>
                            <span class="text-sm font-medium text-gray-700">Waktu</span>
                        </div>
                        <p class="text-gray-900">${waktuFormatted}</p>
                    </div>
                    
                    <!-- Tempat -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <i data-lucide="map-pin" class="w-4 h-4 text-gray-500 mr-2"></i>
                            <span class="text-sm font-medium text-gray-700">Tempat/Lokasi</span>
                        </div>
                        <p class="text-gray-900">${data.nama_tempat || '-'}</p>
                    </div>
                </div>
                
                <!-- Anggota -->
                <div>
                    <div class="flex items-center mb-3">
                        <i data-lucide="users" class="w-4 h-4 text-gray-500 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Anggota Kegiatan</span>
                        <span class="ml-2 text-xs text-gray-500">(${anggotaList.length} orang)</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        ${anggotaHtml}
                    </div>
                </div>
                
                <!-- Action Buttons (for archive/restore) -->
                <div class="flex justify-end space-x-2 pt-4 border-t">
                    ${!data.is_archived ? `
                        <button class="btn-archive px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors" data-id="${data.id_kegiatan}">
                            <i data-lucide="archive" class="w-4 h-4 inline mr-2"></i>
                            Arsipkan Manual
                        </button>
                    ` : `
                        <div class="text-sm text-gray-500 bg-gray-100 px-4 py-2 rounded-lg">
                            <i data-lucide="archive" class="w-4 h-4 inline mr-2"></i>
                            Jadwal telah diarsipkan
                        </div>
                    `}
                </div>
            </div>
        `);
        
        lucide.createIcons();
        
        // Add event listeners for archive button
        $('.btn-archive').click(function() {
            const id = $(this).data('id');
            archiveSchedule(id);
        });
        
    }).fail(function() {
        $('#detailContent').html(`
            <div class="text-center py-8">
                <i data-lucide="alert-circle" class="w-8 h-8 mx-auto mb-4 text-red-500"></i>
                <p class="text-red-600">Gagal memuat detail kegiatan</p>
            </div>
        `);
        lucide.createIcons();
    });
}

// Archive schedule function
function archiveSchedule(id) {
    Swal.fire({
        title: 'Arsipkan Jadwal?',
        text: 'Jadwal akan dipindahkan ke arsip',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d97706',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Arsipkan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `{{ url('dashboard/manual-archive') }}/${id}`,
                method: 'POST',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        $('#modalDetail').addClass('hidden');
                        table.ajax.reload();
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mengarsipkan jadwal'
                    });
                }
            });
        }
    });
}
</script>
@endpush