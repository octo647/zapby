<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - ZapBy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.facebook-pixel')
    <script>
        // Evento: Lead - Usuário chegou na página de registro
        fbq('track', 'Lead', {content_name: 'Registration Page'});
    </script>
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

        <form method="POST" action="{{ route('professional.register') }}" onsubmit="fbq('track', 'CompleteRegistration', {content_name: 'Professional Registration'});">
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

            <div class="mb-6">
                <label for="referral_code" class="block text-sm font-medium text-gray-700 mb-2">
                    🎁 Código de indicação (opcional)
                </label>
                <input
                    type="text"
                    id="referral_code"
                    name="referral_code"
                    value="{{ old('referral_code', request('ref')) }}"
                    placeholder="Quem indicou você?"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg uppercase focus:ring-2 focus:ring-green-500 focus:border-transparent"
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
