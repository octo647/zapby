@extends('layouts.app')

@section('title', 'Painel - Zapby')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Header with link -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Olá, {{ $professional->name }}! 👋</h1>
        
        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-gray-700 mb-2">Seu link de agendamento:</p>
            <div class="flex flex-col sm:flex-row gap-2">
                <input 
                    type="text" 
                    value="{{ route('agenda.show', $professional) }}"
                    readonly
                    class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded text-sm"
                    id="agendaLink"
                >
                <button 
                    onclick="copyLink()"
                    class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition text-sm font-medium"
                >
                    Copiar Link
                </button>
                <a 
                    href="https://wa.me/?text={{ urlencode('Agende comigo: ' . route('agenda.show', $professional)) }}"
                    target="_blank"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm font-medium text-center"
                >
                    Compartilhar
                </a>
            </div>
        </div>

        @if($professional->isOnTrial())
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-sm text-yellow-800">
                    ⚠️ Você está no período de teste. Expira em {{ $professional->trial_ends_at->diffForHumans() }}.
                </p>
            </div>
        @endif
    </div>

    <!-- Navigation -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <nav class="flex overflow-x-auto">
            <a href="{{ route('dashboard') }}" class="px-6 py-4 text-indigo-600 border-b-2 border-indigo-600 font-medium whitespace-nowrap">
                Agendamentos
            </a>
            <a href="{{ route('dashboard.schedule') }}" class="px-6 py-4 text-gray-600 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 whitespace-nowrap">
                Horários
            </a>
            <a href="{{ route('dashboard.time-blocks') }}" class="px-6 py-4 text-gray-600 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 whitespace-nowrap">
                Bloqueios
            </a>
        </nav>
    </div>

    <!-- Today's Appointments -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Hoje ({{ now()->format('d/m/Y') }})</h2>
        
        @if($todayAppointments->count() > 0)
            <div class="space-y-3">
                @foreach($todayAppointments as $appointment)
                    <div class="border border-gray-200 rounded-lg p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-lg font-semibold text-gray-900">{{ $appointment->time->format('H:i') }}</span>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($appointment->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($appointment->status === 'completed') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800
                                    @endif
                                ">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                            <p class="text-gray-900 font-medium">{{ $appointment->client_name }}</p>
                            <p class="text-sm text-gray-600">{{ $appointment->client_whatsapp }}</p>
                        </div>
                        
                        <div class="flex gap-2">
                            @if($appointment->isPending() || $appointment->isConfirmed())
                                @if($appointment->isPending())
                                    <form method="POST" action="{{ route('appointments.confirm', $appointment) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                            Confirmar
                                        </button>
                                    </form>
                                @endif
                                
                                <form method="POST" action="{{ route('appointments.complete', $appointment) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                        Concluir
                                    </button>
                                </form>
                                
                                <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" onsubmit="return confirm('Tem certeza que deseja cancelar?')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                        Cancelar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600 text-center py-8">Nenhum agendamento para hoje.</p>
        @endif
    </div>

    <!-- Upcoming Appointments -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Próximos Agendamentos</h2>
        
        @if($upcomingAppointments->count() > 0)
            <div class="space-y-3">
                @foreach($upcomingAppointments as $appointment)
                    <div class="border border-gray-200 rounded-lg p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-lg font-semibold text-gray-900">
                                    {{ $appointment->date->format('d/m/Y') }} às {{ $appointment->time->format('H:i') }}
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($appointment->status === 'confirmed') bg-green-100 text-green-800
                                    @elseif($appointment->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif
                                ">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                            <p class="text-gray-900 font-medium">{{ $appointment->client_name }}</p>
                            <p class="text-sm text-gray-600">{{ $appointment->client_whatsapp }}</p>
                        </div>
                        
                        <div class="flex gap-2">
                            @if($appointment->isPending())
                                <form method="POST" action="{{ route('appointments.confirm', $appointment) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                        Confirmar
                                    </button>
                                </form>
                            @endif
                            
                            @if($appointment->isPending() || $appointment->isConfirmed())
                                <form method="POST" action="{{ route('appointments.cancel', $appointment) }}" onsubmit="return confirm('Tem certeza que deseja cancelar?')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                        Cancelar
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600 text-center py-8">Nenhum agendamento futuro.</p>
        @endif
    </div>

</div>

@push('scripts')
<script>
    function copyLink() {
        const input = document.getElementById('agendaLink');
        input.select();
        document.execCommand('copy');
        alert('Link copiado!');
    }
</script>
@endpush

@endsection
