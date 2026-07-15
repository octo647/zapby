<?php

namespace App\Services;

use App\Models\Professional;
use App\Models\Appointment;
use App\Models\TimeBlock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class WhatsAppBotService
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Processa mensagem recebida do profissional ou cliente
     */
    public function processMessage(string $phone, string $message, ?string $name = null): void
    {
        $phone = $this->normalizePhone($phone);
        
        // Verifica se é um profissional cadastrado
        $professional = Professional::where('whatsapp', $phone)->first();
        
        if ($professional) {
            $this->handleProfessionalMessage($professional, $message);
        } else {
            // Verifica se está em processo de cadastro
            $state = Cache::get("registration_state_{$phone}");
            
            if ($state) {
                $this->handleRegistration($phone, $message, $name);
            } else {
                // Nova conversa - oferece cadastro ou busca de profissional
                $this->sendWelcomeMessage($phone, $name);
            }
        }
    }

    /**
     * Mensagem de boas-vindas
     */
    protected function sendWelcomeMessage(string $phone, ?string $name): void
    {
        $greeting = $name ? "Olá {$name}!" : "Olá!";
        
        $message = "{$greeting} 👋\n\n";
        $message .= "Bem-vindo ao *Zapby*!\n\n";
        $message .= "Sou um assistente para agendamentos.\n\n";
        $message .= "O que você gostaria de fazer?\n\n";
        $message .= "1️⃣ Criar minha agenda (profissionais)\n";
        $message .= "2️⃣ Agendar um serviço (clientes)\n\n";
        $message .= "Digite *1* ou *2*";

        $this->whatsappService->sendMessage($phone, $message);
        Cache::put("conversation_state_{$phone}", 'welcome', now()->addMinutes(10));
    }

    /**
     * Gerencia mensagens de profissionais cadastrados
     */
    protected function handleProfessionalMessage(Professional $professional, string $message): void
    {
        $message = strtolower(trim($message));
        
        // Menu principal
        if (in_array($message, ['menu', 'início', 'inicio', 'oi', 'olá', 'ola'])) {
            $this->sendProfessionalMenu($professional);
            return;
        }

        // Comandos específicos
        switch ($message) {
            case '1':
            case 'agendamentos':
                $this->showAppointments($professional);
                break;
            
            case '2':
            case 'hoje':
                $this->showTodayAppointments($professional);
                break;
            
            case '3':
            case 'link':
                $this->sendAgendaLink($professional);
                break;
            
            case '4':
            case 'horários':
            case 'horarios':
                $this->showScheduleInfo($professional);
                break;
            
            case '5':
            case 'ajuda':
                $this->sendHelp($professional);
                break;
            
            default:
                // Verifica se é confirmação/cancelamento de agendamento
                if (preg_match('/^(confirmar|cancelar)\s+(\d+)$/i', $message, $matches)) {
                    $action = strtolower($matches[1]);
                    $appointmentId = $matches[2];
                    $this->handleAppointmentAction($professional, $action, $appointmentId);
                } else {
                    $this->sendProfessionalMenu($professional);
                }
                break;
        }
    }

    /**
     * Menu principal do profissional
     */
    protected function sendProfessionalMenu(Professional $professional): void
    {
        $message = "🏠 *Menu Principal*\n\n";
        $message .= "Olá {$professional->name}!\n\n";
        $message .= "Escolha uma opção:\n\n";
        $message .= "1️⃣ Ver todos agendamentos\n";
        $message .= "2️⃣ Agendamentos de hoje\n";
        $message .= "3️⃣ Meu link de agendamento\n";
        $message .= "4️⃣ Meus horários\n";
        $message .= "5️⃣ Ajuda\n\n";
        $message .= "Digite o número da opção";

        $this->whatsappService->sendMessage($professional->whatsapp, $message);
    }

    /**
     * Mostra todos os agendamentos futuros
     */
    protected function showAppointments(Professional $professional): void
    {
        $appointments = $professional->appointments()
            ->where('date', '>=', now()->format('Y-m-d'))
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('date')
            ->orderBy('time')
            ->limit(10)
            ->get();

        if ($appointments->isEmpty()) {
            $message = "📅 *Agendamentos*\n\n";
            $message .= "Você não tem agendamentos futuros.\n\n";
            $message .= "Digite *menu* para voltar";
        } else {
            $message = "📅 *Próximos Agendamentos*\n\n";
            
            foreach ($appointments as $apt) {
                $status = $apt->status === 'confirmed' ? '✅' : '⏳';
                $date = $apt->date->format('d/m/Y');
                $time = $apt->time->format('H:i');
                
                $message .= "{$status} *#{$apt->id}*\n";
                $message .= "📆 {$date} às {$time}\n";
                $message .= "👤 {$apt->client_name}\n";
                $message .= "📱 " . ($apt->client_contact_phone ?? $apt->client_whatsapp) . "\n";
                
                if ($apt->status === 'pending') {
                    $message .= "▸ Para confirmar: *confirmar {$apt->id}*\n";
                    $message .= "▸ Para cancelar: *cancelar {$apt->id}*\n";
                }
                
                $message .= "\n";
            }
            
            $message .= "Digite *menu* para voltar";
        }

        $this->whatsappService->sendMessage($professional->whatsapp, $message);
    }

    /**
     * Mostra agendamentos de hoje
     */
    protected function showTodayAppointments(Professional $professional): void
    {
        $appointments = $professional->appointments()
            ->where('date', now()->format('Y-m-d'))
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('time')
            ->get();

        if ($appointments->isEmpty()) {
            $message = "📅 *Agendamentos de Hoje*\n\n";
            $message .= "Nenhum agendamento para hoje.\n\n";
            $message .= "Digite *menu* para voltar";
        } else {
            $message = "📅 *Hoje - " . now()->format('d/m/Y') . "*\n\n";
            
            foreach ($appointments as $apt) {
                $status = $apt->status === 'confirmed' ? '✅' : '⏳';
                $time = $apt->time->format('H:i');
                
                $message .= "{$status} *{$time}* - {$apt->client_name}\n";
                $message .= "📱 " . ($apt->client_contact_phone ?? $apt->client_whatsapp) . "\n\n";
            }
            
            $message .= "Digite *menu* para voltar";
        }

        $this->whatsappService->sendMessage($professional->whatsapp, $message);
    }

    /**
     * Envia link da agenda
     */
    protected function sendAgendaLink(Professional $professional): void
    {
        $link = url("/{$professional->slug}");
        
        $message = "🔗 *Seu Link de Agendamento*\n\n";
        $message .= "{$link}\n\n";
        $message .= "Compartilhe este link para seus clientes agendarem online!\n\n";
        $message .= "Digite *menu* para voltar";

        $this->whatsappService->sendMessage($professional->whatsapp, $message);
    }

    /**
     * Mostra informações de horário
     */
    protected function showScheduleInfo(Professional $professional): void
    {
        $days = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        $workingDaysNames = array_map(fn($d) => $days[$d], $professional->working_days ?? []);
        
        $message = "⏰ *Meus Horários*\n\n";
        $message .= "📅 Dias: " . implode(', ', $workingDaysNames) . "\n";
        $message .= "🕐 Horário: {$professional->working_hours['start']} às {$professional->working_hours['end']}\n";
        $message .= "⏱️ Duração: {$professional->default_duration} minutos\n\n";
        $message .= "Para alterar, digite: *alterar horários*\n\n";
        $message .= "Digite *menu* para voltar";

        $this->whatsappService->sendMessage($professional->whatsapp, $message);
    }

    /**
     * Envia ajuda
     */
    protected function sendHelp(Professional $professional): void
    {
        $message = "ℹ️ *Ajuda*\n\n";
        $message .= "Comandos disponíveis:\n\n";
        $message .= "• *menu* - Menu principal\n";
        $message .= "• *agendamentos* - Ver todos\n";
        $message .= "• *hoje* - Ver hoje\n";
        $message .= "• *link* - Seu link\n";
        $message .= "• *horários* - Seus horários\n\n";
        $message .= "• *confirmar ID* - Confirmar agendamento\n";
        $message .= "• *cancelar ID* - Cancelar agendamento\n\n";
        $message .= "Exemplo: confirmar 123\n\n";
        $message .= "Digite *menu* para voltar";

        $this->whatsappService->sendMessage($professional->whatsapp, $message);
    }

    /**
     * Confirma ou cancela agendamento
     */
    protected function handleAppointmentAction(Professional $professional, string $action, int $appointmentId): void
    {
        $appointment = Appointment::where('id', $appointmentId)
            ->where('professional_id', $professional->id)
            ->first();

        if (!$appointment) {
            $message = "❌ Agendamento #{$appointmentId} não encontrado.\n\n";
            $message .= "Digite *agendamentos* para ver a lista";
            $this->whatsappService->sendMessage($professional->whatsapp, $message);
            return;
        }

        if ($action === 'confirmar') {
            $appointment->update(['status' => 'confirmed']);
            $this->whatsappService->confirmAppointmentToClient($appointment);
            
            $message = "✅ Agendamento #{$appointmentId} confirmado!\n\n";
            $message .= "Cliente notificado via WhatsApp.\n\n";
            $message .= "Digite *menu* para voltar";
        } else {
            $appointment->update(['status' => 'cancelled']);
            $this->whatsappService->notifyCancellation($appointment);
            
            $message = "❌ Agendamento #{$appointmentId} cancelado.\n\n";
            $message .= "Cliente notificado via WhatsApp.\n\n";
            $message .= "Digite *menu* para voltar";
        }

        $this->whatsappService->sendMessage($professional->whatsapp, $message);
    }

    /**
     * Processo de cadastro do profissional
     */
    protected function handleRegistration(string $phone, string $message, ?string $name): void
    {
        $state = Cache::get("registration_state_{$phone}");
        $data = Cache::get("registration_data_{$phone}", []);

        switch ($state) {
            case 'awaiting_name':
                $data['name'] = $message;
                Cache::put("registration_data_{$phone}", $data, now()->addMinutes(10));
                Cache::put("registration_state_{$phone}", 'awaiting_profession', now()->addMinutes(10));
                
                $response = "Ótimo, {$message}! 👍\n\n";
                $response .= "Qual sua profissão?\n\n";
                $response .= "1️⃣ Cabeleireiro(a)\n";
                $response .= "2️⃣ Manicure\n";
                $response .= "3️⃣ Barbeiro(a)\n";
                $response .= "4️⃣ Esteticista\n";
                $response .= "5️⃣ Outro\n\n";
                $response .= "Digite o número";
                
                $this->whatsappService->sendMessage($phone, $response);
                break;

            case 'awaiting_profession':
                $professions = [
                    '1' => 'Cabeleireiro(a)',
                    '2' => 'Manicure',
                    '3' => 'Barbeiro(a)',
                    '4' => 'Esteticista',
                    '5' => 'Outro'
                ];
                
                if (isset($professions[$message])) {
                    $data['profession'] = $professions[$message];
                } else {
                    $data['profession'] = $message;
                }
                
                Cache::put("registration_data_{$phone}", $data, now()->addMinutes(10));
                Cache::put("registration_state_{$phone}", 'awaiting_duration', now()->addMinutes(10));
                
                $response = "Perfeito! ✨\n\n";
                $response .= "Quanto tempo dura um atendimento em média?\n\n";
                $response .= "1️⃣ 30 minutos\n";
                $response .= "2️⃣ 1 hora\n";
                $response .= "3️⃣ 1h30\n";
                $response .= "4️⃣ 2 horas\n\n";
                $response .= "Digite o número";
                
                $this->whatsappService->sendMessage($phone, $response);
                break;

            case 'awaiting_duration':
                $durations = [
                    '1' => 30,
                    '2' => 60,
                    '3' => 90,
                    '4' => 120
                ];
                
                $data['duration'] = $durations[$message] ?? 60;
                
                // Criar profissional
                $slug = Str::slug($data['name']);
                $baseSlug = $slug;
                $counter = 1;
                
                while (Professional::where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $professional = Professional::create([
                    'name' => $data['name'],
                    'slug' => $slug,
                    'whatsapp' => $phone,
                    'profession' => $data['profession'],
                    'default_duration' => $data['duration'],
                    'working_days' => [1, 2, 3, 4, 5, 6], // Seg a Sáb por padrão
                    'working_hours' => [
                        'start' => '08:00',
                        'end' => '19:00',
                    ],
                    'subscription_status' => 'trial',
                    'trial_ends_at' => Carbon::now()->addDays(14),
                    'password' => Hash::make(Str::random(16)),
                ]);
                
                // Limpar cache
                Cache::forget("registration_state_{$phone}");
                Cache::forget("registration_data_{$phone}");
                
                $link = url("/{$professional->slug}");
                
                $response = "🎉 *Pronto! Sua agenda está no ar!*\n\n";
                $response .= "📝 Nome: {$professional->name}\n";
                $response .= "💼 Profissão: {$professional->profession}\n";
                $response .= "⏱️ Duração: {$professional->default_duration} min\n\n";
                $response .= "🔗 *Seu link:*\n{$link}\n\n";
                $response .= "Compartilhe este link com seus clientes!\n\n";
                $response .= "✨ 14 dias grátis! Depois R$ 19,90/mês\n\n";
                $response .= "Digite *menu* para acessar seus agendamentos";
                
                $this->whatsappService->sendMessage($phone, $response);
                break;
        }
    }

    /**
     * Inicia processo de cadastro
     */
    public function startRegistration(string $phone): void
    {
        Cache::put("registration_state_{$phone}", 'awaiting_name', now()->addMinutes(10));
        
        $message = "Vamos criar sua agenda! 🚀\n\n";
        $message .= "Qual seu nome completo?";
        
        $this->whatsappService->sendMessage($phone, $message);
    }

    /**
     * Normaliza telefone
     */
    protected function normalizePhone(string $phone): string
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }
}
