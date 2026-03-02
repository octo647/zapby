@extends('layouts.professional')

@section('title', 'Agendamentos')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Meus Agendamentos</h2>
    </div>
    
    <!-- Link de Agendamento (Colapsável) -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg overflow-hidden mb-6">
        <!-- Header Clicável -->
        <button 
            onclick="toggleBookingLink()"
            class="w-full text-left p-6 text-white hover:bg-white/10 transition-colors flex items-center justify-between"
        >
            <h3 class="text-xl font-bold">📱 Compartilhe seu Link de Agendamento</h3>
            <svg id="bookingLinkIcon" class="w-6 h-6 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        
        <!-- Conteúdo Expansível (oculto por padrão) -->
        <div id="bookingLinkContent" class="hidden">
            <div class="px-6 pb-6">
                <div class="bg-white/20 backdrop-blur-sm rounded-lg p-6 border-2 border-white/30">
                    <div class="flex flex-col gap-4">
                        <div>
                            <p class="text-sm text-indigo-100 mb-2">Link de Agendamento</p>
                            <p class="text-sm text-indigo-200 mb-4">
                                Compartilhe este link com seus clientes. Ao clicar, eles serão direcionados ao WhatsApp com sua chave já preenchida!
                            </p>
                            
                            <div class="bg-white/10 rounded-lg p-4 mb-4 break-all font-mono text-sm">
                                https://wa.me/553298294948?text={{ $professional->access_key }}
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <button 
                                onclick="copyBookingLink()"
                                class="bg-white text-indigo-600 px-6 py-3 rounded-lg font-semibold hover:bg-indigo-50 transition shadow-lg"
                            >
                                📋 Copiar Link
                            </button>
                            
                            <button 
                                onclick="shareWhatsApp()"
                                class="bg-green-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-600 transition shadow-lg"
                            >
                                💬 WhatsApp
                            </button>
                            
                            <button 
                                onclick="shareFacebook()"
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition shadow-lg"
                            >
                                📘 Facebook
                            </button>
                            
                            <button 
                                onclick="shareInstagram()"
                                class="bg-gradient-to-r from-purple-500 via-pink-500 to-orange-500 text-white px-6 py-3 rounded-lg font-semibold hover:from-purple-600 hover:via-pink-600 hover:to-orange-600 transition shadow-lg"
                            >
                                📸 Instagram
                            </button>
                        </div>
                        
                        <div class="text-xs text-indigo-300 mt-2">
                            🔑 Sua chave: <strong class="text-white">{{ $professional->access_key }}</strong> | 
                            WhatsApp Bot: <strong class="text-white">(32) 98294-948</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($appointments->count() > 0)
        <!-- Desktop View - Tabela -->
        <div class="hidden md:block bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Horário
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cliente
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Serviço
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                WhatsApp
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Duração
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($appointments as $appointment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($appointment->date)->translatedFormat('l') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-indigo-100 rounded-full p-2 mr-3">
                                            <span class="text-lg">👤</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $appointment->client_name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $appointment->service ?? '-' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        // Usar client_whatsapp que contém o número correto
                                        $phoneNumber = $appointment->client_whatsapp ?? '';
                                        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
                                        $isValidPhone = $phoneNumber && strlen($phoneNumber) >= 10 && strlen($phoneNumber) <= 15;
                                    @endphp
                                    @if($isValidPhone)
                                        <a href="https://wa.me/{{ $phoneNumber }}" 
                                           target="_blank"
                                           class="text-green-600 hover:text-green-700 text-sm flex items-center gap-1">
                                            <span>💬</span>
                                            <span>Contatar</span>
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">Número indisponível</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $appointment->duration }} min
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'scheduled' => 'bg-blue-100 text-blue-800',
                                            'confirmed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            'completed' => 'bg-gray-100 text-gray-800',
                                        ];
                                        $statusLabels = [
                                            'scheduled' => 'Agendado',
                                            'confirmed' => 'Confirmado',
                                            'cancelled' => 'Cancelado',
                                            'completed' => 'Concluído',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$appointment->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$appointment->status] ?? $appointment->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($appointment->status !== 'cancelled' && $appointment->status !== 'completed')
                                        <form method="POST" action="{{ route('professional.appointments.cancel', $appointment->id) }}" class="inline">
                                            @csrf
                                            <button 
                                                type="submit"
                                                onclick="return confirm('Tem certeza que deseja cancelar este agendamento?')"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Cancelar
                                            </button>
                                        </form>
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

        <!-- Mobile View - Cards -->
        <div class="block md:hidden space-y-4">
            @foreach($appointments as $appointment)
                @php
                    $statusColors = [
                        'scheduled' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'confirmed' => 'bg-green-100 text-green-800 border-green-200',
                        'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                        'completed' => 'bg-gray-100 text-gray-800 border-gray-200',
                    ];
                    $statusLabels = [
                        'scheduled' => 'Agendado',
                        'confirmed' => 'Confirmado',
                        'cancelled' => 'Cancelado',
                        'completed' => 'Concluído',
                    ];
                @endphp
                
                <div class="bg-white rounded-lg shadow-md border-l-4 {{ str_replace('bg-', 'border-', explode(' ', $statusColors[$appointment->status] ?? 'bg-gray-100')[0]) }} overflow-hidden">
                    <div class="p-4">
                        <!-- Header do card com data e status -->
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="text-lg font-bold text-gray-900">
                                    {{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($appointment->date)->translatedFormat('l') }}
                                </div>
                            </div>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusColors[$appointment->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$appointment->status] ?? $appointment->status }}
                            </span>
                        </div>

                        <!-- Horário -->
                        <div class="flex items-center gap-2 mb-3 bg-indigo-50 rounded-lg p-3">
                            <span class="text-2xl">🕐</span>
                            <div>
                                <div class="text-xs text-gray-500">Horário</div>
                                <div class="text-lg font-bold text-indigo-700">
                                    {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }}
                                </div>
                            </div>
                            <div class="ml-auto">
                                <div class="text-xs text-gray-500">Duração</div>
                                <div class="text-sm font-semibold text-gray-700">
                                    {{ $appointment->duration }} min
                                </div>
                            </div>
                        </div>

                        <!-- Cliente -->
                        <div class="flex items-center gap-3 mb-3 pb-3 border-b border-gray-200">
                            <div class="bg-indigo-100 rounded-full p-2">
                                <span class="text-xl">👤</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-xs text-gray-500">Cliente</div>
                                <div class="text-base font-semibold text-gray-900">
                                    {{ $appointment->client_name }}
                                </div>
                            </div>
                        </div>
                        <!-- Serviço -->
                        @if($appointment->service)
                        <div class="flex items-center gap-3 mb-3 pb-3 border-b border-gray-200">
                            <div class="bg-purple-100 rounded-full p-2">
                                <span class="text-xl">💼</span>
                            </div>
                            <div class="flex-1">
                                <div class="text-xs text-gray-500">Serviço</div>
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $appointment->service }}
                                </div>
                            </div>
                        </div>
                        @endif
                        <!-- Ações -->
                        <div class="flex gap-2">
                            @php
                                // Usar client_whatsapp que contém o número correto
                                $phoneNumberCard = $appointment->client_whatsapp ?? '';
                                $phoneNumberCard = preg_replace('/[^0-9]/', '', $phoneNumberCard);
                                $isValidPhoneCard = $phoneNumberCard && strlen($phoneNumberCard) >= 10 && strlen($phoneNumberCard) <= 15;
                            @endphp
                            
                            @if($isValidPhoneCard)
                                <a href="https://wa.me/{{ $phoneNumberCard }}" 
                                   target="_blank"
                                   class="flex-1 bg-green-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-600 transition text-center">
                                    💬 Contatar
                                </a>
                            @else
                                <div class="flex-1 bg-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm font-semibold text-center cursor-not-allowed">
                                    💬 Número indisponível
                                </div>
                            @endif
                            
                            @if($appointment->status !== 'cancelled' && $appointment->status !== 'completed')
                                <form method="POST" action="{{ route('professional.appointments.cancel', $appointment->id) }}" class="flex-1">
                                    @csrf
                                    <button 
                                        type="submit"
                                        onclick="return confirm('Tem certeza que deseja cancelar este agendamento?')"
                                        class="w-full bg-red-500 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-red-600 transition"
                                    >
                                        🚫 Cancelar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $appointments->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <span class="text-8xl mb-6 block">📅</span>
            <h3 class="text-2xl font-semibold text-gray-700 mb-3">Nenhum agendamento ainda</h3>
            <p class="text-gray-500 mb-6">
                Compartilhe seu link de agendamento com seus clientes para começar a receber agendamentos!
            </p>
        </div>
    @endif

</div>

@push('scripts')
<script>
const professionalName = '{{ $professional->name }}';
const bookingLink = 'https://wa.me/553298294948?text={{ $professional->access_key }}';

function copyBookingLink() {
    navigator.clipboard.writeText(bookingLink).then(() => {
        alert('✅ Link copiado!\n\nCompartilhe este link com seus clientes. Ao clicar, eles serão direcionados ao WhatsApp e poderão agendar automaticamente!');
    }).catch(() => {
        // Fallback para navegadores antigos
        const textarea = document.createElement('textarea');
        textarea.value = bookingLink;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('✅ Link copiado!');
    });
}

function shareWhatsApp() {
    const message = `📅 *Agende comigo pelo WhatsApp!*\n\n` +
                   `Sou ${professionalName} e agora você pode marcar seu horário direto pelo WhatsApp!\n\n` +
                   `👉 Clique no link abaixo:\n${bookingLink}\n\n` +
                   `É rápido, fácil e automático! 🚀`;
    
    const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(message)}`;
    window.open(whatsappUrl, '_blank');
}

function shareFacebook() {
    const facebookUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(bookingLink)}`;
    window.open(facebookUrl, '_blank', 'width=600,height=400');
}

function shareInstagram() {
    const message = `📅 Agende comigo pelo WhatsApp!\n\n` +
                   `Clique no link do meu perfil ou use este:\n${bookingLink}\n\n` +
                   `#agendamento #${professionalName.replace(/\s+/g, '')}`;
    
    navigator.clipboard.writeText(message).then(() => {
        alert('✅ Texto copiado para área de transferência!\n\n' +
              'Cole este texto em uma publicação ou story no Instagram.\n\n' +
              '💡 Dica: Adicione o link na sua bio do Instagram e mencione nos stories!');
    }).catch(() => {
        const textarea = document.createElement('textarea');
        textarea.value = message;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('✅ Texto copiado! Cole no Instagram.');
    });
}

function toggleBookingLink() {
    const content = document.getElementById('bookingLinkContent');
    const icon = document.getElementById('bookingLinkIcon');
    
    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
    }
}
</script>
@endpush

@endsection
