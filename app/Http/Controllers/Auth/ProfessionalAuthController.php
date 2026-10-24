<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfessionalAuthController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function showLoginForm()
    {
        if (Auth::guard('professional')->check()) {
            $professional = Auth::guard('professional')->user();
            
            // Redirecionar admin para dashboard admin
            if ($professional->is_admin) {
                return redirect()->route('admin.dashboard');
            }
            
            return redirect()->route('professional.dashboard');
        }
        
        return view('auth.professional-login');
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'cpf' => 'required|string',
            'password' => 'required|string',
        ]);
        
        // Limpar o CPF (remover máscaras)
        $cpf = preg_replace('/[^0-9]/', '', $request->cpf);
        
        // Buscar profissional pelo CPF
        $professional = \App\Models\Professional::where('cpf', $cpf)->first();
        
        if (!$professional) {
            return back()->withErrors([
                'cpf' => 'CPF não cadastrado.',
            ])->onlyInput('cpf');
        }
        
        // Verificar senha
        if (!Hash::check($request->password, $professional->password)) {
            return back()->withErrors([
                'password' => 'Senha incorreta.',
            ])->onlyInput('cpf');
        }
        
        // Autenticar o profissional
        Auth::guard('professional')->login($professional, $request->filled('remember'));
        $request->session()->regenerate();
        
        // Redirecionar admin para dashboard admin
        if ($professional->is_admin) {
            return redirect()->intended(route('admin.dashboard'));
        }
        
        return redirect()->intended(route('professional.dashboard'));
    }
    
    // Registro - Etapa 1: Solicitar informações básicas
    public function showRegisterForm()
    {
        return view('auth.professional-register');
    }
    
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|min:11|max:14',
            'whatsapp' => 'required|string',
            'profession' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'referral_code' => 'nullable|string|max:30',
        ]);

        $seller = null;
        if (filled($request->referral_code)) {
            $seller = \App\Models\Seller::where('code', \App\Models\Seller::normalizeCode($request->referral_code))
                ->where('active', true)->first();
            if (!$seller) {
                return back()->withErrors([
                    'referral_code' => 'Código de indicação inválido.',
                ])->withInput();
            }
        }
        
        // Limpar CPF e WhatsApp (remover máscaras)
        $cpf = preg_replace('/[^0-9]/', '', $request->cpf);
        $whatsapp = preg_replace('/[^0-9]/', '', $request->whatsapp);
        
        // Validar tamanho do CPF após limpar
        if (strlen($cpf) !== 11) {
            return back()->withErrors([
                'cpf' => 'CPF inválido.',
            ])->withInput();
        }
        
        // Verificar se o CPF já está cadastrado
        if (\App\Models\Professional::where('cpf', $cpf)->exists()) {
            return back()->withErrors([
                'cpf' => 'Este CPF já está cadastrado.',
            ])->onlyInput('name', 'whatsapp', 'profession');
        }
        
        // Verificar se o WhatsApp já está cadastrado
        if (\App\Models\Professional::where('whatsapp', $whatsapp)->exists()) {
            return back()->withErrors([
                'whatsapp' => 'Este número de WhatsApp já está cadastrado.',
            ])->onlyInput('name', 'cpf', 'profession');
        }
        
        // Criar slug único
        $slug = \Illuminate\Support\Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;
        
        while (\App\Models\Professional::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
         
        // Criar profissional
        $professional = \App\Models\Professional::create([
            'name' => $request->name,
            'slug' => $slug,
            'cpf' => $cpf,
            'whatsapp' => $whatsapp,
            'profession' => $request->profession,
            'access_key' => null,
            'seller_id' => $seller?->id,
            'password' => Hash::make($request->password),
            'whatsapp_verified' => true,
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays(7),
        ]);
        $professional->update([
            'access_key' => \App\Models\Professional::generateAccessKey($professional->profession, $professional->id),
        ]);
        
        // Criar assinatura em trial
        \App\Models\Subscription::create([
            'professional_id' => $professional->id,
            'status' => 'trial',
            'amount' => 19.90,
            'next_due_date' => now()->addDays(7),
        ]);
        
        // Notificar administrador sobre novo cadastro
        try {
            $this->whatsappService->notifyAdminNewProfessional($professional);
        } catch (\Exception $e) {
            Log::error('Erro ao notificar admin sobre novo cadastro', [
                'professional_id' => $professional->id,
                'error' => $e->getMessage()
            ]);
        }
        
        // Autenticar automaticamente
        Auth::guard('professional')->login($professional);
        $request->session()->regenerate();
        
        return redirect()->route('professional.dashboard')
            ->with('success', "Cadastro realizado com sucesso! Você tem 7 dias de teste gratuito. Sua chave de acesso para clientes agendarem pelo WhatsApp é: {$professional->access_key}. Após o período de teste, a assinatura será de R$ 19,90/mês.");
    }
    
    
    public function showVerifyRegistrationForm(Request $request)
    {
        // Redirecionar para registro (não usa mais verificação de código)
        return redirect()->route('professional.register');
    }
    
    public function verifyRegistration(Request $request)
    {
        // Redirecionar para registro (não usa mais verificação de código)
        return redirect()->route('professional.register');
    }
    
    public function logout(Request $request)
    {
        Auth::guard('professional')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('professional.login');
    }
    
    // Recuperação de senha - Etapa 1: Solicitar CPF
    public function showForgotPasswordForm()
    {
        return view('auth.professional-forgot-password');
    }
    
    public function sendResetCode(Request $request)
    {
        $request->validate([
            'cpf' => 'required|string|min:11|max:14',
        ]);
        
        // Limpar CPF
        $cpf = preg_replace('/[^0-9]/', '', $request->cpf);
        
        // Buscar profissional
        $professional = \App\Models\Professional::where('cpf', $cpf)->first();
        
        if (!$professional) {
            return back()->withErrors([
                'cpf' => 'CPF não cadastrado.',
            ])->withInput();
        }
        
        // Gerar código de 6 dígitos
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Salvar código na sessão com timestamp
        session([
            'password_reset_code' => $code,
            'password_reset_cpf' => $cpf,
            'password_reset_expires' => now()->addMinutes(10),
        ]);
        
        // Enviar código via WhatsApp
        try {
            $message = "🔐 *Recuperação de Senha - ZapBy*\n\n";
            $message .= "Seu código de verificação é: *{$code}*\n\n";
            $message .= "Este código expira em 10 minutos.\n\n";
            $message .= "Se você não solicitou esta recuperação, ignore esta mensagem.";
            
            $sent = $this->whatsappService->sendMessage($professional->whatsapp, $message);
            
            Log::info('Código de recuperação enviado', [
                'cpf' => $cpf,
                'whatsapp' => $professional->whatsapp,
                'sent' => $sent,
            ]);
            
            // Verificar se o contato existe no bot
            $contactsFile = storage_path('app/whatsapp_contacts.json');
            $hasContact = false;
            
            if (file_exists($contactsFile)) {
                $contacts = json_decode(file_get_contents($contactsFile), true) ?? [];
                foreach ($contacts as $contact) {
                    $contactPhone = preg_replace('/\D/', '', $contact['phone'] ?? '');
                    $profPhone = preg_replace('/\D/', '', $professional->whatsapp);
                    
                    if ($contactPhone === $profPhone || 
                        $contactPhone === '55' . $profPhone ||
                        '55' . $contactPhone === $profPhone) {
                        $hasContact = true;
                        break;
                    }
                }
            }
            
            if (!$hasContact) {
                return redirect()->route('professional.password.reset')
                    ->with('warning', 'Código enviado! ⚠️ Se você nunca recebeu mensagens do ZapBy antes, o código pode não chegar. Nesse caso, entre em contato com o suporte.');
            }
        } catch (\Exception $e) {
            Log::error('Erro ao enviar código de recuperação', [
                'error' => $e->getMessage(),
                'cpf' => $cpf,
            ]);
        }
        
        return redirect()->route('professional.password.reset')
            ->with('success', 'Código de verificação enviado para seu WhatsApp!');
    }
    
    // Recuperação de senha - Etapa 2: Validar código e resetar senha
    public function showResetPasswordForm()
    {
        // Verificar se há uma sessão de recuperação ativa
        if (!session('password_reset_cpf')) {
            return redirect()->route('professional.password.request')
                ->withErrors(['error' => 'Sessão expirada. Por favor, solicite um novo código.']);
        }
        
        // Verificar se o código expirou
        if (now()->isAfter(session('password_reset_expires'))) {
            session()->forget(['password_reset_code', 'password_reset_cpf', 'password_reset_expires']);
            return redirect()->route('professional.password.request')
                ->withErrors(['error' => 'Código expirado. Por favor, solicite um novo código.']);
        }
        
        return view('auth.professional-reset-password');
    }
    
    public function resetPassword(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'password' => 'required|string|min:6|confirmed',
        ]);
        
        // Verificar se há uma sessão de recuperação ativa
        if (!session('password_reset_cpf')) {
            return back()->withErrors(['error' => 'Sessão expirada. Por favor, solicite um novo código.']);
        }
        
        // Verificar se o código expirou
        if (now()->isAfter(session('password_reset_expires'))) {
            session()->forget(['password_reset_code', 'password_reset_cpf', 'password_reset_expires']);
            return redirect()->route('professional.password.request')
                ->withErrors(['error' => 'Código expirado. Por favor, solicite um novo código.']);
        }
        
        // Verificar código
        if ($request->code !== session('password_reset_code')) {
            return back()->withErrors(['code' => 'Código inválido.'])->withInput();
        }
        
        // Buscar profissional
        $professional = \App\Models\Professional::where('cpf', session('password_reset_cpf'))->first();
        
        if (!$professional) {
            session()->forget(['password_reset_code', 'password_reset_cpf', 'password_reset_expires']);
            return redirect()->route('professional.password.request')
                ->withErrors(['error' => 'Profissional não encontrado.']);
        }
        
        // Atualizar senha
        $professional->password = Hash::make($request->password);
        $professional->save();
        
        // Limpar sessão
        session()->forget(['password_reset_code', 'password_reset_cpf', 'password_reset_expires']);
        
        Log::info('Senha resetada com sucesso', ['cpf' => $professional->cpf]);
        
        return redirect()->route('professional.login')
            ->with('success', 'Senha alterada com sucesso! Faça login com sua nova senha.');
    }
}
