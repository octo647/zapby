@extends('layouts.professional')

@section('title', 'Assinatura')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <h2 class="text-3xl font-bold text-gray-900 mb-8">Minha Assinatura</h2>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <p class="text-green-700">✅ {{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
            @foreach($errors->all() as $error)
                <p class="text-red-700">❌ {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Status da Assinatura -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Status da Assinatura</h3>
        </div>
        <div class="p-6">
            @if(!$subscription)
                <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <span class="text-4xl">⚠️</span>
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="text-lg font-semibold text-red-800 mb-2">Assinatura Não Encontrada</h4>
                            <p class="text-red-700 mb-4">
                                Não foi possível encontrar sua assinatura. Por favor, entre em contato com nosso suporte para resolver este problema.
                            </p>
                            <a 
                                href="https://wa.me/5532987007302?text=Olá! Minha assinatura não foi encontrada no sistema Zapby. Meu cadastro: {{ Auth::guard('professional')->user()->email }}" 
                                target="_blank"
                                class="inline-flex items-center bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition"
                            >
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                </svg>
                                Falar com Suporte via WhatsApp
                            </a>
                        </div>
                    </div>
                </div>
            @elseif($subscription->status === 'trial')
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <span class="text-4xl">🎉</span>
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="text-lg font-semibold text-yellow-800 mb-2">Período de Teste Ativo</h4>
                            <p class="text-yellow-700 mb-3">
                                Você está usando o Zapby gratuitamente até 
                                <strong>{{ $professional->trial_ends_at->format('d/m/Y') }}</strong>
                                ({{ $professional->trial_ends_at->diffForHumans() }})
                            </p>
                            <p class="text-sm text-yellow-600 mb-4">
                                Após o período de teste, você pode ativar a assinatura mensal de <strong>R$ 19,90/mês</strong> 
                                para continuar usando todas as funcionalidades.
                            </p>
                            <form method="POST" action="{{ route('professional.subscription.activate') }}">
                                @csrf
                                <button 
                                    type="submit"
                                    class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition"
                                >
                                    💳 Ativar Assinatura - R$ 19,90/mês
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @elseif($subscription->status === 'active')
                <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <span class="text-4xl">✅</span>
                        </div>
                        <div class="ml-4 flex-1">
                            <h4 class="text-lg font-semibold text-green-800 mb-2">Assinatura Ativa</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-green-600">Valor Mensal</p>
                                    <p class="text-2xl font-bold text-green-800">R$ {{ number_format($subscription->amount, 2, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-green-600">Próximo Vencimento</p>
                                    <p class="text-2xl font-bold text-green-800">
                                        {{ \Carbon\Carbon::parse($subscription->next_due_date)->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                            @if($asaasData)
                                <p class="text-sm text-green-700 mb-4">
                                    ID da Assinatura: {{ $subscription->asaas_subscription_id }}
                                </p>
                            @endif
                            <form method="POST" action="{{ route('professional.subscription.cancel') }}" 
                                  onsubmit="return confirm('Tem certeza que deseja cancelar sua assinatura? Você perderá acesso ao sistema após o período pago.')">
                                @csrf
                                <button 
                                    type="submit"
                                    class="bg-red-100 text-red-700 px-6 py-2 rounded-lg font-semibold hover:bg-red-200 transition"
                                >
                                    Cancelar Assinatura
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @elseif($subscription->status === 'cancelled')
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <span class="text-4xl">❌</span>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-800 mb-2">Assinatura Cancelada</h4>
                            <p class="text-gray-700">
                                Sua assinatura foi cancelada. Você ainda pode usar o sistema até o fim do período pago.
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($subscription->status === 'suspended')
                <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <span class="text-4xl">⚠️</span>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-red-800 mb-2">Assinatura Suspensa</h4>
                            <p class="text-red-700 mb-4">
                                Sua assinatura foi suspensa por falta de pagamento. 
                                Por favor, regularize o pagamento para continuar usando o sistema.
                            </p>
                            @if($subscription->next_due_date)
                                <p class="text-sm text-red-600">
                                    Último vencimento: {{ \Carbon\Carbon::parse($subscription->next_due_date)->format('d/m/Y') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Histórico de Pagamentos -->
    @if($payments && count($payments) > 0)
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">Histórico de Pagamentos</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Valor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($payments as $payment)
                            @php
                                $statusColors = [
                                    'PENDING' => 'bg-yellow-100 text-yellow-800',
                                    'RECEIVED' => 'bg-green-100 text-green-800',
                                    'CONFIRMED' => 'bg-green-100 text-green-800',
                                    'OVERDUE' => 'bg-red-100 text-red-800',
                                    'REFUNDED' => 'bg-gray-100 text-gray-800',
                                ];
                                $statusLabels = [
                                    'PENDING' => 'Pendente',
                                    'RECEIVED' => 'Recebido',
                                    'CONFIRMED' => 'Confirmado',
                                    'OVERDUE' => 'Vencido',
                                    'REFUNDED' => 'Reembolsado',
                                ];
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($payment['dueDate'])->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    R$ {{ number_format($payment['value'], 2, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusColors[$payment['status']] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$payment['status']] ?? $payment['status'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $payment['billingType'] === 'PIX' ? 'PIX' : ($payment['billingType'] === 'BOLETO' ? 'Boleto' : $payment['billingType']) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($payment['status'] === 'PENDING')
                                        <a href="{{ route('professional.subscription.payment', $payment['id']) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 font-semibold">
                                            Ver Detalhes
                                        </a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Benefícios da Assinatura -->
    <div class="mt-6 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-6">
        <h4 class="text-lg font-bold text-gray-900 mb-4">✨ O que está incluído na sua assinatura:</h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-start">
                <span class="text-green-600 mr-2">✅</span>
                <p class="text-gray-700">Agendamentos ilimitados</p>
            </div>
            <div class="flex items-start">
                <span class="text-green-600 mr-2">✅</span>
                <p class="text-gray-700">Notificações automáticas por WhatsApp</p>
            </div>
            <div class="flex items-start">
                <span class="text-green-600 mr-2">✅</span>
                <p class="text-gray-700">Painel de controle completo</p>
            </div>
            <div class="flex items-start">
                <span class="text-green-600 mr-2">✅</span>
                <p class="text-gray-700">Link personalizado para compartilhar</p>
            </div>
            <div class="flex items-start">
                <span class="text-green-600 mr-2">✅</span>
                <p class="text-gray-700">Gestão de horários e bloqueios</p>
            </div>
            <div class="flex items-start">
                <span class="text-green-600 mr-2">✅</span>
                <p class="text-gray-700">Suporte por e-mail</p>
            </div>
        </div>
    </div>

</div>
@endsection
