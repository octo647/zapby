<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ZapBy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 min-h-screen flex items-center justify-center p-4">
    
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <a
                href="https://zapby.com.br"
                class="inline-flex items-center justify-center mb-4 no-underline text-inherit hover:no-underline focus:no-underline"
            >
                <img src="/logo_fundo_branco.png" alt="ZapBy" class="w-20 h-20 rounded-full shadow-lg">
            </a>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">ZapBy</h1>
            <p class="text-gray-600">Área do Profissional</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                @foreach ($errors->all() as $error)
                    <p class="text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('professional.login') }}">
            @csrf

            <div class="mb-4">
                <label for="cpf" class="block text-sm font-medium text-gray-700 mb-2">
                    📋 CPF
                </label>
                <input 
                    type="text" 
                    id="cpf" 
                    name="cpf" 
                    value="{{ old('cpf') }}"
                    required
                    autofocus
                    placeholder="000.000.000-00"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    oninput="this.value = formatCPF(this.value)"
                    maxlength="14"
                >
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    🔒 Senha
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    placeholder="Digite sua senha"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
            </div>

            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-green-600 shadow-sm focus:ring-green-500">
                    <span class="ml-2 text-sm text-gray-600">Lembrar de mim</span>
                </label>
                <a href="{{ route('professional.password.request') }}" class="text-sm text-green-600 hover:text-green-700 font-semibold transition">
                    Esqueci minha senha
                </a>
            </div>

            <button 
                type="submit"
                class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-green-600 hover:to-green-700 transition duration-200 shadow-lg"
            >
                Entrar
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Não tem uma conta?
            </p>
            <a href="{{ route('professional.register') }}" class="text-green-600 hover:text-green-700 font-semibold transition">
                Cadastre-se agora
            </a>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200 text-center">
            <div class="flex items-center justify-center space-x-2 text-xs text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
                <span>Login seguro</span>
            </div>
        </div>
    </div>

    <script>
        function formatCPF(value) {
            // Remove tudo que não é dígito
            value = value.replace(/\D/g, '');
            
            // Limita a 11 dígitos
            value = value.substring(0, 11);
            
            // Formata: 000.000.000-00
            if (value.length > 9) {
                value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2}).*/, '$1.$2.$3-$4');
            } else if (value.length > 6) {
                value = value.replace(/^(\d{3})(\d{3})(\d{0,3}).*/, '$1.$2.$3');
            } else if (value.length > 3) {
                value = value.replace(/^(\d{3})(\d{0,3})/, '$1.$2');
            }
            
            return value;
        }
    </script>

</body>
</html>
