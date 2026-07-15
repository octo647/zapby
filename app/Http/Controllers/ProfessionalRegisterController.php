<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfessionalRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register-professional');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|string|unique:professionals,cpf',
            'whatsapp' => 'required|string|unique:professionals,whatsapp',
            'profession' => 'required|string|max:255',
            'default_duration' => 'required|integer|min:15|max:240',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'O nome é obrigatório',
            'cpf.required' => 'O CPF é obrigatório',
            'cpf.unique' => 'Este CPF já está cadastrado',
            'whatsapp.required' => 'O WhatsApp é obrigatório',
            'whatsapp.unique' => 'Este WhatsApp já está cadastrado',
            'profession.required' => 'A profissão é obrigatória',
            'password.required' => 'A senha é obrigatória',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres',
            'password.confirmed' => 'As senhas não coincidem',
        ]);

        // Limpar CPF e WhatsApp
        $cpf = preg_replace('/\D/', '', $validated['cpf']);
        $whatsapp = preg_replace('/\D/', '', $validated['whatsapp']);

        // Gerar slug único
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        
        while (Professional::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        

        // Criar profissional
        $professional = Professional::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'cpf' => $cpf,
            'whatsapp' => $whatsapp,
            'profession' => $validated['profession'],
            'default_duration' => $validated['default_duration'],
            'access_key' => null,
            'password' => Hash::make($validated['password']),
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays(7), // 7 dias de trial
        ]);
        $professional->update([
            'access_key' => Professional::generateAccessKey($professional->profession, $professional->id),
        ]);

        // Criar assinatura em trial
        Subscription::create([
            'professional_id' => $professional->id,
            'status' => 'trial',
            'amount' => 19.90,
            'next_due_date' => now()->addDays(7),
        ]);

        // Login automático
        auth('professional')->login($professional);

        return redirect()->route('professional.dashboard')
            ->with('success', 'Cadastro realizado com sucesso! Você tem 7 dias de teste gratuito. Sua chave de acesso é: ' . $professional->access_key);
    }
}
