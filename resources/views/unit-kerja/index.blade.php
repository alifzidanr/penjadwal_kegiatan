{{-- resources/views/unit-kerja/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Unit Kerja')
@section('page-title', 'Unit Kerja')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Unit Kerja</h1>
                <p class="text-gray-600 mt-1">Kelola data unit kerja organisasi</p>
            </div>
            <button id="btnTambah" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center space-x-2">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Unit Kerja</span>
            </button>
        </div>
    </div>
    
    <!-- Table Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table id="unitKerjaTable" class="w-full table-auto">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Unit Kerja</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Dibuat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include Modal -->
@include('unit-kerja.tambah')

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    const table = $('#unitKerjaTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('unit-kerja.data') }}"
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'nama_unit_kerja', name: 'nama_unit_kerja'},
            {data: 'created_at', name: 'created_at'},
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
        $('#modalTitle').text('Tambah Unit Kerja');
        $('#unitKerjaId').val('');
    });
    
    // Edit button
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        
        $.get(`{{ url('unit-kerja') }}/${id}`, function(data) {
            $('#modalTambah').removeClass('hidden');
            $('#modalTitle').text('Edit Unit Kerja');
            $('#unitKerjaId').val(data.id_unit_kerja);
            $('#nama_unit_kerja').val(data.nama_unit_kerja);
        });
    });
    
    // Delete button
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Hapus Unit Kerja?',
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
                    url: `{{ url('unit-kerja') }}/${id}`,
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
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Terjadi kesalahan saat menghapus data'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush