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
        
        /* Status badge styles */
        .status-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.375rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            width: fit-content;
        }
        
        .status-ongoing {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-duration {
            background: #dbeafe;
            color: #1e40af;
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
            display: block;
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
                        <th class="text-left">Status</th>
                    </tr>
                </thead>
            </table>
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
            
            // Helper function to get Indonesian day name
            function getIndonesianDayName(dateString) {
                if (!dateString) return '';
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const date = new Date(dateString);
                return days[date.getDay()];
            }
            
            // Helper function to format date with day name
            function formatDateWithDay(dateString) {
                if (!dateString) return '-';
                const date = new Date(dateString);
                const dayName = getIndonesianDayName(dateString);
                const day = String(date.getDate()).padStart(2, '0');
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const year = date.getFullYear();
                return `${dayName}, ${day}-${month}-${year}`;
            }
            
            // Helper function to calculate duration and check if ongoing
            function getStatusInfo(startDate, endDate) {
                if (!startDate || !endDate) return { duration: 0, isOngoing: false };
                
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                const start = new Date(startDate);
                start.setHours(0, 0, 0, 0);
                
                const end = new Date(endDate);
                end.setHours(0, 0, 0, 0);
                
                // Calculate duration in days
                const durationMs = end - start;
                const durationDays = Math.ceil(durationMs / (1000 * 60 * 60 * 24)) + 1;
                
                // Check if ongoing
                const isOngoing = today >= start && today <= end;
                
                return {
                    duration: durationDays,
                    isOngoing: isOngoing
                };
            }
            
            // Initialize DataTable
            const table = $('#fullscreenTable').DataTable({
                processing: true,
                serverSide: true,
                scrollY: 'calc(100vh - 4rem)',
                scrollCollapse: true,
                ajax: {
                    url: "{{ route('dashboard.data') }}",
                    data: function(d) {
                        d.timestamp = Date.now();
                    },
                    error: function(xhr, error, code) {
                        console.error('DataTable AJAX Error:', error, code, xhr.responseText);
                        showUpdateNotification('Error loading data: ' + error, 'error');
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%'},
                    {
                        data: 'tanggal_mulai',
                        name: 'tanggal_mulai',
                        width: '12%',
                        render: function(data, type, row) {
                            return formatDateWithDay(data);
                        }
                    },
                    {
                        data: 'tanggal_selesai',
                        name: 'tanggal_selesai',
                        width: '12%',
                        render: function(data, type, row) {
                            return formatDateWithDay(data);
                        }
                    },
                    {data: 'nama_kegiatan', name: 'nama_kegiatan', width: '20%'},
                    {data: 'nama_tempat', name: 'nama_tempat', width: '12%'},
                    {data: 'jam_formatted', name: 'jam_formatted', orderable: false, width: '10%'},
                    {data: 'person_in_charge', name: 'person_in_charge', width: '12%'},
                    {data: 'anggota', name: 'anggota', width: '10%'},
                    {
                        data: null,
                        name: 'status',
                        orderable: false,
                        searchable: false,
                        width: '12%',
                        render: function(data, type, row) {
                            const statusInfo = getStatusInfo(row.tanggal_mulai, row.tanggal_selesai);
                            
                            let html = '<div class="flex flex-col gap-2 items-center">';
                            
                            if (statusInfo.isOngoing) {
                                html += '<span class="status-badge status-ongoing">Berlangsung</span>';
                            }
                            
                            html += `<span class="status-badge status-duration">${statusInfo.duration} hari</span>`;
                            html += '</div>';
                            
                            return html;
                        }
                    }
                ],
                language: {
                    "emptyTable": "Tidak ada jadwal kegiatan",
                    "loadingRecords": "Memuat...",
                    "processing": "Memproses...",
                    "zeroRecords": "Tidak ditemukan kegiatan yang sesuai"
                },
                pageLength: 50,
                paging: false,
                searching: false,
                lengthChange: false,
                info: false,
                order: [[1, 'asc']],
                dom: 'rt',
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
                
                pollingInterval = setInterval(() => {
                    checkForChanges();
                }, 2000);
                
                console.log('Change detection started - checking every 2 seconds');
            }
            
            function checkForChanges() {
                if (isPolling) return;
                
                isPolling = true;
                
                $.ajax({
                    url: "{{ route('dashboard.data') }}",
                    method: 'GET',
                    data: {
                        draw: 1,
                        start: 0,
                        length: -1,
                        timestamp: Date.now()
                    },
                    success: function(response) {
                        const currentDataHash = generateDataHash(response.data);
                        
                        if (lastDataHash === null) {
                            lastDataHash = currentDataHash;
                        } else if (lastDataHash !== currentDataHash) {
                            lastDataHash = currentDataHash;
                            
                            table.ajax.reload(null, false);
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
                const dataString = JSON.stringify(data.map(item => ({
                    id: item.DT_RowIndex,
                    nama: item.nama_kegiatan,
                    tanggal_mulai: item.tanggal_mulai,
                    tanggal_selesai: item.tanggal_selesai,
                    tempat: item.nama_tempat,
                    pic: item.person_in_charge,
                    anggota: item.anggota
                })));
                
                let hash = 0;
                for (let i = 0; i < dataString.length; i++) {
                    const char = dataString.charCodeAt(i);
                    hash = ((hash << 5) - hash) + char;
                    hash = hash & hash;
                }
                return hash;
            }
            
            function showUpdateNotification(message, type = 'success') {
                const notification = $('#updateNotification');
                $('#updateMessage').text(message);
                
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
            
            // Keyboard shortcut - ESC to go back
            $(document).keydown(function(e) {
                if (e.key === 'Escape') {
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
    </script>
</body>
</html>