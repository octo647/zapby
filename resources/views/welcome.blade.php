<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
    <meta name="description" content="Sistema de agendamentos pelo WhatsApp. Envie confirmações automáticas e receba agendamentos 24h por dia.">
    <meta name="keywords" content="agendamento whatsapp, agenda online, sistema de agendamento, lembrete automático whatsapp, gestão de agenda profissional">
    <meta name="author" content="Heco Softwares Ltda">
    <meta name="facebook-domain-verification" content="lz30s2g8o5pn8qdmxj3zeg6rjwxj6z" />
    <link rel="canonical" href="{{ url()->current() }}">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:site_name" content="ZapBy">
    <meta property="og:title" content="ZapBy - Sistema de Agendamento pelo WhatsApp">
    <meta property="og:description" content="Automatize agendamentos via WhatsApp, reduza faltas com lembretes automáticos e organize sua agenda profissional em minutos.">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ url('/logo_fundo_branco.png') }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="ZapBy - Agendamentos Profissionais pelo WhatsApp">
    <meta name="twitter:description" content="Sistema de agendamentos pelo WhatsApp com lembretes automáticos e painel profissional.">
    <meta name="twitter:image" content="{{ url('/logo_fundo_branco.png') }}">
    
    <title>ZapBy - Sistema de Agendamento pelo WhatsApp</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#16a34a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="ZapBy">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/logo_fundo_branco.png">

    <script src="https://cdn.tailwindcss.com"></script>
  

<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Preciso informar cartão de crédito para testar?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Não. Você pode testar grátis por 7 dias sem informar cartão de crédito."
      }
    },
    {
      "@type": "Question",
      "name": "O ZapBy substitui meu WhatsApp?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Não. Seu WhatsApp continua sendo seu. O ZapBy apenas ajuda seus clientes a agendarem de forma organizada."
      }
    },
    {
      "@type": "Question",
      "name": "Preciso trocar o meu número de WhatsApp?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Não. Seu WhatsApp continua o mesmo."
      }
    },
    {
      "@type": "Question",
      "name": "Meus clientes precisam instalar algum aplicativo?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Não. O agendamento é feito inteiramente pelo WhatsApp."
      }
    },
    {
      "@type": "Question",
      "name": "Posso continuar atendendo normalmente pelo WhatsApp?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Sim. Seu WhatsApp não é afetado em nada por estar usando o ZapBy."
      }
    },
    {
      "@type": "Question",
      "name": "O que acontece após o teste grátis?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Após o período de 7 dias grátis, se não quiser continuar usando o ZapBy, os agendamentos serão interrompidos e você não terá de pagar nada."
      }
    },
    {
      "@type": "Question",
      "name": "Posso cancelar quando quiser?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Sim. O serviço de agendamento do ZapBy pode ser cancelado a qualquer momento sem nenhuma taxa extra."
      }
    },
    {
      "@type": "Question",
      "name": "Funciona para barbeiros?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Sim. O ZapBy permite que barbeiros organizem cortes de cabelo, barba e outros serviços diretamente pelo WhatsApp. Os clientes escolhem horários disponíveis sem necessidade de ligação ou troca de mensagens."
      }
    },
    {
      "@type": "Question",
      "name": "Funciona para manicures?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Sim. O ZapBy ajuda manicures e profissionais de estética a organizar horários, reduzir faltas e enviar lembretes automáticos antes dos atendimentos."
      }
    },
    {
      "@type": "Question",
      "name": "Como funciona o agendamento?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "O profissional cadastra seus horários e serviços. Em seguida recebe um link exclusivo para compartilhar com os clientes. O cliente escolhe o serviço, seleciona um horário disponível e confirma o agendamento diretamente pelo WhatsApp. O ZapBy registra o compromisso e envia lembretes automáticos."
      }
    }
  ]
}
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.video-demonstracao-container').forEach((container) => {
        const video = container.querySelector('.video-demonstracao');
        const botao = container.querySelector('.video-demonstracao-play');

        botao.addEventListener('click', () => video.play());
        video.addEventListener('click', () => video.paused ? video.play() : video.pause());
        video.addEventListener('play', () => botao.classList.add('hidden'));
        video.addEventListener('pause', () => botao.classList.remove('hidden'));

        video.addEventListener('ended', () => {
            const recuo = 0.08;

            if (Number.isFinite(video.duration) && video.duration > recuo) {
                video.currentTime = video.duration - recuo;
                video.pause();
            }
        });
    });
});
</script>
    
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

    @include('partials.facebook-pixel')

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "SoftwareApplication",
            "name": "ZapBy",
            "applicationCategory": "BusinessApplication",
            "operatingSystem": "Web",
            "url": "{{ url('/') }}",
            "description": "Sistema de agendamento via WhatsApp para profissionais, com lembretes automáticos e gestão de horários.",
            "offers": {
                "@type": "Offer",
                "price": "19.90",
                "priceCurrency": "BRL"
            },
            "provider": {
                "@type": "Organization",
                "name": "Heco Softwares Ltda"
            }
        }
    </script>




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
    <section class="pt-32 pb-20 " style="background-color: #eff6ff;">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h1 class="text-5xl font-bold text-gray-900 mb-6">
                        Agendamentos Profissionais <span class="text-green-600">pelo WhatsApp</span>
                    </h1>
                    <p class="text-xl text-gray-600 mb-4">
                    Ideal para barbeiros, cabeleireiros, manicures, esteticistas e profissionais que recebem agendamentos pelo WhatsApp. Automatize confirmações, lembretes e organização da agenda.
                    </p>
                    <!--<p class="text-xl text-gray-600 mb-4">
                        Simplifique sua agenda! Seus clientes agendam pelo WhatsApp e você recebe confirmações automáticas. 
                        Sem complicação, sem apps extras.
                    </p>
                    
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg">
                        <p class="text-gray-800 flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span><strong>Seu WhatsApp continua sendo seu.</strong> O ZapBy é apenas uma forma alternativa para seus clientes agendarem, sem substituir seu atendimento pessoal.</span>
                        </p>
                    </div>
                    -->                    
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('professional.register') }}" onclick="fbq('track', 'Lead', {content_name: 'Hero CTA - Começar Grátis'});" class="inline-flex items-center justify-center bg-green-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-green-700 transition">
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
            <div class="text-center mb-10">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Tudo o que você precisa para gerenciar sua agenda</h2>
                <h4 class="text-3xl font-bold text-gray-900 mb-4">Veja o ZapBy em ação</h4>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Recurso 1 -->
                <div class="bg-blue-50 rounded-xl p-8 hover:shadow-lg transition">
                    <div class="bg-green-100 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Agendamento via WhatsApp</h3>
                    <p class="text-gray-600">Seus clientes agendam direto pelo WhatsApp. Simples, rápido e sem precisar baixar apps.</p>
                    <div class="video-demonstracao-container relative mt-6 mx-auto w-full max-w-[220px]">
                        <video
                            playsinline
                            preload="none"
                            poster="{{ asset('agendamento.png') }}"
                            class="video-demonstracao block w-full rounded-xl cursor-pointer"
                            aria-label="Demonstração do agendamento pelo WhatsApp"
                        >
                            <source src="{{ asset('videos/cliente2.mp4') }}" type="video/mp4">
                            Seu navegador não suporta vídeos.
                        </video>
                        <button type="button" class="video-demonstracao-play absolute inset-0 m-auto flex h-14 w-14 items-center justify-center rounded-full bg-green-600/90 text-white shadow-lg" aria-label="Reproduzir demonstração do agendamento pelo WhatsApp">
                            <svg class="ml-1 h-7 w-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Recurso 2 -->
                <div class="bg-blue-50 rounded-xl p-8 hover:shadow-lg transition">
                    <div class="bg-blue-100 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Lembretes Automáticos</h3>
                    <p class="text-gray-600">Sistema envia lembretes automáticos antes do horário agendado. Reduza faltas!</p>
                    <img src="{{ asset('lembrete.png') }}" alt="Demonstração do lembrete automático" class="mt-6 mx-auto 
                    block w-full max-w-[220px] ">
                </div>

                <!-- Recurso 3 -->
                <div class="bg-blue-50 rounded-xl p-8 hover:shadow-lg transition">
                    <div class="bg-purple-100 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Gestão Completa</h3>
                    <p class="text-gray-600">Painel completo para visualizar, cancelar e gerenciar todos os seus agendamentos.</p>
                    <div class="video-demonstracao-container relative mt-6 mx-auto w-full max-w-[220px]">
                        <video playsinline preload="none" poster="{{ asset('gestao.png') }}" class="video-demonstracao block w-full rounded-xl cursor-pointer" aria-label="Demonstração do painel profissional">
                            <source src="{{ asset('videos/profissional.mp4') }}" type="video/mp4">
                            Seu navegador não suporta vídeos.
                        </video>
                        <button type="button" class="video-demonstracao-play absolute inset-0 m-auto flex h-14 w-14 items-center justify-center rounded-full bg-green-600/90 text-white shadow-lg" aria-label="Reproduzir demonstração do painel profissional">
                            <svg class="ml-1 h-7 w-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Recurso 4 -->
                <div class="bg-blue-50 rounded-xl p-8 hover:shadow-lg transition">
                    <div class="bg-yellow-100 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Horários Flexíveis</h3>
                    <p class="text-gray-600">Configure seus horários de atendimento e bloqueie períodos indisponíveis facilmente.</p>
                    <div class="video-demonstracao-container relative mt-6 mx-auto w-full max-w-[220px]">
                        <video playsinline poster="{{ asset('agendas.png') }}" preload="none" class="video-demonstracao block w-full rounded-xl cursor-pointer" aria-label="Demonstração da configuração de dias e horários">
                            <source src="{{ asset('videos/agenda.mp4') }}" type="video/mp4">
                            Seu navegador não suporta vídeos.
                        </video>
                        <button type="button" class="video-demonstracao-play absolute inset-0 m-auto flex h-14 w-14 items-center justify-center rounded-full bg-green-600/90 text-white shadow-lg" aria-label="Reproduzir demonstração da configuração de dias e horários">
                            <svg class="ml-1 h-7 w-7" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Recurso 5 -->
                <div class="bg-blue-50 rounded-xl p-8 hover:shadow-lg transition">
                    <div class="bg-red-100 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Múltiplos Serviços</h3>
                    <p class="text-gray-600">Cadastre diferentes serviços com preços e durações personalizadas.</p>
                    <img src="{{ asset('servicos.png') }}" alt="Demonstração do lembrete automático" class="mt-6 mx-auto 
                    block w-full max-w-[220px] ">
                </div>

                <!-- Recurso 6 -->
                <div class="bg-blue-50 rounded-xl p-8 hover:shadow-lg transition">
                    <div class="bg-indigo-100 w-14 h-14 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Link Personalizado</h3>
                    <p class="text-gray-600">Compartilhe seu link exclusivo nas redes sociais. Seus clientes agendam na hora!</p>
                    <img src="{{ asset('link_personalizado.png') }}" alt="Demonstração do link personalizado" 
                    class="mt-6 mx-auto block w-full max-w-[220px] ">
                </div>
            </div>
        </div>
    </section>

    <!-- Tranquilidade / Segurança -->
    <section class="py-16 bg-gradient-to-br from-blue-50 to-green-50">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12 border-2 border-green-200">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Seu WhatsApp Continua Sendo Seu</h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                        <strong class="text-gray-900">O ZapBy não substitui seu WhatsApp.</strong> É apenas uma forma alternativa e organizada para seus clientes agendarem.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-green-50 rounded-xl p-6">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 mb-2">Você mantém o controle total</h3>
                                <p class="text-gray-700">Seus clientes continuam usando o WhatsApp normalmente. O ZapBy apenas organiza e automatiza os agendamentos.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 rounded-xl p-6">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 mb-2">Forma alternativa, não obrigatória</h3>
                                <p class="text-gray-700">Seus clientes podem escolher: agendar pelo link automático ou falar diretamente com você no WhatsApp.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 rounded-xl p-6">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 mb-2">Não perde nenhum cliente</h3>
                                <p class="text-gray-700">O ZapBy facilita a vida de quem prefere agendar rápido, mas mantém seu atendimento pessoal para quem prefere conversar.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 rounded-xl p-6">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 mb-2">Mais clientes, menos trabalho</h3>
                                <p class="text-gray-700">Atenda melhor quem precisa de atenção especial, enquanto agendamentos simples acontecem automaticamente.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 bg-blue-50 border-l-4 border-blue-500 p-6 rounded-r-lg">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-gray-800">
                            <strong class="text-gray-900">Pense no ZapBy como um assistente:</strong> ele cuida dos agendamentos automáticos 24/7, liberando você para focar no que realmente importa - atender bem seus clientes.
                        </p>
                    </div>
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

                    <a href="{{ route('professional.register') }}" onclick="fbq('track', 'Lead', {content_name: 'Pricing CTA'});" class="block w-full bg-green-600 text-white text-center px-8 py-4 rounded-lg text-lg font-semibold hover:bg-green-700 transition">
                        Começar Teste Grátis
                    </a>
                    <p class="text-center text-sm text-gray-600 mt-4">Sem cartão de crédito • Cancele quando quiser</p>
                </div>
            </div>
        </div>
    </section>
    <!-- Seção Vantagens do ZapBy -->
<section id="vantagens" class="py-10 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Por que usar um sistema de 
                agendamento pelo WhatsApp?</h2>            
        </div>
        <div class="space-y-2">
            <details class="bg-white rounded-xl p-4 shadow-sm">
                <summary class="font-semibold text-gray-900 cursor-pointer">
                    Evita perda de clientes
                </summary>
                <p class="mt-4 text-gray-600">
                  O atendimento pessoal no agendamento tem vantagens e desvantagens. A principal 
                  vantagem é o contato pessoal com o cliente, que torna o atendimento humano e 
                  amigável. Já a desvantagem é que você não está sempre disponível na hora que o 
                  cliente liga para agendar. Se você não atende na hora, muitas vezes retornar 
                  a ligação já não encontra o cliente disponível. E então, até calhar do cliente 
                  atender, pode passar muito tempo e ele perder o interesse. Esse problema 
                  de comunicação pode fazer com que você tenha dificuldade de aumentar o número de 
                  clientes ou mesmo chegar a perder clientes.  O ZapBy sempre atende na hora que o 
                  cliente liga, oferecendo seus horários disponíveis automaticamente. E depois que 
                  o cliente agenda, o ZapBy envia lembretes do agendamento, reduzindo as faltas por
                  esquecimento.
                </p>
            </details>
           <details class="bg-white rounded-xl p-4 shadow-sm">
                <summary class="font-semibold text-gray-900 cursor-pointer">
                    Evita o esquecimento de horários
                </summary>
                <p class="mt-4 text-gray-600">
                  Um problema diário que profissionais da área de beleza e saúde têm é falta do 
                  cliente no horário agendado. Muitas vezes o cliente simplesmente esquece do 
                  compromisso agendado, ou pode acontecer algum imprevisto, etc. Seja como for, 
                  isso resulta em prejuízo para o profissional que fica com o horário empatado. 
                  Para contornar esse problema, o ZapBy envia automaticamente dois lembretes 
                  para o cliente, um com 24 h e outro com 2 h de antecedência. 
                </p>
            </details>
            <details class="bg-white rounded-xl p-4 shadow-sm">
                <summary class="font-semibold text-gray-900 cursor-pointer">
                  Realiza atendimento fora do expediente
                </summary>
                <p class="mt-4 text-gray-600">
                  Os clientes ligam pelo WhatsApp em qualquer horário. Ligam na hora do almoço, 
                  quando você está descansando, quando está no trânsito... Por maior que seja 
                  sua boa vontade, em alguns momentos será impossível atender. O ZapBy está 
                  sempre disponível para o agendamento, 24 h por dia, 7 dias da semana. 
                </p>
            </details>
           <details class="bg-white rounded-xl p-4 shadow-sm">
                <summary class="font-semibold text-gray-900 cursor-pointer">
                   Organização da agenda
                </summary>
                <p class="mt-4 text-gray-600">
                  O ZapBy oferece a você uma agenda sempre organizada e fácil de consultar. 
                  Chega de agenda de papel, rabiscada e confusa!
                </p>
            </details>


</div>
</div>
</section>
    <!--Seção Perguntas Frequentes -->
<section id="faq" class="py-10 bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Perguntas Frequentes</h2>
            <p class="text-xl text-gray-600">Tire suas principais dúvidas antes de começar</p>
        </div>

        <div class="space-y-2">
            <details class="bg-white rounded-xl p-4 shadow-sm">
                <summary class="font-semibold text-gray-900 cursor-pointer">
                    Preciso informar cartão de crédito para testar?
                </summary>
                <p class="mt-4 text-gray-600">
                    Não. Você pode testar grátis por 7 dias sem informar cartão de crédito.
                </p>
            </details>

            <details class="bg-white rounded-xl p-4 shadow-sm">
                <summary class="font-semibold text-gray-900 cursor-pointer">
                    O ZapBy substitui meu WhatsApp?
                </summary>
                <p class="mt-4 text-gray-600">
                    Não. Seu WhatsApp continua sendo seu. O ZapBy apenas ajuda seus clientes a agendarem de forma organizada.
                </p>
            </details>
            <details class="bg-white rounded-xl p-4 shadow-sm">
                <summary class="font-semibold text-gray-900 cursor-pointer">
                    Preciso trocar o meu número de WhatsApp?
                </summary>
                <p class="mt-4 text-gray-600">
                    Não. Seu WhatsApp continua o mesmo.
                </p>
            </details>
            <details class="bg-white rounded-xl p-4 shadow-sm">
                <summary class="font-semibold text-gray-900 cursor-pointer">
                    Meus clientes precisam instalar algum aplicativo?
                </summary>
                <p class="mt-4 text-gray-600">
                    Não. O agendamento é feito inteiramente pelo WhatsApp.
                </p>
            </details>
            <details class="bg-white rounded-xl p-4 shadow-sm">
                <summary class="font-semibold text-gray-900 cursor-pointer">
                    Posso continuar atendendo normalmente pelo WhatsApp?
                </summary>
                <p class="mt-4 text-gray-600">
                    Sim. Seu WhatsApp não é afetado em nada por estar usando o ZapBy.
                </p>
            </details>           
            <details class="bg-white rounded-xl p-4 shadow-sm">
                <summary class="font-semibold text-gray-900 cursor-pointer">
                    O que acontece após o teste grátis?
                </summary>
                <p class="mt-4 text-gray-600">
                    Após o período de 7 dias grátis, se não quiser continuar 
                    usando o ZapBy, os agendamentos serão interrompidos e você não terá de pagar nada.
                </p>
            </details> 
            <details class="bg-white rounded-xl p-4 shadow-sm">
                <summary class="font-semibold text-gray-900 cursor-pointer">
                    Posso cancelar quando quiser?
                </summary>
                <p class="mt-4 text-gray-600">
                    Sim! O serviço de agendamento do ZapBy pode ser cancelado a qualquer momento 
                    sem nenhuma taxa extra.
                </p>
            </details> 
            <details class="bg-white rounded-xl p-4 shadow-sm">
                <summary class="font-semibold text-gray-900 cursor-pointer">
                    Funciona para barbeiros?
                </summary>
                <p class="mt-4 text-gray-600">
                   Sim. O ZapBy permite que barbeiros organizem cortes de cabelo, barba e outros
                   serviços diretamente pelo WhatsApp. Os clientes escolhem horários disponíveis
                   sem necessidade de ligação ou troca de mensagens.
                </p>
            </details> 
            <details class="bg-white rounded-xl p-4 shadow-sm">
                <summary class="font-semibold text-gray-900 cursor-pointer">
                    Funciona para manicures?
                </summary>
                <p class="mt-4 text-gray-600">
                   Sim. O ZapBy ajuda manicures e profissionais de estética a organizar horários,
                   reduzir faltas e enviar lembretes automáticos antes dos atendimentos.
                </p>
            </details>
           <details class="bg-white rounded-xl p-4 shadow-sm">
                <summary class="font-semibold text-gray-900 cursor-pointer">
                   Como funciona o agendamento?
                </summary>
                <p class="mt-4 text-gray-600">
                  O profissional cadastra seus horários e serviços. Em seguida recebe um link 
                  exclusivo para compartilhar com os clientes. O cliente escolhe o serviço, 
                  seleciona um horário disponível e confirma o agendamento diretamente pelo WhatsApp. 
                  O ZapBy registra o compromisso e envia lembretes automáticos.
                </p>
            </details>





        </div>
    </div>
</section>

    <!-- CTA Final -->
    <section class="py-20 bg-gradient-to-br from-green-600 to-blue-600">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">Pronto para profissionalizar seus agendamentos?</h2>
            <p class="text-xl text-white mb-8 opacity-90">Comece seu teste grátis de 7 dias agora mesmo. Sem cartão de crédito!</p>
            <a href="{{ route('professional.register') }}" onclick="fbq('track', 'Lead', {content_name: 'Footer CTA'});" class="inline-flex items-center bg-white text-green-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition">
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
                <p class="mb-2">&copy; {{ date('Y') }} ZapBy. Todos os direitos reservados.</p>
                <p class="text-sm text-gray-500">ZapBy é uma marca da <strong class="text-gray-400">Heco Softwares Ltda</strong></p>
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
        // Verificar se está em HTTPS (requisito para PWA)
        const isSecure = window.location.protocol === 'https:' || window.location.hostname === 'localhost';
        
        // Registrar Service Worker apenas em contexto seguro
        if ('serviceWorker' in navigator && isSecure) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js', { scope: '/' })
                    .then(reg => {
                        console.log('[PWA] Service Worker registrado com sucesso');
                        // Verificar atualizações
                        reg.update();
                    })
                    .catch(err => console.error('[PWA] Erro ao registrar Service Worker:', err));
            });
        } else if (!isSecure) {
            console.warn('[PWA] Service Worker requer HTTPS para funcionar');
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
            
            // Mostrar banner personalizado apenas em contexto seguro e mobile
            if (isSecure && window.innerWidth <= 768) {
                installBanner.classList.remove('hidden');
            }
        });

        installButton.addEventListener('click', async () => {
            if (!deferredPrompt) return;
            
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                console.log('[PWA] Aplicativo instalado com sucesso');
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
            window.addEventListener('befor
