@extends('layouts.admin')

@section('title', 'Dashboard Administrativo')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-3xl font-bold text-gray-900">Dashboard Administrativo</h2>
        <p class="mt-1 text-sm text-gray-600">Visão geral dos profissionais e assinaturas</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Profissionais -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Profissionais</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['total_professionals'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Em Trial -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Em Trial</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['on_trial'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Assinaturas Ativas -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Assinaturas Ativas</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['active_subscriptions'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Receita Mensal -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                    <i class="fas fa-dollar-sign text-purple-600 text-2xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Receita Mensal</dt>
                        <dd class="text-2xl font-bold text-gray-900">R$ {{ number_format($stats['monthly_revenue'], 2, ',', '.') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Trial Expirado -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                    <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Trial Expirado</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['trial_expired'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Suspensas -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-orange-100 rounded-md p-3">
                    <i class="fas fa-pause-circle text-orange-600 text-2xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Suspensas</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['suspended_subscriptions'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Canceladas -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-gray-100 rounded-md p-3">
                    <i class="fas fa-times-circle text-gray-600 text-2xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Canceladas</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['cancelled_subscriptions'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Vencendo em 7 dias -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                    <i class="fas fa-calendar-alt text-indigo-600 text-2xl"></i>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Vencendo em 7 dias</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $stats['expiring_in_7_days'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input 
                    type="text" 
                    name="search" 
                    id="search" 
                    value="{{ request('search') }}"
                    placeholder="Nome, WhatsApp, profissão..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                >
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select 
                    name="status" 
                    id="status"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                >
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Todos</option>
                    <option value="trial" {{ request('status') == 'trial' ? 'selected' : '' }}>Em Trial</option>
                    <option value="trial_expired" {{ request('status') == 'trial_expired' ? 'selected' : '' }}>Trial Expirado</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativo</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspenso</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition">
                    <i class="fas fa-search mr-2"></i> Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Professionals List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Profissionais ({{ $professionals->total() }})</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profissional</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plano</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cadastro</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($professionals as $professional)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                                    <span class="text-purple-600 font-semibold">{{ substr($professional->name, 0, 1) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $professional->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $professional->profession }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $professional->whatsapp }}</div>
                            <div class="text-xs text-gray-500">{{ $professional->slug }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($professional->subscription_status === 'trial')
                                @if($professional->trial_ends_at && $professional->trial_ends_at->isFuture())
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i> Trial
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-circle mr-1"></i> Expirado
                                    </span>
                                @endif
                            @elseif($professional->subscription && $professional->subscription->status === 'active')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Ativo
                                </span>
                            @elseif($professional->subscription && $professional->subscription->status === 'suspended')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                    <i class="fas fa-pause-circle mr-1"></i> Suspenso
                                </span>
                            @elseif($professional->subscription && $professional->subscription->status === 'cancelled')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    <i class="fas fa-times-circle mr-1"></i> Cancelado
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Sem plano
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($professional->subscription)
                                R$ {{ number_format($professional->subscription->amount, 2, ',', '.') }}/mês
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($professional->subscription_status === 'trial' && $professional->trial_ends_at)
                                <div class="text-sm text-gray-900">{{ $professional->trial_ends_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $professional->trial_ends_at->diffForHumans() }}</div>
                            @elseif($professional->subscription && $professional->subscription->next_due_date)
                                <div class="text-sm text-gray-900">{{ $professional->subscription->next_due_date->format('d/m/Y') }}</div>
                                @php
                                    $daysUntilDue = now()->diffInDays($professional->subscription->next_due_date, false);
                                @endphp
                                @if($daysUntilDue >= 0 && $daysUntilDue <= 7)
                                    <div class="text-xs text-red-600 font-semibold">em {{ $daysUntilDue }} dias</div>
                                @else
                                    <div class="text-xs text-gray-500">{{ $professional->subscription->next_due_date->diffForHumans() }}</div>
                                @endif
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $professional->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('admin.professionals.show', $professional) }}" class="text-purple-600 hover:text-purple-900">
                                <i class="fas fa-eye mr-1"></i> Detalhes
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                            <p>Nenhum profissional encontrado</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($professionals->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $professionals->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
