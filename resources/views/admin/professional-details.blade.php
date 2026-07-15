@extends('layouts.admin')

@section('title', 'Detalhes do Profissional')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('admin.dashboard') }}" class="text-purple-600 hover:text-purple-800 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Voltar ao Dashboard
        </a>
    </div>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-16 w-16 bg-purple-100 rounded-full flex items-center justify-center">
                    <span class="text-purple-600 font-bold text-2xl">{{ substr($professional->name, 0, 1) }}</span>
                </div>
                <div class="ml-6">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $professional->name }}</h2>
                    <p class="text-gray-600">{{ $professional->profession }}</p>
                    <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                        <span><i class="fas fa-phone mr-1"></i> {{ $professional->whatsapp }}</span>
                        <span><i class="fas fa-link mr-1"></i> {{ $professional->slug }}</span>
                        <span><i class="fas fa-calendar mr-1"></i> Desde {{ $professional->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
            
            <div>
                @if($professional->is_admin)
                    <span class="px-4 py-2 rounded-full bg-purple-100 text-purple-800 font-semibold">
                        <i class="fas fa-shield-alt mr-1"></i> Administrador
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Agendamentos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $professionalStats['total_appointments'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <i class="fas fa-calendar text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Este Mês</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $professionalStats['appointments_this_month'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                    <i class="fas fa-clock text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Próximos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $professionalStats['upcoming_appointments'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Subscription Info -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-credit-card mr-2 text-purple-600"></i> Informações de Assinatura
                </h3>
            </div>
            <div class="p-6 space-y-4">
                @if($professional->subscription)
                    <div>
                        <span class="text-sm text-gray-500">Status:</span>
                        @if($professional->subscription->status === 'active')
                            <span class="ml-2 px-3 py-1 rounded-full bg-green-100 text-green-800 font-semibold text-sm">
                                <i class="fas fa-check-circle"></i> Ativo
                            </span>
                        @elseif($professional->subscription->status === 'trial')
                            <span class="ml-2 px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 font-semibold text-sm">
                                <i class="fas fa-clock"></i> Trial
                            </span>
                        @elseif($professional->subscription->status === 'suspended')
                            <span class="ml-2 px-3 py-1 rounded-full bg-orange-100 text-orange-800 font-semibold text-sm">
                                <i class="fas fa-pause-circle"></i> Suspenso
                            </span>
                        @else
                            <span class="ml-2 px-3 py-1 rounded-full bg-gray-100 text-gray-800 font-semibold text-sm">
                                <i class="fas fa-times-circle"></i> Cancelado
                            </span>
                        @endif
                    </div>

                    <div>
                        <span class="text-sm text-gray-500">Valor Mensal:</span>
                        <span class="ml-2 font-semibold text-gray-900">R$ {{ number_format($professional->subscription->amount, 2, ',', '.') }}</span>
                    </div>

                    @if($professional->subscription->next_due_date)
                    <div>
                        <span class="text-sm text-gray-500">Próximo Vencimento:</span>
                        <span class="ml-2 font-semibold text-gray-900">{{ $professional->subscription->next_due_date->format('d/m/Y') }}</span>
                        <span class="ml-1 text-xs text-gray-500">({{ $professional->subscription->next_due_date->diffForHumans() }})</span>
                    </div>
                    @endif

                    @if($professional->subscription->asaas_customer_id)
                    <div>
                        <span class="text-sm text-gray-500">ID Cliente Asaas:</span>
                        <span class="ml-2 font-mono text-sm text-gray-700">{{ $professional->subscription->asaas_customer_id }}</span>
                    </div>
                    @endif

                    @if($professional->subscription->asaas_subscription_id)
                    <div>
                        <span class="text-sm text-gray-500">ID Assinatura Asaas:</span>
                        <span class="ml-2 font-mono text-sm text-gray-700">{{ $professional->subscription->asaas_subscription_id }}</span>
                    </div>
                    @endif
                @elseif($professional->subscription_status === 'trial' && $professional->trial_ends_at)
                    <div>
                        <span class="text-sm text-gray-500">Status:</span>
                        @if($professional->trial_ends_at->isFuture())
                            <span class="ml-2 px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 font-semibold text-sm">
                                <i class="fas fa-clock"></i> Trial
                            </span>
                        @else
                            <span class="ml-2 px-3 py-1 rounded-full bg-red-100 text-red-800 font-semibold text-sm">
                                <i class="fas fa-exclamation-circle"></i> Trial Expirado
                            </span>
                        @endif
                    </div>

                    <div>
                        <span class="text-sm text-gray-500">Trial Válido Até:</span>
                        <span class="ml-2 font-semibold text-gray-900">{{ $professional->trial_ends_at->format('d/m/Y') }}</span>
                        <span class="ml-1 text-xs text-gray-500">({{ $professional->trial_ends_at->diffForHumans() }})</span>
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Sem assinatura ativa</p>
                @endif

                <!-- Change Status Form -->
                <div class="pt-4 border-t border-gray-200">
                    <form method="POST" action="{{ route('admin.professionals.update-status', $professional) }}" class="space-y-3">
                        @csrf
                        <label class="text-sm font-medium text-gray-700">Alterar Status:</label>
                        <div class="flex space-x-2">
                            <select name="status" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                <option value="trial" {{ $professional->subscription_status === 'trial' ? 'selected' : '' }}>Trial</option>
                                <option value="active" {{ $professional->subscription_status === 'active' ? 'selected' : '' }}>Ativo</option>
                                <option value="suspended" {{ $professional->subscription_status === 'suspended' ? 'selected' : '' }}>Suspenso</option>
                                <option value="cancelled" {{ $professional->subscription_status === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                                Atualizar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Extend Trial Form -->
                @if($professional->subscription_status === 'trial' || !$professional->subscription)
                <div class="pt-4 border-t border-gray-200">
                    <form method="POST" action="{{ route('admin.professionals.extend-trial', $professional) }}" class="space-y-3">
                        @csrf
                        <label class="text-sm font-medium text-gray-700 flex items-center">
                            <i class="fas fa-calendar-plus mr-2 text-purple-600"></i>
                            Estender Trial:
                        </label>
                        <div class="flex space-x-2">
                            <select name="days" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                                <option value="7">7 dias</option>
                                <option value="15" selected>15 dias</option>
                                <option value="30">30 dias</option>
                                <option value="60">60 dias</option>
                                <option value="90">90 dias</option>
                            </select>
                            <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                                <i class="fas fa-plus-circle mr-1"></i> Estender
                            </button>
                        </div>
                        <p class="text-xs text-gray-500">
                            @if($professional->trial_ends_at && $professional->trial_ends_at->isFuture())
                                O período será adicionado ao vencimento atual ({{ $professional->trial_ends_at->format('d/m/Y') }})
                            @else
                                Um novo período de trial será iniciado a partir de hoje
                            @endif
                        </p>
                    </form>
                </div>
                @endif
            </div>
        </div>

        <!-- Professional Info -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-user-circle mr-2 text-purple-600"></i> Informações Profissionais
                </h3>
            </div>
            <div class="p-6 space-y-4">
                @if($professional->cpf)
                <div>
                    <span class="text-sm text-gray-500">CPF:</span>
                    <span class="ml-2 font-semibold text-gray-900">{{ $professional->cpf }}</span>
                </div>
                @endif

                <div>
                    <span class="text-sm text-gray-500">Duração Padrão:</span>
                    <span class="ml-2 font-semibold text-gray-900">{{ $professional->default_duration }} minutos</span>
                </div>

                @if($professional->services)
                <div>
                    <span class="text-sm text-gray-500">Serviços:</span>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($professional->services as $service)
                            <span class="px-3 py-1 bg-purple-50 text-purple-700 rounded-full text-sm">
                                {{ is_array($service) ? $service['name'] : $service }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($professional->working_days)
                <div>
                    <span class="text-sm text-gray-500">Dias de Trabalho:</span>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @php
                            $days = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
                        @endphp
                        @foreach($professional->working_days as $day)
                            <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-sm">
                                {{ $days[$day] ?? $day }}
                            </span>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($professional->access_key)
                <div>
                    <span class="text-sm text-gray-500">Chave de Acesso:</span>
                    <span class="ml-2 font-mono font-semibold text-gray-900">{{ $professional->access_key }}</span>
                </div>
                @endif

                <div>
                    <span class="text-sm text-gray-500">WhatsApp Verificado:</span>
                    @if($professional->whatsapp_verified)
                        <span class="ml-2 text-green-600"><i class="fas fa-check-circle"></i> Sim</span>
                    @else
                        <span class="ml-2 text-red-600"><i class="fas fa-times-circle"></i> Não</span>
                    @endif
                </div>

                <div>
                    <span class="text-sm text-gray-500">Onboarding Completo:</span>
                    @if($professional->onboarding_completed)
                        <span class="ml-2 text-green-600"><i class="fas fa-check-circle"></i> Sim</span>
                    @else
                        <span class="ml-2 text-orange-600"><i class="fas fa-clock"></i> Pendente</span>
                    @endif
                </div>

                <!-- Toggle Admin -->
                <div class="pt-4 border-t border-gray-200">
                    <form method="POST" action="{{ route('admin.professionals.toggle-admin', $professional) }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 {{ $professional->is_admin ? 'bg-red-600 hover:bg-red-700' : 'bg-purple-600 hover:bg-purple-700' }} text-white rounded-lg transition">
                            <i class="fas fa-shield-alt mr-2"></i>
                            {{ $professional->is_admin ? 'Remover Permissões Admin' : 'Tornar Administrador' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Appointments -->
    @if($professional->appointments->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">
                <i class="fas fa-calendar-alt mr-2 text-purple-600"></i> Últimos Agendamentos
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serviço</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horário</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($professional->appointments as $appointment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $appointment->client_name }}</div>
                            <div class="text-xs text-gray-500">{{ $appointment->client_phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $appointment->service }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($appointment->date)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $appointment->time }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($appointment->status === 'confirmed')
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Confirmado</span>
                            @elseif($appointment->status === 'cancelled')
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Cancelado</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pendente</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
