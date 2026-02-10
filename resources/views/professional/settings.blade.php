@extends('layouts.professional')

@section('title', 'Configurações')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <h2 class="text-3xl font-bold text-gray-900 mb-8">Configurações</h2>

    <!-- Configurações do Perfil -->
    <div class="bg-white rounded-lg shadow-md mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Dados do Perfil</h3>
        </div>
        <form method="POST" action="{{ route('professional.settings') }}" class="p-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nome Completo *
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $professional->name) }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="profession" class="block text-sm font-medium text-gray-700 mb-2">
                        Profissão / Serviço *
                    </label>
                    <input 
                        type="text" 
                        id="profession" 
                        name="profession" 
                        value="{{ old('profession', $professional->profession) }}"
                        required
                        placeholder="Ex: Cabeleireiro, Psicólogo, Personal Trainer"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                    @error('profession')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="whatsapp" class="block text-sm font-medium text-gray-700 mb-2">
                        WhatsApp (opcional)
                    </label>
                    <input 
                        type="text" 
                        id="whatsapp" 
                        name="whatsapp" 
                        value="{{ old('whatsapp', $professional->whatsapp) }}"
                        placeholder="(32) 98294-948"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                    <p class="mt-1 text-xs text-gray-500">Para receber notificações de novos agendamentos</p>
                    @error('whatsapp')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="default_duration" class="block text-sm font-medium text-gray-700 mb-2">
                        Duração Padrão (minutos) *
                    </label>
                    <input 
                        type="number" 
                        id="default_duration" 
                        name="default_duration" 
                        value="{{ old('default_duration', $professional->default_duration) }}"
                        required
                        min="15"
                        max="240"
                        step="15"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                    @error('default_duration')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Dias de Trabalho *
                </label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @php
                        $days = [
                            0 => 'Domingo',
                            1 => 'Segunda',
                            2 => 'Terça',
                            3 => 'Quarta',
                            4 => 'Quinta',
                            5 => 'Sexta',
                            6 => 'Sábado'
                        ];
                        $workingDays = old('working_days', $professional->working_days ?? []);
                    @endphp
                    @foreach($days as $value => $label)
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 {{ in_array($value, $workingDays) ? 'bg-indigo-50 border-indigo-500' : '' }}">
                            <input 
                                type="checkbox" 
                                name="working_days[]" 
                                value="{{ $value }}"
                                {{ in_array($value, $workingDays) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >
                            <span class="ml-2 text-sm font-medium text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
                @error('working_days')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                @php
                    $workingHours = old('working_hours', $professional->working_hours ?? ['start' => '08:00', 'end' => '18:00']);
                @endphp
                <div>
                    <label for="working_hours_start" class="block text-sm font-medium text-gray-700 mb-2">
                        Horário de Início *
                    </label>
                    <input 
                        type="time" 
                        id="working_hours_start" 
                        name="working_hours_start" 
                        value="{{ $workingHours['start'] }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                    @error('working_hours_start')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="working_hours_end" class="block text-sm font-medium text-gray-700 mb-2">
                        Horário de Término *
                    </label>
                    <input 
                        type="time" 
                        id="working_hours_end" 
                        name="working_hours_end" 
                        value="{{ $workingHours['end'] }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                    @error('working_hours_end')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end">
                <button 
                    type="submit"
                    class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition"
                >
                    💾 Salvar Configurações
                </button>
            </div>
        </form>
    </div>

    <!-- Alterar Senha -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Alterar Senha</h3>
        </div>
        <form method="POST" action="{{ route('professional.password') }}" class="p-6">
            @csrf

            <div class="space-y-4 mb-6">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Senha Atual *
                    </label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                        Nova Senha *
                    </label>
                    <input 
                        type="password" 
                        id="new_password" 
                        name="new_password" 
                        required
                        minlength="6"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                    <p class="mt-1 text-xs text-gray-500">Mínimo de 6 caracteres</p>
                    @error('new_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirmar Nova Senha *
                    </label>
                    <input 
                        type="password" 
                        id="new_password_confirmation" 
                        name="new_password_confirmation" 
                        required
                        minlength="6"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                </div>
            </div>

            <div class="flex justify-end">
                <button 
                    type="submit"
                    class="bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition"
                >
                    🔒 Alterar Senha
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
