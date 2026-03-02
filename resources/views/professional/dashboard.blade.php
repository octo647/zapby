@extends('layouts.professional')

@section('title', 'Painel')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header de boas-vindas -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-6 text-white mb-6">
        <h2 class="text-2xl font-bold mb-2">Olá, {{ $professional->name }}! 👋</h2>
        <p class="text-indigo-100">{{ $professional->profession }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Agendamentos Hoje</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $todayAppointments->count() }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-4">
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
                                - {{ $appointment->duration }} minutos
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-4 py-2 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                            {{ $appointment->status }}
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
        <span class="text-6xl mb-4 block">📅</span>
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
                            <p class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($appointment->date)->format('M') }}</p>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $appointment->client_name }}</p>
                            <p class="text-sm text-gray-600">
                                🕐 {{ \Carbon\Carbon::parse($appointment->time)->format('H:i') }} 
                                - {{ $appointment->duration }} min
                            </p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                        {{ \Carbon\Carbon::parse($appointment->date->format('Y-m-d') . ' ' . $appointment->time)->diffForHumans() }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

</div>

@endsection
