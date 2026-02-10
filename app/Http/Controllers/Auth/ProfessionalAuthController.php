<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    
    public function requestCode(Request $request)
    {
        $request->validate([
            'whatsapp' => 'required|string',
        ]);
        
        // Limpar o número de WhatsApp (remover máscaras)
        $whatsapp = preg_replace('/[^0-9]/', '', $request->whatsapp);
        
        // Verificar se o profissional existe
        $professional = \App\Models\Professional::where('whatsapp', $whatsapp)->first();
        
        if (!$professional) {
            return back()->withErrors([
                'whatsapp' => 'Número de WhatsApp não cadastrado.',
            ])->onlyInput('whatsapp');
        }
        
        // Gerar código de verificação
        $code = $professional->generateVerificationCode();
        
        // Enviar código via WhatsApp
        try {
            $this->whatsappService->sendMessage(
                $whatsapp,
                "🔐 *AgendaZap - Código de Verificação*\n\nSeu código de acesso é: *{$code}*\n\nEste código expira em 10 minutos.\n\n_Não compartilhe este código com ninguém._"
            );
            
            // Armazenar o WhatsApp na sessão para a próxima etapa
            $request->session()->put('verification_whatsapp', $whatsapp);
            
            return redirect()->route('professional.verify-code');
        } catch (\Exception $e) {
            Log::error('Erro ao enviar código via WhatsApp: ' . $e->getMessage());
            
            return back()->withErrors([
                'whatsapp' => 'Erro ao enviar código. Tente novamente.',
            ])->onlyInput('whatsapp');
        }
    }
    
    public function showVerifyCodeForm(Request $request)
    {
        if (!$request->session()->has('verification_whatsapp')) {
            return redirect()->route('professional.login');
        }
        
        return view('auth.professional-verify-code');
    }
    
    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);
        
        $whatsapp = $request->session()->get('verification_whatsapp');
        
        if (!$whatsapp) {
            return redirect()->route('professional.login')->withErrors([
                'code' => 'Sessão expirada. Por favor, solicite um novo código.',
            ]);
        }
        
        $professional = \App\Models\Professional::where('whatsapp', $whatsapp)->first();
        
        if (!$professional) {
            return redirect()->route('professional.login')->withErrors([
                'code' => 'Profissional não encontrado.',
            ]);
        }
        
        if ($professional->verifyCode($request->code)) {
            // Autenticar o profissional
            Auth::guard('professional')->login($professional, true);
            
            // Limpar a sessão
            $request->session()->forget('verification_whatsapp');
            $request->session()->regenerate();
            
            return redirect()->intended(route('professional.dashboard'));
        }
        
        return back()->withErrors([
            'code' => 'Código inválido ou expirado.',
        ])->onlyInput('code');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'slug' => 'required|string',
            'password' => 'required|string',
        ]);
        
        // Tentar autenticar usando slug como identificador
        $professional = \App\Models\Professional::where('slug', $credentials['slug'])->first();
        
        if ($professional && Auth::guard('professional')->attempt(['slug' => $credentials['slug'], 'password' => $credentials['password']], $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('professional.dashboard'));
        }
        
        return back()->withErrors([
            'slug' => 'Credenciais inválidas.',
        ])->onlyInput('slug');
    }
    
    public function logout(Request $request)
    {
        Auth::guard('professional')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('professional.login');
    }
}
