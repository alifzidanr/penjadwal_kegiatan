{{-- resources/views/jadwal/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Jadwal Kegiatan')
@section('page-title', 'Jadwal Kegiatan')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Jadwal Kegiatan</h1>
                <p class="text-gray-600 mt-1">Kelola dan pantau jadwal kegiatan organisasi</p>
            </div>
            <button id="btnTambah" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center space-x-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Jadwal</span>
            </button>
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
                <button id="btnFilter" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Filter
                </button>
            </div>
        </div>
    </div>
    
    <!-- Table Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table id="jadwalTable" class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kegiatan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Kerja</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tempat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include Modal -->
@include('jadwal.tambah')
@include('jadwal.detail')

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Global variable to store all anggota data
    window.allAnggotaData = [];
    
    // Function to get selected anggota names
    window.getSelectedAnggota = function() {
        const selected = [];
        $('#anggotaList input[type="checkbox"]:checked').each(function() {
            selected.push($(this).val());
        });
        return selected;
    };
    
    // Function to update selected count
    window.updateSelectedCount = function() {
        const count = $('#anggotaList input[type="checkbox"]:checked').length;
        $('#selectedCount').text(count);
    };
    
    // Function to render anggota list
    window.renderAnggotaList = function(anggotaData, selectedAnggota = []) {
        const anggotaList = $('#anggotaList');
        anggotaList.empty();
        
        if (anggotaData.length === 0) {
            anggotaList.html(`
                <div class="text-center text-gray-500 py-4">
                    <i data-lucide="users" class="w-6 h-6 mx-auto mb-2"></i>
                    <p class="text-sm">Tidak ada anggota ditemukan</p>
                </div>
            `);
            lucide.createIcons();
            return;
        }
        
        anggotaData.forEach(function(anggota) {
            const isChecked = selectedAnggota.includes(anggota.nama_anggota);
            const unitKerjaText = anggota.unit_kerja ? anggota.unit_kerja.nama_unit_kerja : 'No Unit';
            
            const anggotaItem = $(`
                <div class="flex items-center space-x-3 p-2 hover:bg-blue-50 rounded-lg transition-colors anggota-item" data-name="${anggota.nama_anggota.toLowerCase()}">
                    <input type="checkbox" 
                           id="anggota_${anggota.id_anggota}" 
                           value="${anggota.nama_anggota}" 
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                           ${isChecked ? 'checked' : ''}>
                    <label for="anggota_${anggota.id_anggota}" class="flex-1 cursor-pointer">
                        <div class="font-medium text-gray-900">${anggota.nama_anggota}</div>
                        <div class="text-sm text-gray-500">${unitKerjaText}</div>
                        ${anggota.jabatan ? `<div class="text-xs text-gray-400">${anggota.jabatan}</div>` : ''}
                    </label>
                </div>
            `);
            
            anggotaList.append(anggotaItem);
        });
        
        // Update count after rendering
        updateSelectedCount();
        
        // Add checkbox change event
        $('#anggotaList input[type="checkbox"]').change(function() {
            updateSelectedCount();
        });
    };

    // Initialize DataTable
    const table = $('#jadwalTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('jadwal.data') }}",
            data: function(d) {
                d.unit_kerja = $('#filterUnitKerja').val();
                d.tanggal_mulai = $('#filterTanggalMulai').val();
                d.tanggal_selesai = $('#filterTanggalSelesai').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'nama_kegiatan', name: 'nama_kegiatan'},
            {data: 'person_in_charge', name: 'person_in_charge'},
            {data: 'unit_kerja', name: 'unit_kerja'},
            {data: 'tanggal_formatted', name: 'tanggal'},
            {data: 'jam_formatted', name: 'jam_formatted', orderable: false},
            {data: 'nama_tempat', name: 'nama_tempat'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        language: {
            "decimal": "",
            "emptyTable": "Tidak ada data yang tersedia",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
            "infoEmpty": "Menampilkan 0 sampai 0 dari 0 entri",
            "infoFiltered": "(disaring dari _MAX_ total entri)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Tampilkan _MENU_ entri",
            "loadingRecords": "Memuat...",
            "processing": "Memproses...",
            "search": "Cari:",
            "zeroRecords": "Tidak ditemukan data yang sesuai",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            }
        },
        pageLength: 10,
        responsive: true,
        dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>',
        drawCallback: function() {
            lucide.createIcons();
        }
    });
    
    // Filter button
    $('#btnFilter').click(function() {
        table.ajax.reload();
    });
    
    // Add button
    $('#btnTambah').click(function() {
        $('#modalTambah').removeClass('hidden');
        $('#formTambah')[0].reset();
        $('#modalTitle').text('Tambah Jadwal Kegiatan');
        $('#jadwalId').val('');
    });
    
    // Detail button click handler
    $(document).on('click', '.btn-detail', function() {
        const id = $(this).data('id');
        
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
        $.get(`{{ url('jadwal') }}/${id}`, function(data) {
            // Format tanggal
            const tanggalFormatted = data.tanggal ? new Date(data.tanggal).toLocaleDateString('id-ID', {
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
                        <div class="flex items-center text-sm text-gray-600">
                            <i data-lucide="calendar" class="w-4 h-4 mr-2"></i>
                            ${tanggalFormatted}
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
    
    // Edit button
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        
        $.get(`{{ url('jadwal') }}/${id}`, function(data) {
            $('#modalTambah').removeClass('hidden');
            $('#modalTitle').text('Edit Jadwal Kegiatan');
            $('#jadwalId').val(data.id_kegiatan);
            $('#nama_kegiatan').val(data.nama_kegiatan);
            $('#person_in_charge').val(data.person_in_charge);
            
            // Fix date format for date input
            if (data.tanggal) {
                const formattedDate = new Date(data.tanggal).toISOString().split('T')[0];
                $('#tanggal').val(formattedDate);
            } else {
                $('#tanggal').val('');
            }
            
            $('#jam_mulai').val(data.jam_mulai ? data.jam_mulai.substring(0, 5) : '');
            $('#jam_selesai').val(data.jam_selesai ? data.jam_selesai.substring(0, 5) : '');
            $('#nama_tempat').val(data.nama_tempat);
            $('#id_unit_kerja').val(data.id_unit_kerja);
            
            // Handle anggota selection for new checkbox system
            const anggotaNames = data.anggota ? data.anggota.split(', ') : [];
            
            // Load all anggota and pre-select the ones from the jadwal
            $.get(`{{ url('anggota/all') }}`, function(anggotaData) {
                window.allAnggotaData = anggotaData;
                window.renderAnggotaList(anggotaData, anggotaNames);
            });
        });
    });
    
    // Delete button
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Hapus Jadwal?',
            text: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('jadwal') }}/${id}`,
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
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menghapus data'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush