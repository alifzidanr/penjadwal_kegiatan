{{-- resources/views/anggota/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Anggota')
@section('page-title', 'Anggota')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Anggota</h1>
                <p class="text-gray-600 mt-1">Kelola data anggota organisasi</p>
            </div>
            <button id="btnTambah" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center space-x-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Anggota</span>
            </button>
        </div>
    </div>
    
    <!-- Table Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table id="anggotaTable" class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Telepon</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Kerja</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include Modal -->
@include('anggota.tambah')

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#anggotaTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('anggota.data') }}"
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'nama_anggota', name: 'nama_anggota'},
            {data: 'email', name: 'email'},
            {data: 'jabatan', name: 'jabatan'},
            {data: 'nomor_telepon', name: 'nomor_telepon'},
            {data: 'unit_kerja', name: 'unit_kerja'},
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
    
    // Add button
    $('#btnTambah').click(function() {
        $('#modalTambah').removeClass('hidden');
        $('#formTambah')[0].reset();
        $('#modalTitle').text('Tambah Anggota');
        $('#anggotaId').val('');
    });
    
    // Edit button
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        
        $.get(`{{ url('anggota') }}/${id}`, function(data) {
            $('#modalTambah').removeClass('hidden');
            $('#modalTitle').text('Edit Anggota');
            $('#anggotaId').val(data.id_anggota);
            $('#nama_anggota').val(data.nama_anggota);
            $('#email').val(data.email);
            $('#jabatan').val(data.jabatan);
            $('#nomor_telepon').val(data.nomor_telepon);
            $('#id_unit_kerja').val(data.id_unit_kerja);
        });
    });
    
    // Delete button
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Hapus Anggota?',
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
                    url: `{{ url('anggota') }}/${id}`,
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