@extends('layouts.professional')

@section('title', 'Bloqueios de Horário')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">🚫 Bloqueios de Horário</h1>
        <p class="text-gray-600 mt-2">Bloqueie horários ou dias inteiros para gerenciar sua disponibilidade</p>
    </div>

    <!-- Formulário para criar bloqueio -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">➕ Criar Novo Bloqueio</h2>
        
        <form method="POST" action="{{ route('professional.time-blocks.store') }}">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Data -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                        Data *
                    </label>
                    <input 
                        type="date" 
                        id="date" 
                        name="date" 
                        value="{{ old('date', now()->format('Y-m-d')) }}"
                        min="{{ now()->format('Y-m-d') }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                    @error('date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipo de bloqueio -->
                <div>
                    <label for="block_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Bloqueio *
                    </label>
                    <select 
                        id="block_type" 
                        name="block_type" 
                        required
                        onchange="toggleTimeFields(this.value)"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                        <option value="full_day" {{ old('block_type') == 'full_day' ? 'selected' : '' }}>
                            Dia Inteiro
                        </option>
                        <option value="partial" {{ old('block_type') == 'partial' ? 'selected' : '' }}>
                            Horário Específico
                        </option>
                    </select>
                    @error('block_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Horários (apenas para bloqueio parcial) -->
            <div id="time-fields" class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6" style="display: none;">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Horário de Início
                    </label>
                    <input 
                        type="time" 
                        id="start_time" 
                        name="start_time" 
                        value="{{ old('start_time') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                    @error('start_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Horário de Fim
                    </label>
                    <input 
                        type="time" 
                        id="end_time" 
                        name="end_time" 
                        value="{{ old('end_time') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                    @error('end_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Motivo -->
            <div class="mt-6">
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Motivo (Opcional)
                </label>
                <input 
                    type="text" 
                    id="reason" 
                    name="reason" 
                    value="{{ old('reason') }}"
                    placeholder="Ex: Férias, Compromisso pessoal, etc."
                    maxlength="255"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
                @error('reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6">
                <button 
                    type="submit"
                    class="w-full md:w-auto bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition"
                >
                    ➕ Criar Bloqueio
                </button>
            </div>
        </form>
    </div>

    <!-- Lista de bloqueios -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">📋 Bloqueios Ativos</h2>
        </div>

        @if($timeBlocks->isEmpty())
            <div class="p-8 text-center">
                <p class="text-gray-500 text-lg mb-2">Nenhum bloqueio cadastrado</p>
                <p class="text-gray-400 text-sm">Crie bloqueios para gerenciar sua disponibilidade</p>
            </div>
        @else
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
                                Ações
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Motivo
                            </th>
                            
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($timeBlocks as $block)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($block->date)->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($block->date)->locale('pt_BR')->isoFormat('dddd') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $startTime = $block->start_time->format('H:i');
                                    $endTime = $block->end_time->format('H:i');
                                @endphp
                                @if($startTime === '00:00' && $endTime === '23:59')
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full font-medium">
                                        🚫 Dia Inteiro
                                    </span>
                                @else
                                    <div class="text-sm text-gray-900">
                                        {{ $startTime }} - {{ $endTime }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <form method="POST" action="{{ route('professional.time-blocks.destroy', $block->id) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button 
                                        type="submit"
                                        onclick="return confirm('Deseja remover este bloqueio?')"
                                        class="text-red-600 hover:text-red-900 font-medium"
                                    >
                                        🗑️ Remover
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $block->reason ?: '-' }}
                                </div>
                            </td>
                            
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
function toggleTimeFields(blockType) {
    const timeFields = document.getElementById('time-fields');
    const startTime = document.getElementById('start_time');
    const endTime = document.getElementById('end_time');
    
    if (blockType === 'partial') {
        timeFields.style.display = 'grid';
        startTime.required = true;
        endTime.required = true;
    } else {
        timeFields.style.display = 'none';
        startTime.required = false;
        endTime.required = false;
    }
}

// Inicializar ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    const blockType = document.getElementById('block_type').value;
    toggleTimeFields(blockType);
});
</script>
@endpush

@endsection
