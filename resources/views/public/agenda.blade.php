@extends('layouts.app')

@section('title', $professional->name . ' - AgendaPro')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Professional Header -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $professional->name }}</h1>
        <p class="text-gray-600 mb-4">{{ $professional->profession }}</p>
        
        <div class="flex items-center gap-4 text-sm text-gray-600">
            <span>⏰ Duração: {{ $professional->default_duration }} minutos</span>
            <span>📱 WhatsApp: {{ $professional->whatsapp }}</span>
        </div>
    </div>

    <!-- Booking Form -->
    <div class="bg-white rounded-lg shadow-md p-6" x-data="bookingForm()">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Agende seu horário</h2>

        <form method="POST" action="{{ route('agenda.store', $professional) }}">
            @csrf

            <!-- Client Name -->
            <div class="mb-4">
                <label for="client_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Seu nome
                </label>
                <input 
                    type="text" 
                    id="client_name" 
                    name="client_name" 
                    required
                    value="{{ old('client_name') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="Digite seu nome completo"
                >
                @error('client_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Client WhatsApp -->
            <div class="mb-4">
                <label for="client_whatsapp" class="block text-sm font-medium text-gray-700 mb-2">
                    Seu WhatsApp
                </label>
                <input 
                    type="tel" 
                    id="client_whatsapp" 
                    name="client_whatsapp" 
                    required
                    value="{{ old('client_whatsapp') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="(11) 99999-9999"
                    x-mask="(99) 99999-9999"
                >
                @error('client_whatsapp')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Escolha a data e horário
                </label>

                @if(count($availableSlots) > 0)
                    <div class="space-y-4">
                        @foreach($availableSlots as $date => $slots)
                            <div x-data="{ expanded: @if($loop->first) true @else false @endif }">
                                <button 
                                    type="button"
                                    @click="expanded = !expanded"
                                    class="w-full flex justify-between items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition"
                                >
                                    <span class="font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($date)->locale('pt_BR')->isoFormat('dddd, D [de] MMMM') }}
                                    </span>
                                    <span x-show="!expanded">▼</span>
                                    <span x-show="expanded" x-cloak>▲</span>
                                </button>

                                <div x-show="expanded" x-cloak class="mt-3 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-2">
                                    @foreach($slots as $slot)
                                        <label class="cursor-pointer">
                                            <input 
                                                type="radio" 
                                                name="time" 
                                                value="{{ $slot['time'] }}"
                                                class="hidden peer"
                                                required
                                                @click="selectedDate = '{{ $date }}'"
                                            >
                                            <div class="p-3 text-center border border-gray-300 rounded-lg peer-checked:bg-indigo-600 peer-checked:text-white peer-checked:border-indigo-600 hover:border-indigo-400 transition">
                                                {{ $slot['time'] }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <input type="hidden" name="date" x-model="selectedDate">
                @else
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-yellow-800">Não há horários disponíveis no momento. Entre em contato pelo WhatsApp.</p>
                    </div>
                @endif

                @error('date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            @if(count($availableSlots) > 0)
                <button 
                    type="submit"
                    class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Confirmar Agendamento
                </button>
            @endif
        </form>
    </div>

    <!-- WhatsApp Contact -->
    <div class="mt-6 text-center">
        <a 
            href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $professional->whatsapp) }}" 
            target="_blank"
            class="inline-flex items-center gap-2 text-green-600 hover:text-green-700 font-medium"
        >
            <span>💬</span>
            Falar direto no WhatsApp
        </a>
    </div>

</div>

@push('scripts')
<script>
    function bookingForm() {
        return {
            selectedDate: '{{ old('date', '') }}',
        }
    }
</script>
@endpush

@endsection
