<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - ZapBy</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 min-h-screen flex items-center justify-center p-4">
    
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-green-400 to-green-600 rounded-full mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Cadastro</h1>
            <p class="text-gray-600">Comece seu período de teste grátis</p>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                @foreach ($errors->all() as $error)
                    <p class="text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('professional.register') }}">
            @csrf

            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    👤 Nome Completo
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}"
                    required
                    autofocus
                    placeholder="Digite seu nome"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
            </div>

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
                    placeholder="000.000.000-00"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    oninput="this.value = formatCPF(this.value)"
                    maxlength="14"
                >
            </div>

            <div class="mb-4">
                <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-2">
                    📱 Número do WhatsApp
                </label>
                <input 
                    type="text" 
                    id="whatsapp" 
                    name="whatsapp" 
                    value="{{ old('whatsapp') }}"
                    required
                    placeholder="(00) 00000-0000"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    oninput="this.value = formatPhone(this.value)"
                >
                <p class="mt-1 text-xs text-gray-500">Você receberá um código de confirmação</p>
            </div>

            <div class="mb-4">
                <label for="profession" class="block text-sm font-medium text-gray-700 mb-2">
                    💼 Profissão
                </label>
                <input 
                    type="text" 
                    id="profession" 
                    name="profession" 
                    value="{{ old('profession') }}"
                    required
                    placeholder="Ex: Dentista, Psicólogo, Personal Trainer"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    🔒 Senha
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    placeholder="Mínimo 6 caracteres"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
            </div>

            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    🔒 Confirme a Senha
                </label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required
                    placeholder="Digite a senha novamente"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                >
            </div>

            <button 
                type="submit"
                class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-green-600 hover:to-green-700 transition duration-200 shadow-lg"
            >
                Continuar
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Já tem uma conta?
            </p>
            <a href="{{ route('professional.login') }}" class="text-green-600 hover:text-green-700 font-semibold transition">
                Faça login
            </a>
        </div>

        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-2">
                <p class="text-xs text-green-800 text-center">
                    🎉 <strong>7 dias grátis</strong> para testar todas as funcionalidades
                </p>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <p class="text-xs text-blue-800 text-center">
                    💰 Após o período de teste: <strong>R$ 19,90/mês</strong>
                </p>
            </div>
        </div>
    </div>

    <script>
        function formatPhone(value) {
            // Remove tudo que não é dígito
            value = value.replace(/\D/g, '');
            
            // Limita a 11 dígitos
            value = value.substring(0, 11);
            
            // Formata: (00) 00000-0000
            if (value.length > 10) {
                value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
            } else if (value.length > 6) {
                value = value.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
            } else if (value.length > 2) {
                value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
            } else if (value.length > 0) {
                value = value.replace(/^(\d*)/, '($1');
            }
            
            return value;
        }

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
