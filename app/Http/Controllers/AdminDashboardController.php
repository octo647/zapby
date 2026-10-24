<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use App\Models\Subscription;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Estatísticas gerais
        $stats = $this->getGeneralStats();
        
        // Profissionais com filtros
        $professionals = $this->getProfessionalsWithFilters(request());
        
        return view('admin.dashboard', compact('stats', 'professionals'));
    }

    private function getGeneralStats()
    {
        $totalProfessionals = Professional::count();
        
        // Profissionais em trial
        $onTrial = Professional::where('subscription_status', 'trial')
            ->where('trial_ends_at', '>', now())
            ->count();
            
        // Trial expirado
        $trialExpired = Professional::where('subscription_status', 'trial')
            ->where('trial_ends_at', '<=', now())
            ->count();
        
        // Assinaturas ativas
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        
        // Assinaturas suspensas
        $suspendedSubscriptions = Subscription::where('status', 'suspended')->count();
        
        // Assinaturas canceladas
        $cancelledSubscriptions = Subscription::where('status', 'cancelled')->count();
        
        // Receita mensal (soma dos planos ativos)
        $monthlyRevenue = Subscription::whereIn('status', ['active', 'trial'])
            ->sum('amount');
        
        // Agendamentos do mês
        $appointmentsThisMonth = Appointment::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();
        
        // Profissionais que vão vencer em 7 dias
        $expiringIn7Days = Subscription::where('status', 'active')
            ->whereBetween('next_due_date', [now(), now()->addDays(7)])
            ->count();

        return [
            'total_professionals' => $totalProfessionals,
            'on_trial' => $onTrial,
            'trial_expired' => $trialExpired,
            'active_subscriptions' => $activeSubscriptions,
            'suspended_subscriptions' => $suspendedSubscriptions,
            'cancelled_subscriptions' => $cancelledSubscriptions,
            'monthly_revenue' => $monthlyRevenue,
            'appointments_this_month' => $appointmentsThisMonth,
            'expiring_in_7_days' => $expiringIn7Days,
        ];
    }

    private function getProfessionalsWithFilters(Request $request)
    {
        $query = Professional::with('subscription')
            ->orderBy('created_at', 'desc');
        
        // Filtro por status
        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'trial') {
                $query->where('subscription_status', 'trial')
                    ->where('trial_ends_at', '>', now());
            } elseif ($request->status === 'trial_expired') {
                $query->where('subscription_status', 'trial')
                    ->where('trial_ends_at', '<=', now());
            } else {
                // Para subscription_status direto
                $query->whereHas('subscription', function($q) use ($request) {
                    $q->where('status', $request->status);
                });
            }
        }
        
        // Filtro por busca (nome, whatsapp, email)
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('whatsapp', 'like', "%{$search}%")
                  ->orWhere('profession', 'like', "%{$search}%");
            });
        }
        
        return $query->paginate(20);
    }

    public function show(Professional $professional)
    {
        $professional->load(['subscription', 'seller', 'appointments' => function($query) {
            $query->orderBy('date', 'desc')->orderBy('time', 'desc')->take(10);
        }]);
        
        // Estatísticas do profissional
        $professionalStats = [
            'total_appointments' => $professional->appointments()->count(),
            'appointments_this_month' => $professional->appointments()
                ->whereMonth('date', now()->month)
                ->whereYear('date', now()->year)
                ->count(),
            'upcoming_appointments' => $professional->appointments()
                ->where('date', '>=', now()->format('Y-m-d'))
                ->count(),
        ];
        
        return view('admin.professional-details', compact('professional', 'professionalStats'));
    }

    public function toggleAdmin(Professional $professional)
    {
        $professional->update([
            'is_admin' => !$professional->is_admin
        ]);

        return back()->with('success', 'Permissões de administrador atualizadas com sucesso!');
    }

    public function updateSubscriptionStatus(Professional $professional, Request $request)
    {
        $request->validate([
            'status' => 'required|in:trial,active,suspended,cancelled'
        ]);

        if ($professional->subscription) {
            $professional->subscription->update([
                'status' => $request->status
            ]);
        }

        $professional->update([
            'subscription_status' => $request->status
        ]);

        return back()->with('success', 'Status da assinatura atualizado com sucesso!');
    }

    public function extendTrial(Professional $professional, Request $request)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:365'
        ]);

        $days = (int) $request->days;
        
        // Se não tiver trial_ends_at ou estiver no passado, começa de agora
        $currentTrialEnd = $professional->trial_ends_at && $professional->trial_ends_at->isFuture() 
            ? $professional->trial_ends_at 
            : now();
        
        $newTrialEnd = $currentTrialEnd->copy()->addDays($days);
        
        $professional->update([
            'trial_ends_at' => $newTrialEnd,
            'subscription_status' => 'trial'
        ]);

        // Atualizar next_due_date da subscription se ela existir
        if ($professional->subscription) {
            $professional->subscription->update([
                'next_due_date' => $newTrialEnd
            ]);
        }

        return back()->with('success', "Trial estendido por {$days} dias! Novo vencimento: {$newTrialEnd->format('d/m/Y')}");
    }
}
