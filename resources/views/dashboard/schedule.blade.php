@extends('layouts.app')

@section('title', 'Horários de Trabalho - Zapby')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Navigation -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <nav class="flex overflow-x-auto">
            <a href="{{ route('dashboard') }}" class="px-6 py-4 text-gray-600 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 whitespace-nowrap">
                Agendamentos
            </a>
            <a href="{{ route('dashboard.schedule') }}" class="px-6 py-4 text-indigo-600 border-b-2 border-indigo-600 font-medium whitespace-nowrap">
                Horários
            </a>
            <a href="{{ route('dashboard.time-blocks') }}" class="px-6 py-4 text-gray-600 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 whitespace-nowrap">
                Bloqueios
            </a>
        </nav>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Configurar Horários de Trabalho</h2>

        <form method="POST" action="{{ route('dashboard.schedule.update') }}">
            @csrf
            @method('PUT')

            <!-- Working Days -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Dias da semana que você trabalha
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @php
                        $days = [
                            0 => 'Domingo',
                            1 => 'Segunda',
                            2 => 'Terça',
                            3 => 'Quarta',
                            4 => 'Quinta',
                            5 => 'Sexta',
                            6 => 'Sábado',
                        ];
                    @endphp
                    
                    @foreach($days as $value => $label)
                        <label class="flex items-center gap-2 p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input 
                                type="checkbox" 
                                name="working_days[]" 
                                value="{{ $value }}"
                                {{ in_array($value, old('working_days', $professional->working_days)) ? 'checked' : '' }}
                                class="rounded text-indigo-600 focus:ring-indigo-500"
                            >
                            <span class="text-sm text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('working_days')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Working Hours -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Horário de trabalho
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="start_time" class="block text-sm text-gray-600 mb-1">Início</label>
                        <input 
                            type="time" 
                            id="start_time" 
                            name="working_hours[start]" 
                            value="{{ old('working_hours.start', $professional->working_hours['start']) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        >
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm text-gray-600 mb-1">Término</label>
                        <input 
                            type="time" 
                            id="end_time" 
                            name="working_hours[end]" 
                            value="{{ old('working_hours.end', $professional->working_hours['end']) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        >
                    </div>
                </div>
                @error('working_hours.start')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('working_hours.end')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Default Duration -->
            <div class="mb-6">
                <label for="default_duration" class="block text-sm font-medium text-gray-700 mb-2">
                    Duração padrão do atendimento (minutos)
                </label>
                <select 
                    id="default_duration" 
                    name="default_duration"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                    @foreach([15, 30, 45, 60, 90, 120, 150, 180] as $duration)
                        <option value="{{ $duration }}" {{ old('default_duration', $professional->default_duration) == $duration ? 'selected' : '' }}>
                            {{ $duration }} minutos
                        </option>
                    @endforeach
                </select>
                @error('default_duration')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button 
                    type="submit"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition"
                >
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
