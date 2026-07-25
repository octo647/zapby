@if(!$professional->onboarding_completed && count($professional->getPendingOnboardingSteps()) > 0)
@php
    $isSettingsPage = request()->routeIs('professional.settings');
@endphp
<div class="bg-gradient-to-r from-blue-500 via-indigo-600 to-purple-600 rounded-lg shadow-lg overflow-hidden mb-6 animate-pulse-slow">
    <div class="p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row items-start gap-4">
            <div class="flex-shrink-0">
                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-white rounded-full flex items-center justify-center text-3xl">
                    🚀
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-xl sm:text-2xl font-bold text-white mb-2">
                    @if($isSettingsPage)
                        Você está na página de configurações
                    @else
                        Bem-vindo ao Zapby! Complete sua configuração inicial
                    @endif
                </h3>
                <p class="text-indigo-100 mb-4">
                    @if($isSettingsPage)
                        Use os botões abaixo para ir direto ao bloco que falta configurar.
                    @else
                        Siga os passos abaixo para começar a receber agendamentos dos seus clientes:
                    @endif
                </p>
                
                <div class="space-y-3">
                    @foreach($professional->getPendingOnboardingSteps() as $index => $step)
                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border-2 border-white/30 hover:bg-white/20 transition-all">
                            <div>
                                <div class="flex items-start gap-3">
                                    <span class="text-3xl">{{ $step['icon'] }}</span>
                                    <div class="flex-1">
                                        <h4 class="text-white font-semibold text-base sm:text-lg">
                                            {{ $index + 1 }}. {{ $step['title'] }}
                                        </h4>
                                        <p class="text-indigo-100 text-sm">
                                            {{ $step['description'] }}
                                        </p>
                                    </div>
                                </div>
                                <a 
                                    href="{{ route($step['route']) }}{{ $step['anchor'] ?? '' }}" 
                                    class="mt-3 inline-flex w-full sm:w-auto justify-center bg-white text-indigo-600 px-6 py-2 rounded-lg font-semibold hover:bg-indigo-50 transition shadow-lg whitespace-nowrap"
                                >
                                    @if($step['id'] === 'share_link')
                                        Compartilhar
                                    @elseif($isSettingsPage)
                                        Configurar
                                    @else
                                        Configurar
                                    @endif
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($professional->hasCompletedBasicSettings())
                    <div class="mt-4 flex items-center gap-2">
                        <form method="POST" action="{{ route('professional.onboarding.complete') }}" class="inline">
                            @csrf
                            <button 
                                type="submit"
                                class="text-white text-sm underline hover:text-indigo-100 transition"
                            >
                                Não mostrar mais este aviso
                            </button>
                        </form>
                    </div>
                @endif
            </div>
            
            @if($professional->hasCompletedBasicSettings())
                <form method="POST" action="{{ route('professional.onboarding.complete') }}" class="flex-shrink-0">
                    @csrf
                    <button 
                        type="submit"
                        class="text-white hover:text-indigo-100 transition text-2xl"
                        title="Fechar"
                    >
                        ✕
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

<style>
    @keyframes pulse-slow {
        0%, 100% {
            box-shadow: 0 0 20px rgba(99, 102, 241, 0.5);
        }
        50% {
            box-shadow: 0 0 30px rgba(99, 102, 241, 0.8);
        }
    }
    
    .animate-pulse-slow {
        animation: pulse-slow 3s ease-in-out infinite;
    }
</style>
@endif
