{{-- resources/views/archive/detail.blade.php --}}
<!-- Modal Detail Jadwal Arsip -->
<div id="modalDetail" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Detail Jadwal Kegiatan (Arsip)</h3>
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

<script>
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

// Keyboard shortcut to close modal (ESC key)
$(document).keydown(function(e) {
    if (e.key === 'Escape' && $('#modalDetail').is(':visible')) {
        $('#modalDetail').addClass('hidden');
    }
});
</script>