<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sistema completo de agendamentos via WhatsApp. Receba confirmações automáticas, gerencie horários e serviços de forma profissional.">
    
    <title>ZapBy - Agendamentos Profissionais via WhatsApp</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#16a34a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ZapBy">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/logo_fundo_branco.png">

    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        #install-banner {
            animation: slideUp 0.3s ease-out;
        }
        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Header / Navegação -->
    <nav class="bg-white shadow-sm fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-green-600">ZapBy</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('professional.login') }}" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-semibold">
                        Login
                    </a>
                   
                    <a href="{{ route('professional.register') }}" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition font-semibold">
                        Começar Agora
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 bg-gradient-to-br from-green-50 to-blue-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-5xl font-bold text-gray-900 mb-6">
                        Agendamentos Profissionais <span class="text-green-600">via WhatsApp</span>
                    </h1>
                    <p class="text-xl text-gray-600 mb-8">
                        Simplifique sua agenda! Seus clientes agendam pelo WhatsApp e você recebe confirmações automáticas. 
                        Sem complicação, sem apps extras.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('professional.register') }}" class="inline-flex items-center justify-center bg-green-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-green-700 transition">
                            <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            Começar Grátis - 7 Dias
                        </a>
                        <a href="#recursos" class="inline-flex items-center justify-center bg-white text-gray-700 px-8 py-4 rounded-lg text-lg font-semibold border-2 border-gray-300 hover:border-green-600 transition">
                            Saiba Mais
                        </a>
                    </div>
                    <p class="mt-4 text-sm text-gray-500">✓ Sem cartão de crédito para teste • ✓ Cancele quando quiser</p>
                </div>
                <div class="hidden lg:block">
                    <div class="bg-white rounded-2xl shadow-2xl p-8 transform rotate-2">
                        <div class="bg-green-100 rounded-xl p-6 mb-4">
                            <p class="text-gray-800 font-semibold mb-2">💬 Cliente</p>
                            <p class="text-gray-700">Olá! Gostaria de agendar um horário</p>
                        </div>
                        <div class="bg-blue-100 rounded-xl p-6">
                            <p class="text-gray-800 font-semibold mb-2">🤖 ZapBy</p>
                            <p class="text-gray-700">✅ Agendamento confirmado para amanhã às 14:00!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Recursos -->
    <section id="recursos" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Tudo que você precisa para gerenciar sua agenda</h2>
                <p class="text-xl text-gray-600">Recursos profissionais em uma solução simples</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Recurso 1 -->
                <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition">
                    <div class="bg-green-100 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Agendamento via WhatsApp</h3>
                    <p class="text-gray-600">Seus clientes agendam direto pelo WhatsApp. Simples, rápido e sem precisar baixar apps.</p>
                </div>

                <!-- Recurso 2 -->
                <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition">
                    <div class="bg-blue-100 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Lembretes Automáticos</h3>
                    <p class="text-gray-600">Sistema envia lembretes automáticos antes do horário agendado. Reduza faltas!</p>
                </div>

                <!-- Recurso 3 -->
                <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition">
                    <div class="bg-purple-100 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Gestão Completa</h3>
                    <p class="text-gray-600">Painel completo para visualizar, cancelar e gerenciar todos os seus agendamentos.</p>
                </div>

                <!-- Recurso 4 -->
                <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition">
                    <div class="bg-yellow-100 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Horários Flexíveis</h3>
                    <p class="text-gray-600">Configure seus horários de atendimento e bloqueie períodos indisponíveis facilmente.</p>
                </div>

                <!-- Recurso 5 -->
                <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition">
                    <div class="bg-red-100 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Múltiplos Serviços</h3>
                    <p class="text-gray-600">Cadastre diferentes serviços com preços e durações personalizadas.</p>
                </div>

                <!-- Recurso 6 -->
                <div class="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition">
                    <div class="bg-indigo-100 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Link Personalizado</h3>
                    <p class="text-gray-600">Compartilhe seu link exclusivo nas redes sociais. Seus clientes agendam na hora!</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Como Funciona -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Como funciona?</h2>
                <p class="text-xl text-gray-600">Comece a usar em 3 passos simples</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-green-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">1</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Cadastre-se</h3>
                    <p class="text-gray-600">Registre seu WhatsApp e configure seus horários de atendimento.</p>
                </div>

                <div class="text-center">
                    <div class="bg-green-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">2</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Compartilhe</h3>
                    <p class="text-gray-600">Receba seu link exclusivo e compartilhe com seus clientes.</p>
                </div>

                <div class="text-center">
                    <div class="bg-green-600 text-white w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">3</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Pronto!</h3>
                    <p class="text-gray-600">Seus clientes agendam pelo WhatsApp e você recebe tudo organizado.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Preço -->
    <section id="preco" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Plano Simples e Transparente</h2>
                <p class="text-xl text-gray-600">Sem surpresas, sem taxas escondidas</p>
            </div>

            <div class="max-w-lg mx-auto">
                <div class="bg-gradient-to-br from-green-50 to-blue-50 rounded-2xl p-8 shadow-xl border-4 border-green-500">
                    <div class="text-center mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Plano Profissional</h3>
                        <div class="flex items-center justify-center">
                            <span class="text-5xl font-bold text-green-600">R$ 19</span>
                            <span class="text-2xl text-gray-600">,90/mês</span>
                        </div>
                        <p class="text-gray-600 mt-2">7 dias grátis para testar</p>
                    </div>

                    <ul class="space-y-4 mb-8">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">Agendamentos ilimitados</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">Notificações automáticas por WhatsApp</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">Múltiplos serviços e preços</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">Painel de controle completo</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">Link personalizado</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="text-gray-700">Suporte via WhatsApp</span>
                        </li>
                    </ul>

                    <a href="{{ route('professional.register') }}" class="block w-full bg-green-600 text-white text-center px-8 py-4 rounded-lg text-lg font-semibold hover:bg-green-700 transition">
                        Começar Teste Grátis
                    </a>
                    <p class="text-center text-sm text-gray-600 mt-4">Sem cartão de crédito • Cancele quando quiser</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Final -->
    <section class="py-20 bg-gradient-to-br from-green-600 to-blue-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">Pronto para profissionalizar seus agendamentos?</h2>
            <p class="text-xl text-white mb-8 opacity-90">Comece seu teste grátis de 7 dias agora mesmo. Sem cartão de crédito!</p>
            <a href="{{ route('professional.register') }}" class="inline-flex items-center bg-white text-green-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition">
                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                </svg>
                Começar Agora Grátis
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-2xl font-bold text-green-400 mb-4">ZapBy</h3>
                    <p class="text-gray-400">Sistema profissional de agendamentos via WhatsApp</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Links Rápidos</h4>
                    <ul class="space-y-2">
                        <li><a href="#recursos" class="text-gray-400 hover:text-white transition">Recursos</a></li>
                        <li><a href="#preco" class="text-gray-400 hover:text-white transition">Preço</a></li>
                        <li><a href="{{ route('professional.register') }}" class="text-gray-400 hover:text-white transition">Cadastrar</a></li>
                        <li><a href="{{ route('professional.login') }}" class="text-gray-400 hover:text-white transition">Entrar</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Suporte</h4>
                    <p class="text-gray-400 mb-2">Dúvidas? Fale conosco:</p>
                    <a href="https://wa.me/5532999612957" target="_blank" class="inline-flex items-center text-green-400 hover:text-green-300 transition">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                        (32) 99961-2957
                    </a>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} ZapBy. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Banner de Instalação PWA (Mobile) -->
    <div id="install-banner" class="hidden fixed bottom-0 left-0 right-0 bg-green-600 text-white shadow-lg z-50">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 flex-1">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <div>
                        <p class="font-semibold">Instalar ZapBy</p>
                        <p class="text-sm text-green-100">Adicione à tela inicial para acesso rápido</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <button id="install-button" class="bg-white text-green-600 px-4 py-2 rounded-lg font-semibold hover:bg-green-50 transition">
                        Instalar
                    </button>
                    <button id="close-banner" class="text-white p-2 hover:bg-green-700 rounded-lg transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Registrar Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => console.log('Service Worker registrado'))
                    .catch(err => console.log('Erro ao registrar Service Worker:', err));
            });
        }

        // PWA Install Banner
        let deferredPrompt;
        const installBanner = document.getElementById('install-banner');
        const installButton = document.getElementById('install-button');
        const closeBanner = document.getElementById('close-banner');

        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevenir o prompt automático
            e.preventDefault();
            deferredPrompt = e;
            
            // Mostrar banner personalizado (apenas mobile)
            if (window.innerWidth <= 768) {
                installBanner.classList.remove('hidden');
            }
        });

        installButton.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                console.log('PWA instalado');
            }
            
            deferredPrompt = null;
            installBanner.classList.add('hidden');
        });

        closeBanner.addEventListener('click', () => {
            installBanner.classList.add('hidden');
            // Guardar preferência para não mostrar novamente nesta sessão
            sessionStorage.setItem('installBannerClosed', 'true');
        });

        // Verificar se o banner já foi fechado nesta sessão
        if (sessionStorage.getItem('installBannerClosed') === 'true') {
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                // Não mostrar o banner
            });
        }

        // Detectar quando o app foi instalado
        window.addEventListener('appinstalled', () => {
            console.log('ZapBy instalado com sucesso!');
            installBanner.classList.add('hidden');
        });
    </script>

</body>
</html>
