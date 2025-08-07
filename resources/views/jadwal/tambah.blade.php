{{-- resources/views/jadwal/tambah.blade.php --}}
<!-- Modal Tambah/Edit Jadwal -->
<div id="modalTambah" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 id="modalTitle" class="text-lg font-semibold text-gray-900">Tambah Jadwal Kegiatan</h3>
            <button id="closeModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="p-6">
            <form id="formTambah" class="space-y-6">
                <input type="hidden" id="jadwalId" name="id">
                
                <!-- Nama Kegiatan -->
                <div>
                    <label for="nama_kegiatan" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kegiatan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama_kegiatan" name="nama_kegiatan" required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                           placeholder="Masukkan nama kegiatan">
                </div>
                
                <!-- Person in Charge -->
                <div>
                    <label for="person_in_charge" class="block text-sm font-medium text-gray-700 mb-2">
                        Person in Charge (PIC)
                    </label>
                    <input type="text" id="person_in_charge" name="person_in_charge"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                           placeholder="Masukkan nama PIC">
                </div>
                
                <!-- Unit Kerja -->
                <div>
                    <label for="id_unit_kerja" class="block text-sm font-medium text-gray-700 mb-2">
                        Unit Kerja
                    </label>
                    <select id="id_unit_kerja" name="id_unit_kerja"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <option value="">Pilih Unit Kerja</option>
                        @foreach($unitKerja as $unit)
                            <option value="{{ $unit->id_unit_kerja }}">{{ $unit->nama_unit_kerja }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Anggota -->
                <div>
                    <label for="anggota" class="block text-sm font-medium text-gray-700 mb-2">
                        Anggota
                    </label>
                    
                    <!-- Search Input -->
                    <div class="relative mb-2">
                        <input type="text" id="anggotaSearch" placeholder="Cari anggota..." 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-3"></i>
                    </div>
                    
                    <!-- Anggota List Container -->
                    <div class="border border-gray-300 rounded-lg p-3 bg-gray-50 max-h-48 overflow-y-auto">
                        <div id="anggotaList" class="space-y-2">
                            <!-- Anggota checkboxes will be loaded here -->
                            <div class="text-center text-gray-500 py-4">
                                <i data-lucide="users" class="w-6 h-6 mx-auto mb-2"></i>
                                <p class="text-sm">Pilih unit kerja untuk melihat anggota</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Selected Count -->
                    <div class="mt-2 text-sm text-gray-600">
                        <span id="selectedCount">0</span> anggota dipilih
                    </div>
                    
                    <!-- Hidden input to store selected anggota -->
                    <input type="hidden" id="selectedAnggota" name="anggota">
                </div>
                
                <!-- Tanggal dan Waktu -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="tanggal" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal
                        </label>
                        <input type="date" id="tanggal" name="tanggal"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>
                    <div>
                        <label for="jam_mulai" class="block text-sm font-medium text-gray-700 mb-2">
                            Jam Mulai
                        </label>
                        <input type="time" id="jam_mulai" name="jam_mulai"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>
                    <div>
                        <label for="jam_selesai" class="block text-sm font-medium text-gray-700 mb-2">
                            Jam Selesai
                        </label>
                        <input type="time" id="jam_selesai" name="jam_selesai"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>
                </div>
                
                <!-- Tempat -->
                <div>
                    <label for="nama_tempat" class="block text-sm font-medium text-gray-700 mb-2">
                        Tempat/Lokasi
                    </label>
                    <input type="text" id="nama_tempat" name="nama_tempat"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                           placeholder="Masukkan nama tempat">
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
        const jadwalId = $('#jadwalId').val();
        const isEdit = jadwalId !== '';
        
        // Show loading state
        const simpanText = $('#simpanText');
        const simpanLoader = $('#simpanLoader');
        const btnSimpan = $('#btnSimpan');
        
        btnSimpan.prop('disabled', true);
        simpanText.addClass('hidden');
        simpanLoader.removeClass('hidden');
        
        // Prepare data
        const selectedAnggotaNames = getSelectedAnggota();
        const formData = {
            nama_kegiatan: $('#nama_kegiatan').val(),
            person_in_charge: $('#person_in_charge').val(),
            id_unit_kerja: $('#id_unit_kerja').val(),
            anggota: selectedAnggotaNames.join(', '),
            tanggal: $('#tanggal').val(),
            jam_mulai: $('#jam_mulai').val(),
            jam_selesai: $('#jam_selesai').val(),
            nama_tempat: $('#nama_tempat').val()
        };
        
        // AJAX request
        const url = isEdit ? `{{ url('jadwal') }}/${jadwalId}` : "{{ route('jadwal.store') }}";
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
                    $('#jadwalTable').DataTable().ajax.reload();
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
    
    // Global variable to store all anggota data
    let allAnggotaData = [];
    
    // Function to get selected anggota names
    function getSelectedAnggota() {
        const selected = [];
        $('#anggotaList input[type="checkbox"]:checked').each(function() {
            selected.push($(this).val());
        });
        return selected;
    }
    
    // Function to update selected count
    function updateSelectedCount() {
        const count = $('#anggotaList input[type="checkbox"]:checked').length;
        $('#selectedCount').text(count);
    }
    
    // Function to render anggota list
    function renderAnggotaList(anggotaData, selectedAnggota = []) {
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
    }
    
    // Search functionality
    $('#anggotaSearch').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        
        if (searchTerm === '') {
            $('.anggota-item').show();
        } else {
            $('.anggota-item').each(function() {
                const itemName = $(this).data('name');
                if (itemName.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    });
    
    // Load anggota when modal opens or unit kerja changes
    function loadAnggota(selectedUnitKerja = null, selectedAnggota = []) {
        $.get(`{{ url('anggota/all') }}`, function(data) {
            allAnggotaData = data;
            renderAnggotaList(data, selectedAnggota);
        }).fail(function() {
            $('#anggotaList').html(`
                <div class="text-center text-red-500 py-4">
                    <i data-lucide="alert-circle" class="w-6 h-6 mx-auto mb-2"></i>
                    <p class="text-sm">Gagal memuat data anggota</p>
                </div>
            `);
            lucide.createIcons();
        });
    }
    
    // Load anggota when unit kerja changes
    $('#id_unit_kerja').change(function() {
        const unitKerjaId = $(this).val();
        $('#anggotaSearch').val(''); // Clear search
        
        if (unitKerjaId) {
            $.get(`{{ url('anggota/by-unit') }}/${unitKerjaId}`, function(data) {
                allAnggotaData = data;
                renderAnggotaList(data);
            }).fail(function() {
                $('#anggotaList').html(`
                    <div class="text-center text-red-500 py-4">
                        <i data-lucide="alert-circle" class="w-6 h-6 mx-auto mb-2"></i>
                        <p class="text-sm">Gagal memuat anggota unit kerja</p>
                    </div>
                `);
                lucide.createIcons();
            });
        } else {
            loadAnggota();
        }
    });
    
    // Load all anggota on modal open
    $('#btnTambah').click(function() {
        $('#anggotaSearch').val(''); // Clear search
        loadAnggota();
    });
    
    // Validation for jam_selesai should be after jam_mulai
    $('#jam_mulai, #jam_selesai').change(function() {
        const jamMulai = $('#jam_mulai').val();
        const jamSelesai = $('#jam_selesai').val();
        
        if (jamMulai && jamSelesai && jamSelesai <= jamMulai) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan!',
                text: 'Jam selesai harus lebih besar dari jam mulai'
            });
            $('#jam_selesai').val('');
        }
    });
});
</script>