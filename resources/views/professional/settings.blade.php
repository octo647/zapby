@extends('layouts.professional')

@section('title', 'Configurações')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Onboarding Banner -->
    @include('professional.partials.onboarding-banner')
    
    <h2 class="text-3xl font-bold text-gray-900 mb-8">Configurações</h2>

    <style>
        .settings-section:target {
            outline: 3px solid rgba(79, 70, 229, 0.45);
            outline-offset: 4px;
        }
    </style>

    <!-- Tipo de Agenda -->
    <div id="agenda-settings" class="settings-section bg-white rounded-lg shadow-md mb-6 scroll-mt-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Tipo de Agenda</h3>
        </div>
        <form method="POST" action="{{ route('professional.settings.update') }}" class="p-6">
            @csrf

		    <div class="mb-6">
			<label class="block text-sm font-medium text-gray-700 mb-2">
			    Tipo de Agenda
		</label>

		<label>
		    <input type="radio" name="schedule_mode" value="weekly"
			{{ $professional->schedule_mode === 'weekly' ? 'checked' : '' }}>
		    Agenda Semanal
		</label>

		<label class="ml-4">
		    <input type="radio" name="schedule_mode" value="dated"
			{{ $professional->schedule_mode === 'dated' ? 'checked' : '' }}>
		    Agenda por Data
		</label>
                
                <div class="">
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
                        // Garantir que é sempre um array
                        $workingDays = is_array($workingDays) ? $workingDays : [];
		@endphp


		<div id="agenda-semanal">    
		@foreach($days as $value => $label)
		<div class="day-row grid grid-cols-6 gap-5 p-2 border border-indigo-300 rounded-lg">	
			<div class="col-span-2 row-span-2 flex items-center">
	    			<input 
                                type="checkbox" 
                                name="working_days[]" 
                                value="{{ $value }}"
                                {{ in_array($value, $workingDays) ? 'checked' : '' }}
                                class="working-day-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            >
			    <span class="text-sm p-2">
				{{ $label }}
		            </span>
			</div>
			<div class='periodo text-sm col-span-4'> 
				Período 1: <input type ="time"  
					id="periodo1-ini" 
					name="periodos[{{ $value }}][1][inicio]"
					value="{{ $periodos[$value][0]['inicio'] ?? '' }}"
			> 
				até    <input type ="time"  
					id="periodo1-fim" 
					name="periodos[{{ $value }}][1][fim]"
					value="{{ $periodos[$value][0]['fim'] ?? '' }}"
			>
			</div> 
			<div class='periodo col-span-4 text-sm'> 
				Período 2: <input type ="time"
					id="periodo2-ini" 
					name="periodos[{{ $value }}][2][inicio]" 
					value="{{ $periodos[$value][1]['inicio'] ?? '' }}"	
			> 
				até    <input type ="time"  
					id="periodo2-fim" 
					name="periodos[{{ $value }}][2][fim]" 
					value="{{ $periodos[$value][1]['fim'] ?? '' }}"
			>
			</div>
		</div>
		<div class="mb-2"></div>
		    
                @endforeach
                </div>              
                
                @error('working_days')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            </div> <!--fecha a agenda semanal -->
            <div id="agenda-data">
            <div class="block text-sm font-medium text-gray-700 mb-2">
            Defina o período da agenda, depois clique em Gerar Datas *
            </div>
            <div class="mb-2"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-1 p-2 border border-indigo-300 rounded-lg">
            <div>
            Data inicial: 
            </div>
           
            <div>
            <input id="agenda-inicio" class="text-sm w-40" type="date">
            </div>
        
            </div> 
            <div class="mb-2"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-1 p-2 border border-indigo-300 rounded-lg">
            <div>
            Data final: 
            </div>
            <div>
            <input id="agenda-fim" class="text-sm w-40" type="date">
            </div>
            </div>
            <div class="mb-2"></div>
            <div class="flex justify-begin">
                <button 
                    id="gerar-datas"
                    type="button"
                    class="bg-lime-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-lime-700 transition"
                >
                    Gerar Datas
                </button>
            </div>
            <div id="cabecalho-datas" style="display:none;" class="grid grid-cols-3 gap-1 mt-3 ml-3 font-semibold text-gray-700 ">
                <div>Data</div>
                <div>Período 1</div>
                <div>Período 2</div>
            </div>
            <div id="lista-datas" class=""></div>
            <div id="datas-programadas">
            @if($datePeriods)
             <div class="grid grid-cols-3 gap-1 mt-3 ml-3 font-semibold text-gray-700 ">
                <div>Data</div>
                <div>Período 1</div>
                <div>Período 2</div>
            </div>
            @foreach($datePeriods as $date => $periodos)
            <div class="schedule-row grid grid-cols-6 mb-1 gap-5 p-2 border border-indigo-300 rounded-lg">


                <div class="col-span-2 flex items-center">
                    <input
                        type="checkbox"
                        class="schedule-checkbox"
                        name="datas_ativas[]"
                        value="{{$date}}"
                        checked>
                    <span class="text-sm ml-2">
            {{ \Carbon\Carbon::parse($date)->format('d/m') }}
        </span>
                </div>

                <div class="col-span-2 periodo">
                    <div class='text-sm '>
                    de  <input
                        type="time"
                        class="w-16 rounded-none rounded-s-lg flex-1 block bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs"
                        name="datas[{{$date}}][1][inicio]"
                        value="{{$periodos[0]['inicio'] ?? ''}}">
                        
                  
                    até <input
                        type="time"
                        class="w-16 rounded-none rounded-s-lg flex-1 block bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs"
                        name="datas[{{$date}}][1][fim]"
                        value="{{$periodos[0]['fim'] ?? ''}}">
                    </div>
                </div>

                <div class="col-span-2 periodo">
                <div class='text-sm'>
                    de  <input
                        type="time"
                        class="w-16 rounded-none rounded-s-lg flex-1 block bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs"
                        name="datas[{{$date}}][2][inicio]"
                        value="{{$periodos[1]['inicio'] ?? '' }}">
             
                    até <input
                        type="time"
                        class="w-16 rounded-none rounded-s-lg flex-1 block bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs"
                        name="datas[{{$date}}][2][fim]"
                        value="{{$periodos[1]['fim'] ?? '' }}">
                       
                </div>
                </div>

            </div>
            @endforeach
            @endif
            </div>

            
            
            
            </div> <!--fecha a agenda-data -->
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                @php
                    $workingHours = old('working_hours', $professional->working_hours ?? ['start' => '08:00', 'end' => '18:00']);
                    // Garantir que é sempre um array
                    if (!is_array($workingHours)) {
                        $workingHours = ['start' => '08:00', 'end' => '18:00'];
                    }
                @endphp
                
            </div>

            <div class="flex justify-end">
	                <button 
	                    type="submit"
	                    class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition"
	                >
	                    💾 Salvar Agenda
	                </button>
	            </div>
	        </form>
	    </div>

    <!-- Gerenciar Serviços -->
    <div id="services-settings" class="settings-section bg-white rounded-lg shadow-md mb-6 scroll-mt-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Meus Serviços</h3>
            <p class="text-sm text-gray-600 mt-1">Personalize os serviços, preços e durações</p>
        </div>
        <form method="POST" action="{{ route('professional.settings') }}" class="p-6">
            @csrf

            <div id="services-container" class="space-y-4 mb-4">
                @php
                    $services = old('services', $professional->services ?? []);
                    // Garantir que é sempre um array
                    if (!is_array($services)) {
                        $services = [];
                    }
                    // Garantir que não está vazio - adicionar serviço padrão
                    if (empty($services)) {
                        $services = [['name' => 'Serviço 1', 'price' => null, 'duration' => 30]];
                    }
                    // Converter serviços antigos (strings) para novo formato (objetos)
                    $services = array_map(function($service) {
                        if (is_string($service)) {
                            return ['name' => $service, 'price' => null, 'duration' => 30];
                        }
                        return $service;
                    }, $services);
                @endphp
                @foreach($services as $index => $service)
                    <div class="service-item bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                            <div class="md:col-span-5">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Nome do Serviço</label>
                                <input 
                                    type="text" 
                                    name="services[{{ $index }}][name]" 
                                    value="{{ is_array($service) ? $service['name'] : $service }}"
                                    placeholder="Ex: Corte Masculino"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                >
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Preço (R$) - opcional</label>
                                <input 
                                    type="number" 
                                    name="services[{{ $index }}][price]" 
                                    value="{{ is_array($service) ? ($service['price'] ?? '') : '' }}"
                                    placeholder=""
                                    step="0.01"
                                    min="0"
                                    
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                >
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-xs font-medium text-gray-700 mb-1">Duração (min)</label>
                                <input 
                                    type="number" 
                                    name="services[{{ $index }}][duration]" 
                                    value="{{ is_array($service) ? ($service['duration'] ?? 30) : 30 }}"
                                    placeholder="30"
                                    step="15"
                                    min="15"
                                    max="480"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                >
                            </div>
                            <div class="md:col-span-1 flex items-end">
                                <button 
                                    type="button" 
                                    onclick="removeService(this)"
                                    class="w-full px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition"
                                    title="Remover serviço"
                                >
                                    🗑️
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex gap-3 mb-6">
                <button 
                    type="button"
                    onclick="addService()"
                    class="bg-green-100 text-green-700 px-4 py-2 rounded-lg font-semibold hover:bg-green-200 transition"
                >
                    ➕ Adicionar Serviço
                </button>
            </div>

            @error('services')
                <p class="mt-1 mb-4 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <div class="flex justify-end">
                <button 
                    type="submit"
                    class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition"
                >
                    💾 Salvar Serviços
                </button>
            </div>
	        </form>
	    </div>

    <!-- Configurações do Perfil -->
    <div id="profile-settings" class="settings-section bg-white rounded-lg shadow-md mb-6 scroll-mt-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-xl font-bold text-gray-900">Dados do Perfil</h3>
        </div>
        <form method="POST" action="{{ route('professional.settings.update') }}" class="p-6">
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

            <div class="mb-6">
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

            <div class="flex justify-end">
                <button 
                    type="submit"
                    class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition"
                >
                    💾 Salvar Perfil
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

<script>
let serviceIndex = {{ count($services) }};

function addService() {
    const container = document.getElementById('services-container');
    const div = document.createElement('div');
    div.className = 'service-item bg-gray-50 p-4 rounded-lg border border-gray-200';
    div.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-5">
                <label class="block text-xs font-medium text-gray-700 mb-1">Nome do Serviço</label>
                <input 
                    type="text" 
                    name="services[${serviceIndex}][name]" 
                    value=""
                    placeholder="Ex: Corte Masculino"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
            </div>
            <div class="md:col-span-3">
                <label class="block text-xs font-medium text-gray-700 mb-1">Preço (R$)</label>
                <input 
                    type="number" 
                    name="services[${serviceIndex}][price]" 
                    value=""
                    placeholder=""
                    step="0.01"
                    min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
            </div>
            <div class="md:col-span-3">
                <label class="block text-xs font-medium text-gray-700 mb-1">Duração (min)</label>
                <input 
                    type="number" 
                    name="services[${serviceIndex}][duration]" 
                    value="30"
                    placeholder="30"
                    step="15"
                    min="15"
                    max="480"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
            </div>
            <div class="md:col-span-1 flex items-end">
                <button 
                    type="button" 
                    onclick="removeService(this)"
                    class="w-full px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition"
                    title="Remover serviço"
                >
                    🗑️
                </button>
            </div>
        </div>
    `;
    container.appendChild(div);
    serviceIndex++;
}

function removeService(button) {
    const container = document.getElementById('services-container');
    const items = container.querySelectorAll('.service-item');
    
    // Não permitir remover se só tiver um serviço
    if (items.length <= 1) {
        alert('Você precisa ter pelo menos um serviço cadastrado!');
        return;
    }
    
    button.closest('.service-item').remove();
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.day-row').forEach(function(row) {

        const checkbox = row.querySelector('.working-day-checkbox');

        const periodos = row.querySelectorAll('.periodo');

        function atualizar() {

            periodos.forEach(function(periodo) {

                periodo.style.display =
                    checkbox.checked ? '' : 'none';

            });
        }

        checkbox.addEventListener('change', atualizar);

        atualizar();
    });

});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {

    const weeklyRadio = document.querySelector(
        'input[name="schedule_mode"][value="weekly"]'
    );

    const datedRadio = document.querySelector(
        'input[name="schedule_mode"][value="dated"]'
    );

    const weeklyDiv = document.getElementById('agenda-semanal');
    const datedDiv = document.getElementById('agenda-data');

    function atualizarModoAgenda() {

        if (weeklyRadio.checked) {

            weeklyDiv.style.display = '';
            datedDiv.style.display = 'none';

        } else {

            weeklyDiv.style.display = 'none';
            datedDiv.style.display = '';

        }
    }

    weeklyRadio.addEventListener('change', atualizarModoAgenda);
    datedRadio.addEventListener('change', atualizarModoAgenda);

    atualizarModoAgenda();
});
</script>
<script>
    document.getElementById('gerar-datas').addEventListener('click', function() {
    
        const diasSemana = [
            'Dom',
            'Seg',
            'Ter',
            'Qua',
            'Qui',
            'Sex',
            'Sáb'
        ];
        

        const inicio = document.getElementById('agenda-inicio').value;
        const fim = document.getElementById('agenda-fim').value;
        if (!inicio || !fim) {
            alert('Informe a data inicial e a data final.');
            return;
        }
        if (inicio >= fim) {
            alert('A data inicial deve ser menor que a data final.');
            return;
        }
        const [anoInicio, mesInicio, diaInicio] = inicio.split('-');
        const inicioDate = new Date(anoInicio, mesInicio - 1, diaInicio);
        const [anoFim, mesFim, diaFim] = fim.split('-');
        const fimDate = new Date(anoFim, mesFim - 1, diaFim);
        
        const lista = document.getElementById('lista-datas');
        const programadas = document.getElementById('datas-programadas');
        const cabecalho = document.getElementById('cabecalho-datas');
        if (programadas) {

            if (!confirm(
                'As datas já programadas serão substituídas por uma nova programação. Deseja continuar?'
            )) {
                return;
            }

            programadas.remove();
        }
        
        lista.innerHTML = '';
        
        if (inicioDate > fimDate) {
            cabecalho.style.display = 'none';
            alert('A data inicial deve ser anterior à data final.');
            return;
        }
        cabecalho.style.display = '';
        
    
    let data = new Date(inicioDate);

    while (data <= fimDate) {
        const diaSemana = diasSemana[data.getDay()];
        const yyyy = data.getFullYear();
        const mm = String(data.getMonth() + 1).padStart(2, '0');
        const dd = String(data.getDate()).padStart(2, '0');

        const dataISO = `${yyyy}-${mm}-${dd}`;

        lista.innerHTML += `
<div class="schedule-row grid grid-cols-6 mb-1 gap-5 p-2 border border-indigo-300 rounded-lg">


    <div class="col-span-2 flex items-center">
	        <input
	            type="checkbox"
	            class="schedule-checkbox"
	            name="datas_ativas[]"
	            value="${dataISO}"
	            checked>
        <span class="text-sm ml-2">${diaSemana} ${dd}/${mm}</span>
    </div>

    <div class="col-span-2 periodo">
        <div class='text-sm '>
        de  <input
            type="time"
            class="w-16 rounded-none rounded-s-lg flex-1 block bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs"
            name="datas[${dataISO}][1][inicio]">
            
      
        até <input
            type="time"
            class="w-16 rounded-none rounded-s-lg flex-1 block bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs"
            name="datas[${dataISO}][1][fim]">
        </div>
    </div>

    <div class="col-span-2 periodo">
    <div class='text-sm'>
        de  <input
            type="time"
            class="w-16 rounded-none rounded-s-lg flex-1 block bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs"
            name="datas[${dataISO}][2][inicio]">
 
        até <input
            type="time"
            class="w-16 rounded-none rounded-s-lg flex-1 block bg-neutral-secondary-medium border border-default-medium text-heading text-sm rounded-base focus:ring-brand focus:border-brand shadow-xs"
            name="datas[${dataISO}][2][fim]">
           
    </div>

</div>


`;

        data.setDate(data.getDate() + 1);
    }
    function ativarControleDatas() {

        document.querySelectorAll('.schedule-row').forEach(function(row) {

            const checkbox = row.querySelector('.schedule-checkbox');

            const periodos = row.querySelectorAll('.periodo');

            function atualizar() {

                periodos.forEach(function(periodo) {

                    periodo.style.display =
                        checkbox.checked ? '' : 'none';

                });

            }

            checkbox.addEventListener('change', atualizar);

            atualizar();
        });
    }

    ativarControleDatas();




});
</script>

@endsection
