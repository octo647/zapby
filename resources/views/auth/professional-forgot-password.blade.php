<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - ZapBy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 min-h-screen flex items-center justify-center p-4">
    
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center mb-4">
                <img src="/zapby_redondo.png" alt="ZapBy" class="w-24 h-24 rounded-full shadow-lg">
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Recuperar Senha</h1>
            <p class="text-gray-600">Informe seu CPF para receber um código de verificação via WhatsApp</p>
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

        <form method="POST" action="{{ route('professional.password.send') }}">
            @csrf

            <div class="mb-6">
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

            <button 
                type="submit"
                class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-green-600 hover:to-green-700 transition duration-200 shadow-lg"
            >
                Enviar Código
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('professional.login') }}" class="text-sm text-gray-600 hover:text-gray-700 transition">
                ← Voltar para o login
            </a>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-sm text-blue-700">
                        <p class="font-semibold mb-1">Como funciona?</p>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            <li>Informe seu CPF cadastrado</li>
                            <li>Você receberá um código de 6 dígitos no WhatsApp</li>
                            <li>Use o código para criar uma nova senha</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 rounded-lg p-4 mt-3">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-xs text-yellow-700">
                        <p class="font-semibold mb-1">⚠️ Importante!</p>
                        <p>Para receber o código, você precisa ter conversado com nosso bot WhatsApp anteriormente. Se nunca recebeu mensagens do ZapBy, entre em contato com o suporte.</p>
                    </div>
                </div>
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
