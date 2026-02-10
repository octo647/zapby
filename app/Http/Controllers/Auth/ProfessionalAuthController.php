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
            return redirect()->route('professional.dashboard');
        }
        
        return view('auth.professional-login');
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'whatsapp' => 'required|string',
            'password' => 'required|string',
        ]);
        
        // Limpar o número de WhatsApp (remover máscaras)
        $whatsapp = preg_replace('/[^0-9]/', '', $request->whatsapp);
        
        // Buscar profissional pelo WhatsApp
        $professional = \App\Models\Professional::where('whatsapp', $whatsapp)->first();
        
        if (!$professional) {
            return back()->withErrors([
                'whatsapp' => 'Número de WhatsApp não cadastrado.',
            ])->onlyInput('whatsapp');
        }
        
        // Verificar senha
        if (!Hash::check($request->password, $professional->password)) {
            return back()->withErrors([
                'password' => 'Senha incorreta.',
            ])->onlyInput('whatsapp');
        }
        
        // Autenticar o profissional
        Auth::guard('professional')->login($professional, $request->filled('remember'));
        $request->session()->regenerate();
        
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
            'whatsapp' => 'required|string',
            'profession' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);
        
        // Limpar o número de WhatsApp (remover máscaras)
        $whatsapp = preg_replace('/[^0-9]/', '', $request->whatsapp);
        
        // Verificar se o WhatsApp já está cadastrado
        if (\App\Models\Professional::where('whatsapp', $whatsapp)->exists()) {
            return back()->withErrors([
                'whatsapp' => 'Este número de WhatsApp já está cadastrado.',
            ])->onlyInput('name', 'profession');
        }
        
        // Armazenar dados temporários na sessão
        $request->session()->put('register_data', [
            'name' => $request->name,
            'whatsapp' => $whatsapp,
            'profession' => $request->profession,
            'password' => Hash::make($request->password),
        ]);
        
        // Criar profissional temporário para gerar código
        $tempProfessional = new \App\Models\Professional();
        $tempProfessional->whatsapp = $whatsapp;
        $code = $tempProfessional->generateVerificationCode();
        
        // Salvar código na sessão (já que o profissional ainda não foi criado)
        $request->session()->put('verification_code', $code);
        $request->session()->put('verification_code_expires_at', now()->addMinutes(10));
        
        // Enviar código via WhatsApp
        try {
            $this->whatsappService->sendMessage(
                $whatsapp,
                "🎉 *Bem-vindo ao AgendaZap!*\n\nPara confirmar seu cadastro, digite o código: *{$code}*\n\nEste código expira em 10 minutos."
            );
            
            return redirect()->route('professional.verify-registration');
        } catch (\Exception $e) {
            Log::error('Erro ao enviar código via WhatsApp no registro: ' . $e->getMessage());
            
            return back()->withErrors([
                'whatsapp' => 'Erro ao enviar código de verificação. Verifique o número e tente novamente.',
            ])->withInput();
        }
    }
    
    public function showVerifyRegistrationForm(Request $request)
    {
        if (!$request->session()->has('register_data')) {
            return redirect()->route('professional.register');
        }
        
        return view('auth.professional-verify-registration');
    }
    
    public function verifyRegistration(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);
        
        $registerData = $request->session()->get('register_data');
        $storedCode = $request->session()->get('verification_code');
        $expiresAt = $request->session()->get('verification_code_expires_at');
        
        if (!$registerData || !$storedCode || !$expiresAt) {
            return redirect()->route('professional.register')->withErrors([
                'code' => 'Sessão expirada. Por favor, faça o cadastro novamente.',
            ]);
        }
        
        // Verificar se o código expirou
        if (now()->gt($expiresAt)) {
            return back()->withErrors([
                'code' => 'Código expirado. Solicite um novo código.',
            ]);
        }
        
        // Verificar código
        if ($request->code !== $storedCode) {
            return back()->withErrors([
                'code' => 'Código inválido.',
            ])->onlyInput('code');
        }
        
        // Criar slug único
        $slug = \Illuminate\Support\Str::slug($registerData['name']);
        $originalSlug = $slug;
        $counter = 1;
        
        while (\App\Models\Professional::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        // Criar profissional
        $professional = \App\Models\Professional::create([
            'name' => $registerData['name'],
            'slug' => $slug,
            'whatsapp' => $registerData['whatsapp'],
            'profession' => $registerData['profession'],
            'password' => $registerData['password'],
            'whatsapp_verified' => true,
            'trial_ends_at' => now()->addDays(30),
        ]);
        
        // Limpar sessão
        $request->session()->forget(['register_data', 'verification_code', 'verification_code_expires_at']);
        
        // Autenticar automaticamente
        Auth::guard('professional')->login($professional);
        $request->session()->regenerate();
        
        return redirect()->route('professional.dashboard')->with('success', 'Cadastro realizado com sucesso! Aproveite seus 30 dias de teste grátis.');
    }
    
    public function logout(Request $request)
    {
        Auth::guard('professional')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('professional.login');
    }
}
