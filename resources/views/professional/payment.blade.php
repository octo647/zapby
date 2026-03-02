@extends('layouts.professional')

@section('title', 'Detalhes do Pagamento')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-6">
        <a href="{{ route('professional.subscription') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
            ← Voltar para Assinatura
        </a>
    </div>

    <h2 class="text-3xl font-bold text-gray-900 mb-8">Detalhes do Pagamento</h2>

    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Valor</p>
                    <p class="text-2xl font-bold text-gray-900">
                        R$ {{ number_format($payment['value'], 2, ',', '.') }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Data de Vencimento</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ \Carbon\Carbon::parse($payment['dueDate'])->format('d/m/Y') }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Tipo de Pagamento</p>
                    <p class="text-lg font-semibold text-gray-800">
                        @if($payment['billingType'] === 'PIX')
                            PIX
                        @elseif($payment['billingType'] === 'BOLETO')
                            Boleto Bancário
                        @elseif($payment['billingType'] === 'CREDIT_CARD')
                            Cartão de Crédito
                        @else
                            {{ $payment['billingType'] }}
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 mb-1">Status</p>
                    @php
                        $statusColors = [
                            'PENDING' => 'bg-yellow-100 text-yellow-800',
                            'RECEIVED' => 'bg-green-100 text-green-800',
                            'CONFIRMED' => 'bg-green-100 text-green-800',
                            'OVERDUE' => 'bg-red-100 text-red-800',
                        ];
                        $statusLabels = [
                            'PENDING' => 'Aguardando Pagamento',
                            'RECEIVED' => 'Pagamento Recebido',
                            'CONFIRMED' => 'Pagamento Confirmado',
                            'OVERDUE' => 'Vencido',
                        ];
                    @endphp
                    <span class="inline-block px-4 py-2 text-sm font-semibold rounded-full {{ $statusColors[$payment['status']] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $statusLabels[$payment['status']] ?? $payment['status'] }}
                    </span>
                </div>
            </div>

            @if($payment['description'])
                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-1">Descrição</p>
                    <p class="text-gray-800">{{ $payment['description'] }}</p>
                </div>
            @endif

            <!-- PIX QR Code -->
            @if($payment['billingType'] === 'PIX' && $payment['status'] === 'PENDING' && $pixQrCode)
                <div class="mt-6 p-6 bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg border border-indigo-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 text-center">Pagar com PIX</h3>
                    
                    <div class="flex flex-col items-center mb-6">
                        @if(isset($pixQrCode['encodedImage']))
                            <img src="data:image/png;base64,{{ $pixQrCode['encodedImage'] }}" 
                                 alt="QR Code PIX"
                                 class="w-64 h-64 border-4 border-white rounded-lg shadow-lg mb-4">
                        @endif
                        <p class="text-sm text-gray-600 text-center max-w-md">
                            Escaneie o QR Code acima com o app do seu banco ou copie o código PIX abaixo
                        </p>
                    </div>

                    @if(isset($pixQrCode['payload']))
                        <div class="bg-white rounded-lg p-4">
                            <p class="text-xs font-medium text-gray-600 mb-2">Código PIX Copia e Cola:</p>
                            <div class="flex items-center gap-2">
                                <input 
                                    type="text" 
                                    id="pixCode"
                                    value="{{ $pixQrCode['payload'] }}"
                                    readonly
                                    class="flex-1 text-xs bg-gray-50 border border-gray-300 rounded px-3 py-2 font-mono"
                                >
                                <button 
                                    onclick="copyPixCode()"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition whitespace-nowrap"
                                >
                                    📋 Copiar
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                Válido até: {{ \Carbon\Carbon::parse($pixQrCode['expirationDate'] ?? $payment['dueDate'])->format('d/m/Y H:i') }}
                            </p>
                        </div>
                    @endif

                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-600">
                            O pagamento é processado instantaneamente após a confirmação
                        </p>
                    </div>
                </div>
            @endif

            <!-- Boleto -->
            @if($payment['billingType'] === 'BOLETO' && $payment['status'] === 'PENDING')
                <div class="mt-6 p-6 bg-yellow-50 rounded-lg border border-yellow-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Boleto Bancário</h3>
                    
                    @if(isset($payment['bankSlipUrl']))
                        <a 
                            href="{{ $payment['bankSlipUrl'] }}"
                            target="_blank"
                            class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition"
                        >
                            📄 Imprimir Boleto
                        </a>
                    @endif

                    <div class="mt-4 text-sm text-gray-600">
                        <p>O boleto pode levar até 3 dias úteis para compensação</p>
                    </div>
                </div>
            @endif

            @if($payment['status'] === 'RECEIVED' || $payment['status'] === 'CONFIRMED')
                <div class="mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-800">
                        ✅ Pagamento confirmado em {{ \Carbon\Carbon::parse($payment['paymentDate'] ?? $payment['clientPaymentDate'])->format('d/m/Y') }}
                    </p>
                </div>
            @endif

            @if($payment['status'] === 'OVERDUE')
                <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-800">
                        ⚠️ Este pagamento está vencido. Entre em contato com o suporte se tiver dúvidas.
                    </p>
                </div>
            @endif
        </div>
    </div>

    <div class="text-center text-sm text-gray-500">
        <p>ID do Pagamento: {{ $payment['id'] }}</p>
    </div>

</div>

<script>
function copyPixCode() {
    const pixCode = document.getElementById('pixCode');
    pixCode.select();
    document.execCommand('copy');
    
    alert('✅ Código PIX copiado! Cole no app do seu banco para pagar.');
}
</script>
@endsection
