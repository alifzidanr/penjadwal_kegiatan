{{-- resources/views/dashboard/fullscreen.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Jadwal Kegiatan - Fullscreen View</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Fullscreen specific styles */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        .fullscreen-container {
            min-height: 100vh;
            background: #f8fafc;
        }
        
        .table-container {
            background: white;
            margin: 0;
            border-radius: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .table-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            flex-shrink: 0;
        }
        
        .table-content {
            flex: 1;
            padding: 1.5rem;
            overflow: auto;
        }
        
        .table-wrapper {
            height: 100%;
            overflow: auto;
        }
        
        /* DataTables customization */
        .dataTables_wrapper {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .dataTables_length,
        .dataTables_filter {
            margin-bottom: 1rem;
        }
        
        .dataTables_scroll {
            flex: 1;
            overflow: auto;
        }
        
        table.dataTable {
            font-size: 0.875rem;
        }
        
        table.dataTable thead th {
            padding: 0.75rem 1rem;
            background: #f8fafc;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
        }
        
        table.dataTable tbody td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f3f4f6;
        }
        
        table.dataTable tbody tr:hover {
            background: #f8fafc;
        }
        
        /* Button styles */
        .btn-primary {
            background: #3b82f6;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-primary:hover {
            background: #2563eb;
        }
        
        .btn-secondary {
            background: #6b7280;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
        }
        
        .btn-detail {
            background: #10b981;
            color: white;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            border: none;
            cursor: pointer;
            font-size: 0.875rem;
            transition: all 0.2s;
        }
        
        .btn-detail:hover {
            background: #059669;
        }
    </style>
</head>
<body>
    <div class="fullscreen-container">
        <div class="table-container">
            <!-- Header -->
            <div class="table-header">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                            <i data-lucide="calendar" class="w-6 h-6 mr-3 text-blue-600"></i>
                            Jadwal Kegiatan - Fullscreen View
                        </h1>
                        <p class="text-gray-600 mt-1">{{ date('l, d F Y') }}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <!-- Auto Archive Button -->
                        <button id="btnAutoArchive" class="btn-secondary flex items-center space-x-2" title="Auto Archive Outdated Schedules" style="background: #d97706;">
                            <i data-lucide="archive" class="w-4 h-4"></i>
                            <span>Auto Archive</span>
                        </button>
                        <!-- Refresh Button -->
                        <button id="btnRefresh" class="btn-secondary flex items-center space-x-2" title="Refresh Data">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            <span>Refresh</span>
                        </button>
                        <!-- Back to Dashboard -->
                        <a href="{{ route('dashboard.index') }}" class="btn-primary flex items-center space-x-2">
                            <i data-lucide="arrow-left" class="w-4 h-4"></i>
                            <span>Back to Dashboard</span>
                        </a>
                    </div>
                </div>
                
                <!-- Filters -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
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
                        <button id="btnFilter" class="w-full btn-primary flex items-center justify-center space-x-2">
                            <i data-lucide="filter" class="w-4 h-4"></i>
                            <span>Apply Filter</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Table Content -->
            <div class="table-content">
                <div class="table-wrapper">
                    <table id="fullscreenTable" class="w-full table-auto display">
                        <thead>
                            <tr>
                                <th class="text-left">No</th>
                                <th class="text-left">Tanggal Mulai</th>
                                <th class="text-left">Tanggal Selesai</th>
                                <th class="text-left">Nama Kegiatan</th>
                                <th class="text-left">Tempat</th>
                                <th class="text-left">Waktu</th>
                                <th class="text-left">PIC</th>
                                <th class="text-left">Anggota</th>
                                <th class="text-left">Detail</th>
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
            <div class="flex items-center justify-end p-6 border-t border-gray-200 bg-gray-50">
                <button id="btnCloseDetail" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Add CSRF token to all AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // Initialize icons
            lucide.createIcons();
            
            // Initialize DataTable
            const table = $('#fullscreenTable').DataTable({
                processing: true,
                serverSide: true,
                scrollY: 'calc(100vh - 280px)',
                scrollCollapse: true,
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
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
                order: [[1, 'asc']], // Order by tanggal_mulai (earliest first)
                dom: '<"flex justify-between items-center mb-4"<"flex items-center space-x-4"l<"ml-4"f>><"flex items-center space-x-2"B>>rt<"flex justify-between items-center mt-4"ip>',
                drawCallback: function() {
                    lucide.createIcons();
                }
            });
            
            // Filter button
            $('#btnFilter').click(function() {
                table.ajax.reload();
                showToast('Filter applied successfully!', 'success');
            });
            
            // Refresh button
            $('#btnRefresh').click(function() {
                table.ajax.reload();
                showToast('Data refreshed!', 'success');
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
            
            // Show toast notification
            function showToast(message, type = 'info') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: type,
                    title: message
                });
            }
            
            // Keyboard shortcuts
            $(document).keydown(function(e) {
                // F5 or Ctrl+R for refresh
                if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
                    e.preventDefault();
                    $('#btnRefresh').click();
                }
                
                // Escape to go back
                if (e.key === 'Escape') {
                    window.location.href = "{{ route('dashboard.index') }}";
                }
                
                // Ctrl+Shift+A for auto archive
                if (e.ctrlKey && e.shiftKey && e.key === 'A') {
                    e.preventDefault();
                    $('#btnAutoArchive').click();
                }
                
                // Ctrl+F for search (let browser handle this)
                if (e.ctrlKey && e.key === 'f') {
                    // Let the browser handle this for table search
                }
            });
            
            // Auto-refresh every 5 minutes
            setInterval(function() {
                table.ajax.reload(null, false); // false = don't reset paging
                console.log('Auto-refreshed at', new Date().toLocaleTimeString());
            }, 5 * 60 * 1000); // 5 minutes
            
            // Welcome message
            setTimeout(function() {
                showToast('Welcome to Fullscreen View! Press ESC to go back, Ctrl+Shift+A for auto archive.', 'info');
            }, 1000);
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
                        
                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-2 pt-4 border-t">
                            <button class="btn-archive px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors" data-id="${data.id_kegiatan}">
                                <i data-lucide="archive" class="w-4 h-4 inline mr-2"></i>
                                Arsipkan Manual
                            </button>
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
                                $('#fullscreenTable').DataTable().ajax.reload();
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
</body>
</html>