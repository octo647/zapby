<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação - AgendaZap</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 min-h-screen flex items-center justify-center p-4">
    
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-full mb-4 animate-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Confirme seu WhatsApp</h1>
            <p class="text-gray-600">Verifique seu WhatsApp</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                @foreach ($errors->all() as $error)
                    <p class="text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg mb-6 text-center">
            <p class="text-sm font-medium">✅ Enviamos um código de 6 dígitos</p>
            <p class="text-xs mt-1">O código expira em 10 minutos</p>
        </div>

        <form method="POST" action="{{ route('professional.verify-registration') }}" id="verifyForm">
            @csrf

            <div class="mb-6">
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2 text-center">
                    🔐 Digite o Código
                </label>
                <input 
                    type="text" 
                    id="code" 
                    name="code" 
                    value="{{ old('code') }}"
                    required
                    autofocus
                    maxlength="6"
                    placeholder="000000"
                    class="w-full px-4 py-4 text-center text-2xl font-bold tracking-widest border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6); if(this.value.length === 6) document.getElementById('verifyForm').submit();"
                >
                <p class="mt-2 text-xs text-gray-500 text-center">O código será verificado automaticamente</p>
            </div>

            <button 
                type="submit"
                class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-green-600 hover:to-green-700 transition duration-200 shadow-lg flex items-center justify-center"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Confirmar e Criar Conta
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('professional.register') }}" class="text-sm text-indigo-600 hover:text-indigo-700 transition">
                ← Voltar
            </a>
        </div>
    </div>

    <script>
        // Auto-focus no campo do código
        document.getElementById('code').focus();

        // Adicionar feedback visual ao digitar
        const codeInput = document.getElementById('code');
        codeInput.addEventListener('input', function() {
            if (this.value.length === 6) {
                this.classList.add('border-green-500', 'bg-green-50');
            } else {
                this.classList.remove('border-green-500', 'bg-green-50');
            }
        });
    </script>

</body>
</html>
