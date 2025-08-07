{{-- resources/views/dashboard/detail.blade.php --}}
<!-- Modal Detail Jadwal (Dashboard Version) -->
<div id="modalDetailDashboard" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-blue-600">
            <h3 class="text-lg font-semibold text-white">Detail Jadwal Kegiatan</h3>
            <button id="closeDetailDashboardModal" class="text-white hover:text-gray-200 transition-colors">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="p-6">
            <div class="space-y-6">
                <!-- Nama Kegiatan -->
                <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                    <h4 class="text-xl font-bold text-blue-900 mb-2">
                        <i data-lucide="calendar" class="w-6 h-6 inline mr-2"></i>
                        <span id="dashboard-detail-nama-kegiatan"></span>
                    </h4>
                </div>
                
                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Waktu & Tempat -->
                    <div class="space-y-4">
                        <h5 class="font-bold text-gray-900 border-b-2 border-blue-200 pb-2 flex items-center">
                            <i data-lucide="map-pin" class="w-5 h-5 mr-2 text-blue-600"></i>
                            Waktu & Tempat
                        </h5>
                        
                        <div class="space-y-3">
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                <i data-lucide="calendar-days" class="w-5 h-5 text-blue-500 mt-0.5"></i>
                                <div>
                                    <span class="text-sm font-medium text-gray-600">Tanggal:</span>
                                    <p class="font-semibold text-gray-900" id="dashboard-detail-tanggal"></p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                <i data-lucide="clock" class="w-5 h-5 text-green-500 mt-0.5"></i>
                                <div>
                                    <span class="text-sm font-medium text-gray-600">Waktu:</span>
                                    <p class="font-semibold text-gray-900" id="dashboard-detail-waktu"></p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                <i data-lucide="map-pin" class="w-5 h-5 text-red-500 mt-0.5"></i>
                                <div>
                                    <span class="text-sm font-medium text-gray-600">Tempat:</span>
                                    <p class="font-semibold text-gray-900" id="dashboard-detail-tempat"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Organisasi -->
                    <div class="space-y-4">
                        <h5 class="font-bold text-gray-900 border-b-2 border-green-200 pb-2 flex items-center">
                            <i data-lucide="users" class="w-5 h-5 mr-2 text-green-600"></i>
                            Organisasi
                        </h5>
                        
                        <div class="space-y-3">
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                <i data-lucide="user-check" class="w-5 h-5 text-purple-500 mt-0.5"></i>
                                <div>
                                    <span class="text-sm font-medium text-gray-600">Person in Charge:</span>
                                    <p class="font-semibold text-gray-900" id="dashboard-detail-pic"></p>
                                </div>
                            </div>
                            
                            <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg">
                                <i data-lucide="building" class="w-5 h-5 text-orange-500 mt-0.5"></i>
                                <div>
                                    <span class="text-sm font-medium text-gray-600">Unit Kerja:</span>
                                    <p class="font-semibold text-gray-900" id="dashboard-detail-unit-kerja"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Anggota -->
                <div>
                    <h5 class="font-bold text-gray-900 border-b-2 border-purple-200 pb-2 mb-4 flex items-center">
                        <i data-lucide="users" class="w-5 h-5 mr-2 text-purple-600"></i>
                        Anggota yang Terlibat
                    </h5>
                    <div class="bg-gradient-to-r from-purple-50 to-blue-50 p-4 rounded-lg border border-purple-200">
                        <div id="dashboard-detail-anggota" class="text-gray-800 font-medium"></div>
                    </div>
                </div>
                
                <!-- Info -->
                <div class="bg-gradient-to-r from-gray-50 to-blue-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i data-lucide="calendar-plus" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-600">Dibuat pada:</span>
                            <p class="font-medium text-gray-800" id="dashboard-detail-created"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modal Footer -->
        <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 bg-gray-50">
            <button id="btnCloseDashboardDetail" class="px-6 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors flex items-center space-x-2">
                <i data-lucide="x" class="w-4 h-4"></i>
                <span>Tutup</span>
            </button>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Close detail modal events
    $('#closeDetailDashboardModal, #btnCloseDashboardDetail').click(function() {
        $('#modalDetailDashboard').addClass('hidden');
    });
    
    // Close modal when clicking outside
    $('#modalDetailDashboard').click(function(e) {
        if (e.target === this) {
            $(this).addClass('hidden');
        }
    });
    
    // Function to show detail modal
    window.showDetailModal = function(id) {
        $.get(`{{ url('dashboard') }}/${id}`, function(data) {
            // Populate detail modal
            $('#dashboard-detail-nama-kegiatan').text(data.nama_kegiatan);
            
            // Format tanggal dengan nama hari
            const tanggalFormatted = data.tanggal ? new Date(data.tanggal).toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }) : '-';
            $('#dashboard-detail-tanggal').text(tanggalFormatted);
            
            // Format waktu
            const jamMulai = data.jam_mulai ? data.jam_mulai.substring(0, 5) : '';
            const jamSelesai = data.jam_selesai ? data.jam_selesai.substring(0, 5) : '';
            const waktuFormatted = jamMulai && jamSelesai ? `${jamMulai} - ${jamSelesai} WIB` : 'Waktu belum ditentukan';
            $('#dashboard-detail-waktu').text(waktuFormatted);
            
            $('#dashboard-detail-tempat').text(data.nama_tempat || 'Tempat belum ditentukan');
            $('#dashboard-detail-pic').text(data.person_in_charge || 'PIC belum ditentukan');
            $('#dashboard-detail-unit-kerja').text(data.unit_kerja ? data.unit_kerja.nama_unit_kerja : 'Unit kerja belum ditentukan');
            $('#dashboard-detail-anggota').text(data.anggota || 'Belum ada anggota yang ditugaskan');
            
            const createdFormatted = data.created_at ? new Date(data.created_at).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }) : '-';
            $('#dashboard-detail-created').text(createdFormatted);
            
            $('#modalDetailDashboard').removeClass('hidden');
        }).fail(function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Gagal memuat detail jadwal'
            });
        });
    };
});
</script>