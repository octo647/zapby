@extends('layouts.professional')

@section('title', 'Painel')

@section('content')
@php
    $appointmentStatusLabels = [
        'pending' => 'Pendente',
        'confirmed' => 'Confirmado',
        'completed' => 'Concluído',
        'cancelled' => 'Cancelado',
    ];
@endphp
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Onboarding Banner -->
    @include('professional.partials.onboarding-banner')
    
    <!-- Header de boas-vindas -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-6 text-white mb-8">
        <h2 class="text-2xl font-bold mb-2">Olá, {{ $professional->name }}! 👋</h2>
        <p class="text-indigo-100">{{ $professional->profession }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Agendamentos Hoje</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $todayAppointments->count() }}</p>
                </div>
                <div class="bg-indigo-100 rounded-full p-4">
                    <span class="text-3xl">📅</span>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Próximos 7 Dias</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $upcomingAppointments->count() }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-4">
                    <span class="text-3xl">⏰</span>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total Este Mês</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $monthlyTotal }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-4">
                    <span class="text-3xl">📊</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Disponibilidade de hoje -->
    <section class="bg-white rounded-lg shadow-md mb-8" aria-labelledby="today-schedule-title">
        <div class="px-6 py-4 border-b border-gray-200 sm:flex sm:items-center sm:justify-between">
            <div>
                <h3 id="today-schedule-title" class="text-xl font-bold text-gray-900">Horários de hoje</h3>
                <p class="mt-1 text-sm text-gray-500">Clique em um horário livre para bloqueá-lo por 30 minutos.</p>
            </div>
            <div class="mt-3 flex flex-wrap gap-3 text-xs text-gray-600 sm:mt-0" aria-label="Legenda">
                <span class="flex items-center gap-1"><span class="h-3 w-3 rounded bg-white border border-indigo-300"></span>Livre</span>
                <span class="flex items-center gap-1"><span class="h-3 w-3 rounded bg-gray-300"></span>Ocupado</span>
                <span class="flex items-center gap-1"><span class="h-3 w-3 rounded bg-amber-200"></span>Bloqueado</span>
            </div>
        </div>

        <div class="p-6">
            @if($errors->has('start_time'))
                <p class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    {{ $errors->first('start_time') }}
                </p>
            @endif

            @if(count($todaySchedule) > 0)
                <div class="grid grid-cols-3 gap-2 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8">
                    @foreach($todaySchedule as $slot)
                        @if($slot['status'] === 'free')
                            <form method="POST" action="{{ route('professional.time-blocks.store') }}"
                                  onsubmit="return confirm('Bloquear o horário das {{ $slot['start'] }} às {{ $slot['end'] }}?')">
                                @csrf
                                <input type="hidden" name="date" value="{{ now()->format('Y-m-d') }}">
                                <input type="hidden" name="block_type" value="partial">
                                <input type="hidden" name="start_time" value="{{ $slot['start'] }}">
                                <input type="hidden" name="end_time" value="{{ $slot['end'] }}">
                                <input type="hidden" name="reason" value="Bloqueado pelo painel">
                                <button type="submit"
                                        class="w-full rounded-lg border border-indigo-300 bg-white px-2 py-3 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                        title="Bloquear este horário">
                                    {{ $slot['start'] }}
                                </button>
                            </form>
                        @else
                            @php
                                $slotClasses = match($slot['status']) {
                                    'occupied' => 'bg-gray-300 text-gray-700',
                                    'blocked' => 'bg-amber-200 text-amber-900',
                                    default => 'bg-gray-100 text-gray-400',
                                };
                                $slotTitle = match($slot['status']) {
                                    'occupied' => 'Ocupado'.($slot['label'] ? ': '.$slot['label'] : ''),
                                    'blocked' => 'Bloqueado'.($slot['label'] ? ': '.$slot['label'] : ''),
                                    default => 'Horário encerrado',
                                };
                            @endphp
                            <div class="rounded-lg px-2 py-3 text-center text-sm font-semibold {{ $slotClasses }}"
                                 title="{{ $slotTitle }}" aria-label="{{ $slot['start'] }} — {{ $slotTitle }}">
                                {{ $slot['start'] }}
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="py-6 text-center">
                    <p class="text-gray-600">Não há expediente configurado para hoje.</p>
                    <a href="{{ route('professional.settings') }}#agenda-settings" class="mt-2 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-800">
                        Configurar agenda
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- Agendamentos de Hoje -->
    @if($todayAppointments->count() > 0)
    <div class="bg-white rounded-lg shadow-md mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Agendamentos de Hoje</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($todayAppointments as $appointment)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center gap-4">
                        <div class="bg-indigo-100 rounded-full p-3">
                            <span class="text-2xl">👤</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-lg">{{ $appointment->client_name }}</p>
                            <p class="text-sm text-gray-600">
                                🕐 {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }} 
                                - {{ $appointment->service }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-4 py-2 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                            {{ $appointmentStatusLabels[$appointment->status] ?? $appointment->status }}
                        </span>
                        @php
                            // Usa phone_number se existir, senão tenta client_whatsapp
                            $phoneNumber = $appointment->phone_number;
                            if (!$phoneNumber) {
                                $cleanWhatsapp = preg_replace('/[^0-9]/', '', $appointment->client_whatsapp);
                                // Só usa se NÃO for LID (LIDs têm 15 dígitos)
                                $phoneNumber = (strlen($cleanWhatsapp) >= 10 && strlen($cleanWhatsapp) <= 13) ? $cleanWhatsapp : null;
                            }
                            $isValidPhone = $phoneNumber && strlen($phoneNumber) >= 10 && strlen($phoneNumber) <= 13;
                        @endphp
                        @if($isValidPhone)
                            <a href="https://wa.me/{{ $phoneNumber }}" 
                               target="_blank"
                               class="text-green-600 hover:text-green-700 text-2xl">
                                💬
                            </a>
                        @else
                            <span class="text-gray-400 text-xl" title="Número indisponível">
                                💬
                            </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow-md mb-8 p-8 text-center">
        <h3 class="text-xl font-semibold text-gray-700 mb-2">Nenhum agendamento para hoje</h3>
        <p class="text-gray-500">Aproveite para organizar sua agenda!</p>
    </div>
    @endif

    <!-- Próximos Agendamentos -->
    @if($upcomingAppointments->count() > 0)
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Próximos Agendamentos</h3>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @foreach($upcomingAppointments->take(10) as $appointment)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-4">
                        <div class="text-center bg-indigo-100 rounded-lg p-3">
                            <p class="text-2xl font-bold text-indigo-600">{{ \Carbon\Carbon::parse($appointment->date)->format('d') }}</p>
                            <p class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($appointment->date)->locale('pt_BR')->translatedFormat('M') }}</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $appointment->client_name }}</p>
                            <p class="text-sm text-gray-600">
                                🕐 {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }} 
                                - {{ $appointment->service }}
                            </p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                        {{ \Carbon\Carbon::parse($appointment->date->format('Y-m-d') . ' ' . $appointment->time)->locale('pt_BR')->diffForHumans() }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>

@endsection
