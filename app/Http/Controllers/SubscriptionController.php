<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\AsaasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    private AsaasService $asaasService;

    public function __construct(AsaasService $asaasService)
    {
        $this->asaasService = $asaasService;
    }

    /**
     * Exibir página de assinatura
     */
    public function index()
    {
        $professional = Auth::guard('professional')->user();
        $subscription = $professional->subscription;

        // Buscar informações atualizadas do Asaas se houver assinatura ativa
        $asaasData = null;
        $payments = null;

        if ($subscription && $subscription->asaas_subscription_id) {
            $asaasData = $this->asaasService->getSubscription($subscription->asaas_subscription_id);
            $paymentsData = $this->asaasService->getSubscriptionPayments($subscription->asaas_subscription_id);
            $payments = $paymentsData['data'] ?? [];
        }

        return view('professional.subscription', compact('professional', 'subscription', 'asaasData', 'payments'));
    }

    /**
     * Ativar assinatura paga (após trial)
     */
    public function activate(Request $request)
    {
        $professional = Auth::guard('professional')->user();
        $subscription = $professional->subscription;

        if (!$subscription) {
            return back()->withErrors(['error' => 'Assinatura não encontrada']);
        }

        if ($subscription->status !== 'trial') {
            return back()->withErrors(['error' => 'Assinatura já está ativa']);
        }

        try {
            // Criar cliente no Asaas se ainda não existe
            if (!$subscription->asaas_customer_id) {
                Log::info('Tentando criar cliente no Asaas', [
                    'professional_id' => $professional->id,
                    'name' => $professional->name,
                    'cpf' => $professional->cpf ? '***'.substr($professional->cpf, -4) : 'não informado',
                ]);
                
                $customerData = $this->asaasService->createCustomer([
                    'name' => $professional->name,
                    'whatsapp' => $professional->whatsapp,
                    'cpf' => $professional->cpf,
                    'external_reference' => "professional_{$professional->id}",
                ]);

                if (!$customerData) {
                    Log::error('Falha ao criar cliente no Asaas', [
                        'professional_id' => $professional->id,
                    ]);
                    return back()->withErrors([
                        'error' => 'Não foi possível criar seu cadastro no sistema de pagamentos. Por favor, verifique se seus dados (nome e CPF) estão corretos e tente novamente. Se o problema persistir, entre em contato com o suporte.'
                    ]);
                }

                $subscription->asaas_customer_id = $customerData['id'];
                $subscription->save();
                
                Log::info('Cliente criado no Asaas com sucesso', [
                    'professional_id' => $professional->id,
                    'customer_id' => $customerData['id'],
                ]);
            }

            // Criar assinatura no Asaas
            $subscriptionData = $this->asaasService->createSubscription(
                $subscription->asaas_customer_id,
                [
                    'value' => 19.90,
                    'next_due_date' => now()->addDays(7)->format('Y-m-d'), // 7 dias após trial
                    'cycle' => 'MONTHLY',
                    'billing_type' => 'PIX',
                    'description' => 'Assinatura Mensal Zapby',
                    'external_reference' => "subscription_{$subscription->id}",
                ]
            );

            if (!$subscriptionData) {
                return back()->withErrors([
                    'error' => 'Erro ao criar assinatura no sistema de pagamentos. Por favor, tente novamente.'
                ]);
            }

            // Atualizar assinatura local
            $subscription->update([
                'asaas_subscription_id' => $subscriptionData['id'],
                'status' => 'active',
                'next_due_date' => $subscriptionData['nextDueDate'],
            ]);

            // Atualizar status do profissional
            $professional->update([
                'subscription_status' => 'active',
            ]);

            return redirect()->route('professional.subscription')
                ->with('success', 'Assinatura ativada com sucesso! Aguardando primeiro pagamento.');

        } catch (\Exception $e) {
            Log::error('Erro ao ativar assinatura', [
                'professional_id' => $professional->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Erro ao processar assinatura. Tente novamente.']);
        }
    }

    /**
     * Cancelar assinatura
     */
    public function cancel(Request $request)
    {
        $professional = Auth::guard('professional')->user();
        $subscription = $professional->subscription;

        if (!$subscription) {
            return back()->withErrors(['error' => 'Assinatura não encontrada']);
        }

        if ($subscription->status === 'cancelled') {
            return back()->withErrors(['error' => 'Assinatura já está cancelada']);
        }

        try {
            // Cancelar no Asaas se houver ID
            if ($subscription->asaas_subscription_id) {
                $cancelled = $this->asaasService->cancelSubscription($subscription->asaas_subscription_id);
                
                if (!$cancelled) {
                    Log::warning('Falha ao cancelar assinatura no Asaas', [
                        'subscription_id' => $subscription->id,
                    ]);
                }
            }

            // Atualizar status local
            $subscription->update([
                'status' => 'cancelled',
            ]);

            $professional->update([
                'subscription_status' => 'cancelled',
            ]);

            return redirect()->route('professional.subscription')
                ->with('success', 'Assinatura cancelada. Você ainda pode usar o sistema até o fim do período pago.');

        } catch (\Exception $e) {
            Log::error('Erro ao cancelar assinatura', [
                'professional_id' => $professional->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => 'Erro ao cancelar assinatura. Tente novamente.']);
        }
    }

    /**
     * Ver detalhes de um pagamento específico
     */
    public function payment($paymentId)
    {
        $professional = Auth::guard('professional')->user();
        $subscription = $professional->subscription;

        if (!$subscription || !$subscription->asaas_subscription_id) {
            return back()->withErrors(['error' => 'Assinatura não encontrada']);
        }

        $payment = $this->asaasService->getPayment($paymentId);

        if (!$payment) {
            return back()->withErrors(['error' => 'Pagamento não encontrado']);
        }

        // Buscar QR Code PIX se for pagamento PIX
        $pixQrCode = null;
        if ($payment['billingType'] === 'PIX' && $payment['status'] === 'PENDING') {
            $pixQrCode = $this->asaasService->getPixQrCode($paymentId);
        }

        return view('professional.payment', compact('payment', 'pixQrCode'));
    }
}
