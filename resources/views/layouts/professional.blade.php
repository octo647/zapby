<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Painel') - ZapBy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.facebook-pixel')
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100">
    
    <!-- Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-indigo-600">ZapBy</h1>
                    
                    <!-- Desktop Menu -->
                    <div class="hidden md:flex ml-10 space-x-4">
                        @if(Auth::guard('professional')->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.*') ? 'bg-purple-100 text-purple-700' : 'text-purple-700 hover:bg-purple-50' }}">
                            <i class="fas fa-shield-alt mr-1"></i> Admin
                        </a>
                        @endif
                        <a href="{{ route('professional.dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('professional.dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            Painel
                        </a>
                        <a href="{{ route('professional.appointments') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('professional.appointments') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            Agendamentos
                        </a>
                        <a href="{{ route('professional.time-blocks') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('professional.time-blocks') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            Bloqueios
                        </a>
                        <a href="{{ route('professional.settings') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('professional.settings') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            Configurações
                        </a>
                        <a href="{{ route('professional.subscription') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('professional.subscription*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                            Assinatura
                            @if(Auth::guard('professional')->user()->subscription && Auth::guard('professional')->user()->subscription->status === 'trial')
                                <span class="ml-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Trial</span>
                            @endif
                        </a>
                    </div>
                </div>
                
                <div class="flex items-center">
                    <!-- User name - hidden on mobile -->
                    <span class="hidden sm:block text-sm text-gray-700 mr-4">{{ Auth::guard('professional')->user()->name }}</span>
                    
                    <!-- Logout button - hidden on mobile -->
                    <form method="POST" action="{{ route('professional.logout') }}" class="hidden sm:block">
                        @csrf
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-600 transition">
                            Sair
                        </button>
                    </form>
                    
                    <!-- Mobile menu button -->
                    <button 
                        onclick="toggleMobileMenu()" 
                        class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                        aria-label="Menu"
                    >
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path id="menu-icon" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path id="close-icon" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden">
            <div class="px-2 pt-2 pb-3 space-y-1">
                @if(Auth::guard('professional')->user()->is_admin)
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.*') ? 'bg-purple-100 text-purple-700' : 'text-purple-700 hover:bg-purple-50' }}">
                    🛡️ Admin
                </a>
                @endif
                <a href="{{ route('professional.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('professional.dashboard') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    📊 Painel
                </a>
                <a href="{{ route('professional.appointments') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('professional.appointments') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    Agendamentos
                </a>
                <a href="{{ route('professional.time-blocks') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('professional.time-blocks') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    🚫 Bloqueios
                </a>
                <a href="{{ route('professional.settings') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('professional.settings') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    ⚙️ Configurações
                </a>
                <a href="{{ route('professional.subscription') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('professional.subscription*') ? 'bg-indigo-100 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                    💳 Assinatura
                    @if(Auth::guard('professional')->user()->subscription && Auth::guard('professional')->user()->subscription->status === 'trial')
                        <span class="ml-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Trial</span>
                    @endif
                </a>
                
                <!-- User info and logout on mobile -->
                <div class="border-t border-gray-200 pt-3 mt-3">
                    <div class="px-3 py-2 text-sm text-gray-700 font-medium">
                        👤 {{ Auth::guard('professional')->user()->name }}
                    </div>
                    <form method="POST" action="{{ route('professional.logout') }}" class="px-3">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-red-50">
                            🚪 Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const menuIcon = document.getElementById('menu-icon');
            const closeIcon = document.getElementById('close-icon');
            
            menu.classList.toggle('hidden');
            menuIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        }
    </script>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
