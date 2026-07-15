<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Profissional - Zapby</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-800 mb-2">📅 Zapby</h1>
                <p class="text-xl text-gray-600">Cadastro de Profissional</p>
            </div>

            <!-- Card Principal -->
            <div class="bg-white rounded-2xl shadow-xl p-8 mb-6">
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded">
                    <h3 class="font-semibold text-green-800 mb-2">✨ Plano Mensal</h3>
                    <div class="text-3xl font-bold text-green-600 mb-1">R$ 19,90<span class="text-sm text-gray-600">/mês</span></div>
                    <p class="text-sm text-gray-600">Acesso completo a todas as funcionalidades</p>
                </div>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                        <ul class="text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 rounded">
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                @endif

                <form action="{{ route('professional.register.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Nome -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome Completo *
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name') }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="Seu nome completo"
                        >
                    </div>

                    <!-- CPF -->
                    <div>
                        <label for="cpf" class="block text-sm font-medium text-gray-700 mb-2">
                            CPF *
                        </label>
                        <input 
                            type="text" 
                            id="cpf" 
                            name="cpf" 
                            value="{{ old('cpf') }}"
                            required
                            maxlength="14"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="000.000.000-00"
                        >
                    </div>

                    <!-- WhatsApp -->
                    <div>
                        <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-2">
                            WhatsApp *
                        </label>
                        <input 
                            type="text" 
                            id="whatsapp" 
                            name="whatsapp" 
                            value="{{ old('whatsapp') }}"
                            required
                            maxlength="15"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="(00) 00000-0000"
                        >
                        <p class="mt-1 text-xs text-gray-500">Este número receberá notificações de agendamentos</p>
                    </div>

                    <!-- Profissão -->
                    <div>
                        <label for="profession" class="block text-sm font-medium text-gray-700 mb-2">
                            Profissão *
                        </label>
                        <input 
                            type="text" 
                            id="profession" 
                            name="profession" 
                            value="{{ old('profession') }}"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="Ex: Barbeiro, Cabeleireiro, Manicure..."
                        >
                    </div>

                    <!-- Duração Padrão -->
                    <div>
                        <label for="default_duration" class="block text-sm font-medium text-gray-700 mb-2">
                            Duração Padrão do Atendimento (minutos) *
                        </label>
                        <select 
                            id="default_duration" 
                            name="default_duration"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        >
                            <option value="30">30 minutos</option>
                            <option value="45">45 minutos</option>
                            <option value="60" selected>60 minutos (1 hora)</option>
                            <option value="90">90 minutos (1h30)</option>
                            <option value="120">120 minutos (2 horas)</option>
                        </select>
                    </div>

                    <!-- Senha -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Senha *
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            minlength="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="Mínimo 6 caracteres"
                        >
                    </div>

                    <!-- Confirmar Senha -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmar Senha *
                        </label>
                        <input 
                            type="password" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            required
                            minlength="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="Digite a senha novamente"
                        >
                    </div>

                    <!-- Botão Submit -->
                    <button 
                        type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-lg transition duration-200 shadow-lg hover:shadow-xl"
                    >
                        🚀 Iniciar Assinatura - R$ 19,90/mês
                    </button>

                    <p class="text-xs text-center text-gray-500 mt-4">
                        Ao cadastrar, você concorda com nossos Termos de Uso e Política de Privacidade
                    </p>
                </form>
            </div>

            <!-- Benefícios -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h3 class="text-xl font-bold text-gray-800 mb-4">✨ O que você terá acesso:</h3>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Agendamentos pelo WhatsApp automatizados</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Notificações automáticas para você e seus clientes</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Painel de controle completo</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Gestão de horários e bloqueios</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-green-500 mr-3">✓</span>
                        <span class="text-gray-700">Sem limite de agendamentos</span>
                    </li>
                </ul>
            </div>

            <div class="text-center mt-6">
                <a href="/" class="text-sm text-gray-600 hover:text-gray-800">← Voltar para o início</a>
            </div>
        </div>
    </div>

    <script>
        // Máscara de CPF
        document.getElementById('cpf').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        });

        // Máscara de WhatsApp
        document.getElementById('whatsapp').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 10) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            } else {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
            }
            e.target.value = value;
        });
    </script>
</body>
</html>
