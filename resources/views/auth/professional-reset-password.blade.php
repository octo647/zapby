<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - ZapBy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 min-h-screen flex items-center justify-center p-4">
    
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center mb-4">
                <img src="/zapby_redondo.png" alt="ZapBy" class="w-24 h-24 rounded-full shadow-lg">
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Nova Senha</h1>
            <p class="text-gray-600">Digite o código recebido no WhatsApp e sua nova senha</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                @foreach ($errors->all() as $error)
                    <p class="text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        @endif
        
        @if (session('warning'))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded-lg mb-4">
                <p class="text-sm">{{ session('warning') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('professional.password.reset') }}">
            @csrf

            <div class="mb-4">
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                    🔢 Código de Verificação
                </label>
                <input 
                    type="text" 
                    id="code" 
                    name="code" 
                    value="{{ old('code') }}"
                    required
                    autofocus
                    placeholder="000000"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-center text-2xl font-mono tracking-widest"
                    maxlength="6"
                    oninput="this.value = this.value.replace(/\D/g, '').substring(0, 6)"
                >
                <p class="text-xs text-gray-500 mt-1">Digite o código de 6 dígitos enviado para seu WhatsApp</p>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    🔒 Nova Senha
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    placeholder="Digite sua nova senha"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    minlength="6"
                >
                <p class="text-xs text-gray-500 mt-1">Mínimo de 6 caracteres</p>
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    🔒 Confirmar Nova Senha
                </label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required
                    placeholder="Digite novamente sua nova senha"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    minlength="6"
                >
            </div>

            <button 
                type="submit"
                class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-green-600 hover:to-green-700 transition duration-200 shadow-lg"
            >
                Redefinir Senha
            </button>
        </form>

        <div class="mt-6 text-center space-y-2">
            <p class="text-sm text-gray-600">
                Não recebeu o código?
            </p>
            <a href="{{ route('professional.password.request') }}" class="text-sm text-green-600 hover:text-green-700 font-semibold transition block">
                Reenviar código
            </a>
            <a href="{{ route('professional.login') }}" class="text-sm text-gray-600 hover:text-gray-700 transition block">
                ← Voltar para o login
            </a>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="bg-yellow-50 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-xs text-yellow-700">
                        <p class="font-semibold mb-1">O código expira em 10 minutos</p>
                        <p>Se o tempo expirar, solicite um novo código.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
