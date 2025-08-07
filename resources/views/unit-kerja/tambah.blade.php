{{-- resources/views/unit-kerja/tambah.blade.php --}}
<!-- Modal Tambah/Edit Unit Kerja -->
<div id="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Tambah Unit Kerja</h3>
            <button id="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="p-6">
            <form id="formTambah" class="space-y-4">
                <input type="hidden" id="unitKerjaId" name="id">
                
                <!-- Nama Unit Kerja -->
                <div>
                    <label for="nama_unit_kerja" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Unit Kerja <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama_unit_kerja" name="nama_unit_kerja" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                           placeholder="Masukkan nama unit kerja">
                    <small class="text-gray-500 text-xs">Contoh: IT & Technology, Human Resources, Finance & Accounting</small>
                </div>
            </form>
        </div>
        
        <!-- Modal Footer -->
        <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 bg-gray-50">
            <button id="btnBatal" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Batal
            </button>
            <button id="btnSimpan" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center space-x-2">
                <span id="simpanText">Simpan</span>
                <span id="simpanLoader" class="hidden">
                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                </span>
            </button>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Close modal events
    $('#closeModal, #btnBatal').click(function() {
        $('#modalTambah').addClass('hidden');
    });
    
    // Close modal when clicking outside
    $('#modalTambah').click(function(e) {
        if (e.target === this) {
            $(this).addClass('hidden');
        }
    });
    
    // Submit form
    $('#btnSimpan').click(function() {
        const form = $('#formTambah');
        const unitKerjaId = $('#unitKerjaId').val();
        const isEdit = unitKerjaId !== '';
        
        // Show loading state
        const simpanText = $('#simpanText');
        const simpanLoader = $('#simpanLoader');
        const btnSimpan = $('#btnSimpan');
        
        btnSimpan.prop('disabled', true);
        simpanText.addClass('hidden');
        simpanLoader.removeClass('hidden');
        
        // Prepare data
        const formData = {
            nama_unit_kerja: $('#nama_unit_kerja').val()
        };
        
        // AJAX request
        const url = isEdit ? `{{ url('unit-kerja') }}/${unitKerjaId}` : "{{ route('unit-kerja.store') }}";
        const method = isEdit ? 'PUT' : 'POST';
        
        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    $('#modalTambah').addClass('hidden');
                    $('#unitKerjaTable').DataTable().ajax.reload();
                }
            },
            error: function(xhr) {
                const errors = xhr.responseJSON?.errors;
                let errorMessage = 'Terjadi kesalahan saat menyimpan data';
                
                if (errors) {
                    const firstError = Object.values(errors)[0];
                    errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                } else if (xhr.responseJSON?.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage
                });
            },
            complete: function() {
                // Hide loading state
                btnSimpan.prop('disabled', false);
                simpanText.removeClass('hidden');
                simpanLoader.addClass('hidden');
                lucide.createIcons();
            }
        });
    });
    
    // Enter key submit
    $('#nama_unit_kerja').keypress(function(e) {
        if (e.which === 13) {
            $('#btnSimpan').click();
        }
    });
});
</script>