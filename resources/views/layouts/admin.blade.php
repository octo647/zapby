<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - ZapBy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50">
    
    <!-- Navigation -->
    <nav class="bg-gradient-to-r from-purple-600 to-indigo-600 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center min-w-0">
                    <h1 class="text-xl sm:text-2xl font-bold text-white flex items-center truncate">
                        <i class="fas fa-shield-alt mr-2"></i> ZapBy Admin
                    </h1>
                    
                    <!-- Desktop Menu -->
                    <div class="hidden md:flex ml-10 space-x-4">
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-white bg-opacity-20 text-white' : 'text-white text-opacity-90 hover:bg-white hover:bg-opacity-10' }}">
                            <i class="fas fa-chart-line mr-1"></i> Dashboard
                        </a>
                        <a href="{{ route('professional.dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium text-white text-opacity-90 hover:bg-white hover:bg-opacity-10">
                            <i class="fas fa-user-circle mr-1"></i> Meu Painel
                        </a>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <span class="hidden sm:block text-sm text-white mr-4">
                        <i class="fas fa-user-shield mr-1"></i> {{ Auth::guard('professional')->user()->name }}
                    </span>
                    
                    <form method="POST" action="{{ route('professional.logout') }}" class="hidden sm:block">
                        @csrf
                        <button type="submit" class="bg-white bg-opacity-20 text-white px-4 py-2 rounded-lg text-sm hover:bg-white hover:bg-opacity-30 transition">
                            <i class="fas fa-sign-out-alt mr-1"></i> Sair
                        </button>
                    </form>

                    <!-- Mobile menu button -->
                    <button 
                        type="button"
                        onclick="toggleAdminMobileMenu()" 
                        class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-white text-opacity-90 hover:bg-white hover:bg-opacity-10 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                        aria-label="Menu"
                        aria-controls="admin-mobile-menu"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path id="admin-menu-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path id="admin-close-icon" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="admin-mobile-menu" class="hidden md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-white bg-opacity-20 text-white' : 'text-white text-opacity-90 hover:bg-white hover:bg-opacity-10' }}">
                    <i class="fas fa-chart-line mr-2"></i> Dashboard
                </a>
                <a href="{{ route('professional.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-white text-opacity-90 hover:bg-white hover:bg-opacity-10">
                    <i class="fas fa-user-circle mr-2"></i> Meu Painel
                </a>
                
                <!-- User info and logout on mobile -->
                <div class="border-t border-white border-opacity-20 pt-3 mt-3">
                    <div class="px-3 py-2 text-sm text-white text-opacity-90 font-medium">
                        <i class="fas fa-user-shield mr-2"></i> {{ Auth::guard('professional')->user()->name }}
                    </div>
                    <form method="POST" action="{{ route('professional.logout') }}" class="px-3">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-white text-opacity-90 hover:bg-white hover:bg-opacity-10">
                            <i class="fas fa-sign-out-alt mr-2"></i> Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <script>
        function toggleAdminMobileMenu() {
            const menu = document.getElementById('admin-mobile-menu');
            const menuIcon = document.getElementById('admin-menu-icon');
            const closeIcon = document.getElementById('admin-close-icon');
            
            menu.classList.toggle('hidden');
            menuIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        }
    </script>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
