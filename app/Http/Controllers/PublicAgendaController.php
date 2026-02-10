<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use App\Models\Appointment;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PublicAgendaController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Exibe a agenda pública do profissional
     */
    public function show(Professional $professional)
    {
        if (!$professional->isActive()) {
            abort(404, 'Profissional não encontrado ou inativo');
        }

        $availableSlots = $this->getAvailableSlots($professional);

        return view('public.agenda', [
            'professional' => $professional,
            'availableSlots' => $availableSlots,
        ]);
    }

    /**
     * Cria novo agendamento
     */
    public function store(Request $request, Professional $professional)
    {
        $validated = $request->validate([
            'client_name' => 'required|string|max:255',
            'client_whatsapp' => 'required|string|max:20',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
        ]);

        // Verifica se o horário está disponível
        if (!$this->isSlotAvailable($professional, $validated['date'], $validated['time'])) {
            return back()->with('error', 'Horário não disponível. Por favor, escolha outro horário.');
        }

        $appointment = Appointment::create([
            'professional_id' => $professional->id,
            'client_name' => $validated['client_name'],
            'client_whatsapp' => $validated['client_whatsapp'],
            'date' => $validated['date'],
            'time' => $validated['time'],
            'duration' => $professional->default_duration,
            'status' => 'pending',
        ]);

        // Notifica profissional
        $this->whatsappService->notifyNewAppointment($appointment);

        // Confirma ao cliente
        $this->whatsappService->confirmAppointmentToClient($appointment);

        return redirect()->route('agenda.show', $professional)
            ->with('success', 'Agendamento realizado com sucesso! Você receberá uma confirmação no WhatsApp.');
    }

    /**
     * Retorna horários disponíveis para a próxima semana
     */
    protected function getAvailableSlots(Professional $professional): array
    {
        $slots = [];
        $workingHours = $professional->working_hours;
        $workingDays = $professional->working_days;

        // Próximos 7 dias
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->addDays($i);
            $dayOfWeek = $date->dayOfWeek;

            // Verifica se o profissional trabalha neste dia
            if (!in_array($dayOfWeek, $workingDays)) {
                continue;
            }

            $slots[$date->format('Y-m-d')] = $this->getSlotsForDate($professional, $date);
        }

        return $slots;
    }

    /**
     * Retorna slots disponíveis para uma data específica
     */
    protected function getSlotsForDate(Professional $professional, Carbon $date): array
    {
        $slots = [];
        $workingHours = $professional->working_hours;
        $duration = $professional->default_duration;

        $startTime = Carbon::parse($date->format('Y-m-d') . ' ' . $workingHours['start']);
        $endTime = Carbon::parse($date->format('Y-m-d') . ' ' . $workingHours['end']);

        // Gera todos os slots possíveis
        $current = $startTime->copy();
        while ($current->lt($endTime)) {
            $slotTime = $current->format('H:i');
            
            if ($this->isSlotAvailable($professional, $date->format('Y-m-d'), $slotTime)) {
                $slots[] = [
                    'time' => $slotTime,
                    'available' => true,
                ];
            }

            $current->addMinutes($duration);
        }

        return $slots;
    }

    /**
     * Verifica se um slot está disponível
     */
    protected function isSlotAvailable(Professional $professional, string $date, string $time): bool
    {
        // Verifica se já existe agendamento neste horário
        $existingAppointment = Appointment::where('professional_id', $professional->id)
            ->where('date', $date)
            ->where('time', $time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($existingAppointment) {
            return false;
        }

        // Verifica se existe bloqueio de horário
        $timeBlocked = $professional->timeBlocks()
            ->where('date', $date)
            ->where(function($query) use ($time) {
                $query->where('start_time', '<=', $time)
                      ->where('end_time', '>', $time);
            })
            ->exists();

        return !$timeBlocked;
    }
}
