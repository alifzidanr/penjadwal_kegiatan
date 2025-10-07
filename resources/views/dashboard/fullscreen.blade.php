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
            background: white;
        }
        
        .fullscreen-container {
            min-height: 100vh;
            background: white;
            padding: 1.5rem;
        }
        
        .table-wrapper {
            height: calc(100vh - 3rem);
            overflow: auto;
        }
        
        /* DataTables customization */
        .dataTables_wrapper {
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        /* Hide length, filter, info, and pagination */
        .dataTables_length,
        .dataTables_filter,
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }
        
        .dataTables_scroll {
            flex: 1;
            overflow: auto;
        }
        
        table.dataTable {
            font-size: 0.875rem;
            width: 100% !important;
        }
        
        table.dataTable thead th {
            padding: 0.75rem 1rem;
            background: #f8fafc;
            font-weight: 600;
            color: #374151;
            border-bottom: 2px solid #e5e7eb;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        table.dataTable tbody td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f3f4f6;
        }
        
        table.dataTable tbody tr:hover {
            background: #f8fafc;
        }
        
        /* Button styles */
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

        /* Update notification */
        .update-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            background: #10b981;
            color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 300px;
            display: none;
        }
        
        .update-notification.show {
            transform: translateX(0);
        }
        
        .update-notification.error {
            background: #ef4444;
        }
    </style>
</head>
<body>
    <div class="fullscreen-container">
        <!-- Update Notification -->
        <div id="updateNotification" class="update-notification">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i data-lucide="bell" class="w-4 h-4 mr-2"></i>
                    <span id="updateMessage">Update detected</span>
                </div>
                <button id="dismissNotification" class="ml-4 text-white hover:text-gray-200">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
        
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

    <!-- Detail Modal -->
    <div id="modalDetail" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Detail Jadwal Kegiatan</h3>
                <button id="closeDetailModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <div class="p-6">
                <div id="detailContent">
                    <div class="text-center py-8">
                        <i data-lucide="loader-2" class="w-8 h-8 mx-auto mb-4 animate-spin text-blue-600"></i>
                        <p class="text-gray-600">Memuat detail kegiatan...</p>
                    </div>
                </div>
            </div>
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
                scrollY: 'calc(100vh - 4rem)',
                scrollCollapse: true,
                ajax: {
                    url: "{{ route('dashboard.data') }}",
                    data: function(d) {
                        d.timestamp = Date.now(); // Add timestamp to prevent caching
                    },
                    error: function(xhr, error, code) {
                        console.error('DataTable AJAX Error:', error, code, xhr.responseText);
                        showUpdateNotification('Error loading data: ' + error, 'error');
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
                    "emptyTable": "Tidak ada jadwal kegiatan",
                    "loadingRecords": "Memuat...",
                    "processing": "Memproses...",
                    "zeroRecords": "Tidak ditemukan kegiatan yang sesuai"
                },
                pageLength: 50,
                paging: false, // Disable pagination to show all records
                searching: false, // Disable search
                lengthChange: false, // Disable length change
                info: false, // Disable info
                order: [[1, 'asc']],
                dom: 'rt', // Only show the table (r = processing, t = table)
                drawCallback: function() {
                    lucide.createIcons();
                }
            });
            
            // Change detection polling
            let pollingInterval;
            let lastDataHash = null;
            let isPolling = false;
            
            function startChangeDetection() {
                if (pollingInterval) clearInterval(pollingInterval);
                
                // Poll every 2 seconds for changes
                pollingInterval = setInterval(() => {
                    checkForChanges();
                }, 2000);
                
                console.log('Change detection started - checking every 2 seconds');
            }
            
            function checkForChanges() {
                if (isPolling) return; // Prevent overlapping requests
                
                isPolling = true;
                
                // Create a lightweight request to check for changes
                $.ajax({
                    url: "{{ route('dashboard.data') }}",
                    method: 'GET',
                    data: {
                        draw: 1,
                        start: 0,
                        length: -1, // Get all records for hash comparison
                        timestamp: Date.now()
                    },
                    success: function(response) {
                        const currentDataHash = generateDataHash(response.data);
                        
                        if (lastDataHash === null) {
                            // First time - just store the hash
                            lastDataHash = currentDataHash;
                        } else if (lastDataHash !== currentDataHash) {
                            // Data has changed - reload the table
                            lastDataHash = currentDataHash;
                            
                            table.ajax.reload(null, false); // false = don't reset paging
                            showUpdateNotification('Changes detected - data updated!', 'success');
                            
                            console.log('Data changes detected and table updated at', new Date().toLocaleTimeString());
                        }
                    },
                    error: function() {
                        console.error('Change detection failed');
                    },
                    complete: function() {
                        isPolling = false;
                    }
                });
            }
            
            function generateDataHash(data) {
                // Create a simple hash of the data to detect changes
                const dataString = JSON.stringify(data.map(item => ({
                    id: item.DT_RowIndex,
                    nama: item.nama_kegiatan,
                    tanggal_mulai: item.tanggal_mulai_formatted,
                    tanggal_selesai: item.tanggal_selesai_formatted,
                    tempat: item.nama_tempat,
                    pic: item.person_in_charge,
                    anggota: item.anggota
                })));
                
                // Simple hash function
                let hash = 0;
                for (let i = 0; i < dataString.length; i++) {
                    const char = dataString.charCodeAt(i);
                    hash = ((hash << 5) - hash) + char;
                    hash = hash & hash; // Convert to 32-bit integer
                }
                return hash;
            }
            
            function showUpdateNotification(message, type = 'success') {
                const notification = $('#updateNotification');
                $('#updateMessage').text(message);
                
                // Set notification type
                notification.removeClass('error');
                if (type === 'error') notification.addClass('error');
                
                notification.addClass('show');
                
                setTimeout(() => {
                    notification.removeClass('show');
                }, 4000);
            }
            
            // Start change detection
            startChangeDetection();
            
            // Event handlers
            $('#dismissNotification').click(function() {
                $('#updateNotification').removeClass('show');
            });
            
            // Detail modal handlers
            $(document).on('click', '.btn-detail', function() {
                const id = $(this).data('id');
                showDetailModal(id);
            });
            
            $(document).on('click', '#closeDetailModal, #btnCloseDetail', function() {
                $('#modalDetail').addClass('hidden');
            });
            
            $('#modalDetail').click(function(e) {
                if (e.target === this) {
                    $(this).addClass('hidden');
                }
            });
            
            // Keyboard shortcut - ESC to go back
            $(document).keydown(function(e) {
                if (e.key === 'Escape' && !$('#modalDetail').hasClass('hidden')) {
                    $('#modalDetail').addClass('hidden');
                } else if (e.key === 'Escape') {
                    window.location.href = "{{ route('dashboard.index') }}";
                }
            });
            
            // Cleanup on page unload
            $(window).on('beforeunload', function() {
                if (pollingInterval) {
                    clearInterval(pollingInterval);
                }
            });
        });
        
        // Detail modal function
        function showDetailModal(id) {
            $('#modalDetail').removeClass('hidden');
            $('#detailContent').html(`
                <div class="text-center py-8">
                    <i data-lucide="loader-2" class="w-8 h-8 mx-auto mb-4 animate-spin text-blue-600"></i>
                    <p class="text-gray-600">Memuat detail kegiatan...</p>
                </div>
            `);
            lucide.createIcons();
            
            $.get(`{{ url('dashboard') }}/${id}`, function(data) {
                const tanggalMulaiFormatted = data.tanggal_mulai ? new Date(data.tanggal_mulai).toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                }) : '-';
                
                const tanggalSelesaiFormatted = data.tanggal_selesai ? new Date(data.tanggal_selesai).toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                }) : '-';
                
                const jamMulai = data.jam_mulai ? data.jam_mulai.substring(0, 5) : '';
                const jamSelesai = data.jam_selesai ? data.jam_selesai.substring(0, 5) : '';
                const waktuFormatted = jamMulai && jamSelesai ? `${jamMulai} - ${jamSelesai}` : 
                                      jamMulai ? `${jamMulai}` : 
                                      jamSelesai ? `Sampai ${jamSelesai}` : '-';
                
                const unitKerjaInfo = data.unit_kerja ? data.unit_kerja.nama_unit_kerja : '-';
                const anggotaList = data.anggota ? data.anggota.split(', ') : [];
                const anggotaHtml = anggotaList.length > 0 ? 
                    anggotaList.map(anggota => `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">${anggota}</span>`).join(' ') :
                    '<span class="text-gray-500">Tidak ada anggota terdaftar</span>';
                
                $('#detailContent').html(`
                    <div class="space-y-6">
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
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i data-lucide="user" class="w-4 h-4 text-gray-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700">Person in Charge</span>
                                </div>
                                <p class="text-gray-900">${data.person_in_charge || '-'}</p>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i data-lucide="building" class="w-4 h-4 text-gray-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700">Unit Kerja</span>
                                </div>
                                <p class="text-gray-900">${unitKerjaInfo}</p>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i data-lucide="clock" class="w-4 h-4 text-gray-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700">Waktu</span>
                                </div>
                                <p class="text-gray-900">${waktuFormatted}</p>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i data-lucide="map-pin" class="w-4 h-4 text-gray-500 mr-2"></i>
                                    <span class="text-sm font-medium text-gray-700">Tempat/Lokasi</span>
                                </div>
                                <p class="text-gray-900">${data.nama_tempat || '-'}</p>
                            </div>
                        </div>
                        
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
        }
    </script>
</body>
</html>