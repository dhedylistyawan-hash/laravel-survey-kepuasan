<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Pusbin JF MKG - Survey</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('pusbinjfmkg.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('pusbinjfmkg.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Additional CSS for Navigation Responsiveness -->
        <style>
            .nav-link {
                transition: all 0.2s ease-in-out;
                cursor: pointer;
            }
            
            .nav-link:hover {
                transform: translateY(-1px);
            }
            
            .nav-link:active {
                transform: translateY(0);
            }
            
        /* Prevent double-click issues */
        .nav-link:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }
        
        /* Smooth transitions for all interactive elements */
        button, a, input, select {
            transition: all 0.2s ease-in-out;
        }
        
        /* Disabled state for navigation */
        .nav-link.disabled {
            pointer-events: none;
            opacity: 0.6;
        }
        
        /* Smooth navigation styles */
        .nav-link {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        /* Smooth hover and active states */
        .nav-link:hover {
            text-decoration: none;
            transform: translateY(-1px);
        }
        
        .nav-link:active {
            transform: translateY(0);
        }
        
        /* Ensure proper clickability */
        .nav-link {
            position: relative;
            z-index: 1;
        }
        
        /* Smooth page transitions */
        body {
            transition: opacity 0.3s ease;
        }
        
        /* Loading state for navigation */
        .nav-link.loading {
            opacity: 0.7;
            pointer-events: none;
        }
        </style>
        
        <!-- CSRF Token Setup -->
        <script>
            window.Laravel = {
                csrfToken: '{{ csrf_token() }}'
            };
            
            // Refresh CSRF token setiap 30 menit
            setInterval(function() {
                fetch('/refresh-csrf', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.csrf_token) {
                        document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
                        window.Laravel.csrfToken = data.csrf_token;
                        // Update semua form dengan token baru
                        document.querySelectorAll('input[name="_token"]').forEach(input => {
                            input.value = data.csrf_token;
                        });
                    }
                })
                .catch(error => console.log('CSRF refresh failed:', error));
            }, 30 * 60 * 1000); // 30 menit
            
            // Smooth navigation handling
            document.addEventListener('DOMContentLoaded', function() {
                let lastClickTime = 0;
                
                // Handle all link clicks
                document.addEventListener('click', function(e) {
                    const target = e.target.closest('a');
                    if (target) {
                        const currentTime = Date.now();
                        
                        // Only prevent very rapid clicks (less than 50ms apart)
                        if (currentTime - lastClickTime < 50) {
                            e.preventDefault();
                            return false;
                        }
                        
                        lastClickTime = currentTime;
                        
                        // Add smooth visual feedback for nav-links
                        if (target.classList.contains('nav-link')) {
                            // Add loading class
                            target.classList.add('loading');
                            
                            // Smooth transition
                            target.style.transition = 'all 0.2s ease';
                            target.style.opacity = '0.8';
                            target.style.transform = 'translateY(1px)';
                            
                            // Reset after navigation
                            setTimeout(() => {
                                target.classList.remove('loading');
                                target.style.opacity = '1';
                                target.style.transform = 'translateY(0)';
                            }, 300);
                        }
                    }
                });
                
                // Add smooth page load effect
                window.addEventListener('load', function() {
                    document.body.style.opacity = '1';
                });
            });
        </script>

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        
        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
        <!-- SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        
        @stack('head')
    </head>
    <body class="font-sans antialiased">
        <div id="app-root" class="min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
            @include('layouts.navigation')
            <!-- Dark mode toggle button -->
            <button id="dark-toggle" class="fixed top-4 right-4 z-50 bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-full p-2 shadow transition hover:bg-gray-300 dark:hover:bg-gray-700" title="Toggle dark mode">
                <svg id="icon-moon" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3a7 7 0 109.79 9.79z" /></svg>
                <svg id="icon-sun" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-13.66l-.71.71M4.05 19.07l-.71.71M21 12h-1M4 12H3m16.66 6.66l-.71-.71M4.05 4.93l-.71-.71M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
            </button>
            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>
        @if (session('success'))
            <script>
                window.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses',
                        text: @json(session('success')),
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                });
            </script>
        @endif
        @if (session('error'))
            <script>
                window.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: @json(session('error')),
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                });
            </script>
        @endif
    </body>
</html>
