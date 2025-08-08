{{-- resources/views/archive/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Arsip Jadwal')
@section('page-title', 'Arsip Jadwal')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Arsip Jadwal Kegiatan</h1>
                <p class="text-gray-600 mt-1">Lihat dan kelola jadwal kegiatan yang telah diarsipkan</p>
            </div>
            <div class="flex items-center space-x-2">
                <!-- Stats Button -->
                <button id="btnStats" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                    <i data-lucide="bar-chart" class="w-4 h-4 mr-2"></i>
                    <span>Statistik</span>
                </button>
                <!-- Refresh Button -->
                <button id="btnRefresh" class="inline-flex items-center px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors" title="Refresh Data">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6" id="statsCards" style="display: none;">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Total Aktif</p>
                    <p class="text-2xl font-bold text-green-900" id="totalActive">-</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="calendar-check" class="w-6 h-6 text-green-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Total Arsip</p>
                    <p class="text-2xl font-bold text-gray-900" id="totalArchived">-</p>
                </div>
                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="archive" class="w-6 h-6 text-gray-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Minggu Ini</p>
                    <p class="text-2xl font-bold text-blue-900" id="thisWeek">-</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="calendar-days" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-600">Bulan Ini</p>
                    <p class="text-2xl font-bold text-purple-900" id="thisMonth">-</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="calendar-range" class="w-6 h-6 text-purple-600"></i>
                </div>
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
            <h3 class="text-lg font-semibold text-gray-900">Jadwal Kegiatan Terarsip</h3>
            <div class="flex items-center space-x-2">
                <!-- Bulk Actions -->
                <button id="btnBulkRestore" class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors" style="display: none;">
                    <i data-lucide="rotate-ccw" class="w-4 h-4 mr-2"></i>
                    <span>Restore Terpilih</span>
                </button>
            </div>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table id="archiveTable" class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAll" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kegiatan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tempat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Anggota</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diarsip</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include Detail Modal -->
@include('archive.detail')

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#archiveTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('archive.data') }}",
            data: function(d) {
                d.unit_kerja = $('#filterUnitKerja').val();
                d.tanggal_mulai = $('#filterTanggalMulai').val();
                d.tanggal_selesai = $('#filterTanggalSelesai').val();
            }
        },
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                width: '3%',
                render: function(data, type, row) {
                    return `<input type="checkbox" class="row-select w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" data-id="${row.DT_RowIndex}">`;
                }
            },
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '4%'},
            {data: 'tanggal_mulai_formatted', name: 'tanggal_mulai', width: '9%'},
            {data: 'tanggal_selesai_formatted', name: 'tanggal_selesai', width: '9%'},
            {data: 'nama_kegiatan', name: 'nama_kegiatan', width: '18%'},
            {data: 'nama_tempat', name: 'nama_tempat', width: '10%'},
            {data: 'jam_formatted', name: 'jam_formatted', orderable: false, width: '8%'},
            {data: 'person_in_charge', name: 'person_in_charge', width: '10%'},
            {data: 'anggota', name: 'anggota', width: '11%'},
            {data: 'archived_at', name: 'archived_at', width: '9%'},
            {data: 'action', name: 'action', orderable: false, searchable: false, width: '9%'}
        ],
        language: {
            "decimal": "",
            "emptyTable": "Tidak ada jadwal yang diarsipkan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ jadwal",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 jadwal",
            "infoFiltered": "(disaring dari _MAX_ total jadwal)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Tampilkan _MENU_ jadwal",
            "loadingRecords": "Memuat...",
            "processing": "Memproses...",
            "search": "Cari jadwal:",
            "zeroRecords": "Tidak ditemukan jadwal yang sesuai",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            }
        },
        pageLength: 10,
        responsive: true,
        order: [[2, 'desc']], // Order by tanggal_mulai desc (newest first)
        dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>',
        drawCallback: function() {
            lucide.createIcons();
            updateBulkActionVisibility();
        }
    });
    
    // Select All functionality
    $('#selectAll').change(function() {
        $('.row-select').prop('checked', this.checked);
        updateBulkActionVisibility();
    });
    
    // Individual checkbox functionality
    $(document).on('change', '.row-select', function() {
        updateBulkActionVisibility();
        
        // Update select all checkbox
        const totalRows = $('.row-select').length;
        const checkedRows = $('.row-select:checked').length;
        $('#selectAll').prop('indeterminate', checkedRows > 0 && checkedRows < totalRows);
        $('#selectAll').prop('checked', checkedRows === totalRows);
    });
    
    // Update bulk action button visibility
    function updateBulkActionVisibility() {
        const checkedRows = $('.row-select:checked').length;
        if (checkedRows > 0) {
            $('#btnBulkRestore').show();
        } else {
            $('#btnBulkRestore').hide();
        }
    }
    
    // Filter button
    $('#btnFilter').click(function() {
        table.ajax.reload();
    });
    
    // Refresh button
    $('#btnRefresh').click(function() {
        table.ajax.reload();
        loadStats();
        
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
    
    // Stats button
    $('#btnStats').click(function() {
        $('#statsCards').toggle();
        if ($('#statsCards').is(':visible')) {
            loadStats();
        }
    });
    
   // Load statistics
    function loadStats() {
        $.get("{{ route('archive.stats') }}", function(data) {
            $('#totalActive').text(data.total_active);
            $('#totalArchived').text(data.total_archived);
            $('#thisWeek').text(data.this_week);
            $('#thisMonth').text(data.this_month);
        });
    }
    
    // Detail button click handler
    $(document).on('click', '.btn-detail', function() {
        const id = $(this).data('id');
        showDetailModal(id);
    });
    
    // Restore button click handler
    $(document).on('click', '.btn-restore', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Restore Jadwal?',
            text: 'Jadwal akan dikembalikan ke daftar aktif',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Restore!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('archive/restore') }}/${id}`,
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
                            table.ajax.reload();
                            loadStats();
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat restore jadwal'
                        });
                    }
                });
            }
        });
    });
    
    // Permanent delete button click handler
    $(document).on('click', '.btn-delete-permanent', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Hapus Permanen?',
            text: 'Data akan dihapus permanen dan tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus Permanen!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('archive/destroy-permanent') }}/${id}`,
                    method: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            table.ajax.reload();
                            loadStats();
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menghapus jadwal'
                        });
                    }
                });
            }
        });
    });
    
    // Bulk restore button
    $('#btnBulkRestore').click(function() {
        const selectedIds = [];
        $('.row-select:checked').each(function() {
            selectedIds.push($(this).data('id'));
        });
        
        if (selectedIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: 'Pilih minimal satu jadwal untuk direstore'
            });
            return;
        }
        
        Swal.fire({
            title: `Restore ${selectedIds.length} Jadwal?`,
            text: 'Jadwal yang dipilih akan dikembalikan ke daftar aktif',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Restore Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Sedang restore jadwal terpilih',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Process bulk restore
                $.ajax({
                    url: '{{ url("archive/bulk-restore") }}',
                    method: 'POST',
                    data: {
                        ids: selectedIds
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: `${response.count} jadwal berhasil direstore`,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            table.ajax.reload();
                            loadStats();
                            $('#selectAll').prop('checked', false);
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat restore jadwal'
                        });
                    }
                });
            }
        });
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
    $.get(`{{ url('archive') }}/${id}`, function(data) {
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
        
        // Archive status
        const archiveStatus = data.is_archived ? 
            `<div class="bg-red-50 rounded-lg p-4">
                <div class="flex items-center mb-2">
                    <i data-lucide="archive" class="w-4 h-4 text-red-500 mr-2"></i>
                    <span class="text-sm font-medium text-red-700">Status Arsip</span>
                </div>
                <p class="text-red-900">Diarsipkan pada: ${data.archived_at ? new Date(data.archived_at).toLocaleString('id-ID') : '-'}</p>
            </div>` : '';
        
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
                
                ${archiveStatus}
                
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
                
                <!-- Action Buttons -->
                <div class="flex justify-end space-x-2 pt-4 border-t">
                    <button class="btn-restore px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors" data-id="${data.id_kegiatan}">
                        <i data-lucide="rotate-ccw" class="w-4 h-4 inline mr-2"></i>
                        Restore ke Aktif
                    </button>
                    <button class="btn-delete-permanent px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors" data-id="${data.id_kegiatan}">
                        <i data-lucide="trash-2" class="w-4 h-4 inline mr-2"></i>
                        Hapus Permanen
                    </button>
                </div>
            </div>
        `);
        
        lucide.createIcons();
        
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
</script>
@endpush