@extends('layouts.app')

@section('title', 'Bloqueios de Horário - AgendaPro')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Navigation -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <nav class="flex overflow-x-auto">
            <a href="{{ route('dashboard') }}" class="px-6 py-4 text-gray-600 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 whitespace-nowrap">
                Agendamentos
            </a>
            <a href="{{ route('dashboard.schedule') }}" class="px-6 py-4 text-gray-600 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-300 whitespace-nowrap">
                Horários
            </a>
            <a href="{{ route('dashboard.time-blocks') }}" class="px-6 py-4 text-indigo-600 border-b-2 border-indigo-600 font-medium whitespace-nowrap">
                Bloqueios
            </a>
        </nav>
    </div>

    <!-- New Time Block Form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Bloquear Horário</h2>

        <form method="POST" action="{{ route('dashboard.time-blocks.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- Date -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                        Data
                    </label>
                    <input 
                        type="date" 
                        id="date" 
                        name="date" 
                        required
                        min="{{ now()->format('Y-m-d') }}"
                        value="{{ old('date') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                    @error('date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reason -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Motivo (opcional)
                    </label>
                    <input 
                        type="text" 
                        id="reason" 
                        name="reason" 
                        value="{{ old('reason') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                        placeholder="Ex: Almoço, Reunião..."
                    >
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- Start Time -->
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Horário Início
                    </label>
                    <input 
                        type="time" 
                        id="start_time" 
                        name="start_time" 
                        required
                        value="{{ old('start_time') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                    @error('start_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- End Time -->
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Horário Término
                    </label>
                    <input 
                        type="time" 
                        id="end_time" 
                        name="end_time" 
                        required
                        value="{{ old('end_time') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                    @error('end_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <button 
                    type="submit"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition"
                >
                    Criar Bloqueio
                </button>
            </div>
        </form>
    </div>

    <!-- Existing Time Blocks -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Bloqueios Ativos</h2>
        
        @if($timeBlocks->count() > 0)
            <div class="space-y-3">
                @foreach($timeBlocks as $block)
                    <div class="border border-gray-200 rounded-lg p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex-1">
                            <p class="text-lg font-semibold text-gray-900">
                                {{ $block->date->format('d/m/Y') }}
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ $block->start_time->format('H:i') }} - {{ $block->end_time->format('H:i') }}
                            </p>
                            @if($block->reason)
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $block->reason }}
                                </p>
                            @endif
                        </div>
                        
                        <form method="POST" action="{{ route('dashboard.time-blocks.destroy', $block) }}" onsubmit="return confirm('Tem certeza que deseja remover este bloqueio?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                                Remover
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-600 text-center py-8">Nenhum bloqueio de horário cadastrado.</p>
        @endif
    </div>

</div>
@endsection
