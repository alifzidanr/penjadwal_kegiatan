{{-- resources/views/auth/login.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sistem Penjadwalan Kegiatan</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="mx-auto w-16 h-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mb-4">
                    <i data-lucide="calendar-check" class="w-8 h-8 text-white"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Sistem Penjadwalan</h1>
                <p class="text-gray-600">Masuk untuk melanjutkan ke dashboard</p>
            </div>
            
            <!-- Login Form -->
            <form id="loginForm" class="space-y-6">
                @csrf
              <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="user" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input type="text" id="username" name="username" required
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               placeholder="Masukkan username">
                    </div>
                </div>
                
                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                               class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               placeholder="Masukkan password">
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i data-lucide="eye" class="w-5 h-5 text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" id="remember" name="remember" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">Ingat saya</label>
                    </div>
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-500">Lupa password?</a>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" id="loginBtn"
                        class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3 px-4 rounded-lg font-medium hover:from-blue-600 hover:to-indigo-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span id="loginBtnText">Masuk</span>
                    <span id="loginBtnLoader" class="hidden">
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin inline mr-2"></i>
                        Memproses...
                    </span>
                </button>
            </form>
            
            <!-- Footer -->
            <div class="mt-8 text-center text-sm text-gray-600">
                <p>&copy; 2024 Sistem Penjadwalan Kegiatan. All rights reserved.</p>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // Initialize Lucide Icons
        lucide.createIcons();
        
        // CSRF Token for Ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Toggle Password Visibility
        $('#togglePassword').click(function() {
            const passwordField = $('#password');
            const eyeIcon = $(this).find('i');
            
            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                eyeIcon.attr('data-lucide', 'eye-off');
            } else {
                passwordField.attr('type', 'password');
                eyeIcon.attr('data-lucide', 'eye');
            }
            lucide.createIcons();
        });
        
        // Login Form Submit
        $('#loginForm').submit(function(e) {
            e.preventDefault();
            
            const loginBtn = $('#loginBtn');
            const loginBtnText = $('#loginBtnText');
            const loginBtnLoader = $('#loginBtnLoader');
            
            // Show loading state
            loginBtn.prop('disabled', true);
            loginBtnText.addClass('hidden');
            loginBtnLoader.removeClass('hidden');
            
            $.ajax({
                url: '/login',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = response.redirect;
                        });
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Gagal!',
                        text: response.message || 'Terjadi kesalahan, silakan coba lagi'
                    });
                },
                complete: function() {
                    // Hide loading state
                    loginBtn.prop('disabled', false);
                    loginBtnText.removeClass('hidden');
                    loginBtnLoader.addClass('hidden');
                    lucide.createIcons();
                }
            });
        });
    </script>
</body>
</html>