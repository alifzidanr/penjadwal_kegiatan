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
                                <th class="text-left">Nama Kegiatan</th>
                                <th class="text-left">PIC</th>
                                <th class="text-left">Unit Kerja</th>
                                <th class="text-left">Tanggal</th>
                                <th class="text-left">Waktu</th>
                                <th class="text-left">Tempat</th>
                                <th class="text-left">Detail</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Detail Modal -->
    @include('dashboard.detail')

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
                    {data: 'nama_kegiatan', name: 'nama_kegiatan', width: '25%'},
                    {data: 'person_in_charge', name: 'person_in_charge', width: '15%'},
                    {data: 'unit_kerja', name: 'unit_kerja', width: '15%'},
                    {data: 'tanggal_formatted', name: 'tanggal', width: '12%'},
                    {data: 'jam_formatted', name: 'jam_formatted', orderable: false, width: '13%'},
                    {data: 'nama_tempat', name: 'nama_tempat', width: '10%'},
                    {data: 'action', name: 'action', orderable: false, searchable: false, width: '5%'}
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
                order: [[4, 'asc']], // Order by tanggal
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
            
            // Detail button click handler
            $(document).on('click', '.btn-detail', function() {
                const id = $(this).data('id');
                showDetailModal(id);
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
                showToast('Welcome to Fullscreen View! Press ESC to go back.', 'info');
            }, 1000);
        });
        
        // Detail modal function (you'll need to implement this based on your existing modal)
        function showDetailModal(id) {
            // Your existing detail modal logic here
            console.log('Show detail for ID:', id);
            // You can copy the detail modal logic from your dashboard/index.blade.php
        }
    </script>
</body>
</html>