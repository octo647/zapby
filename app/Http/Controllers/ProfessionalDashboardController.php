<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Professional;
use App\Models\TimeBlock;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\ProfessionalWorkPeriod;
use Carbon\Carbon;

class ProfessionalDashboardController extends Controller
{
    /**
     * Dashboard principal
     */
    public function dashboard()
    {
        $professional = Auth::guard('professional')->user();

        // Agendamentos de hoje
        $todayAppointments = $professional->appointments()
            ->whereDate('date', now())
            ->orderBy('time')
            ->get();

        // Próximos 7 dias
        $upcomingAppointments = $professional->appointments()
            ->whereDate('date', '>', now())
            ->whereDate('date', '<=', now()->addDays(7))
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        // Total do mês
        $monthlyTotal = $professional->appointments()
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->count();

        $todaySchedule = $this->buildTodaySchedule($professional, $todayAppointments);

        return view('professional.dashboard', [
            'professional' => $professional,
            'todayAppointments' => $todayAppointments,
            'upcomingAppointments' => $upcomingAppointments,
            'monthlyTotal' => $monthlyTotal,
            'todaySchedule' => $todaySchedule,
        ]);
    }

    /**
     * Configurações do profissional
     */
    public function settings()
    {
        $professional = Auth::guard('professional')->user();
	    $periodos = [];
        $datePeriods = [];
        foreach (
            $professional->datePeriods()
                ->where('date', '>=', now()->format('Y-m-d'))
                ->orderBy('date')
                ->orderBy('start_time')
                ->get()
            as $periodo
        ) {

            $datePeriods[$periodo->date][] = [
                'inicio' => substr($periodo->start_time, 0, 5),
                'fim' => substr($periodo->end_time, 0, 5),
            ];
        }
        
	    foreach ($professional->workPeriods as $periodo) {
	        $periodos[$periodo->weekday][] = [
		    'inicio' => substr($periodo->start_time, 0, 5),
		    'fim' => substr($periodo->end_time, 0, 5),
	        ];
	    }
	    //dd($datePeriods);
	    return view(
		    'professional.settings',
		    compact('professional', 'periodos', 'datePeriods')
	        );
    }

    /**
     * Atualizar configurações
     */
    public function updateSettings(Request $request)
    {
	  
        $professional = Auth::guard('professional')->user();

        if ($request->has('whatsapp')) {
            $whatsapp = preg_replace('/[^0-9]/', '', (string) $request->input('whatsapp'));

            $request->merge([
                'whatsapp' => $whatsapp !== '' ? $whatsapp : null,
            ]);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'profession' => 'nullable|string|max:255',
            'whatsapp' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('professionals', 'whatsapp')->ignore($professional->id),
            ],
            'working_days' => 'nullable|array',
            'working_days.*' => 'integer|between:0,6',
            'working_hours_start' => 'nullable|date_format:H:i',
            'working_hours_end' => 'nullable|date_format:H:i|after:working_hours_start',
            'services' => 'nullable|array|min:1',
            'services.*.name' => 'required|string|max:255',
            'services.*.price' => 'nullable|numeric|min:0',
	        'services.*.duration' => 'required|integer|min:15|max:480',
	        'schedule_mode' => 'nullable|in:weekly,dated',
        ], [
            'whatsapp.unique' => 'Este número de WhatsApp já está cadastrado.',
        ]);

        if (!empty($validated['whatsapp'])) {
            $whatsappVariants = [$validated['whatsapp']];

            if (str_starts_with($validated['whatsapp'], '55') && strlen($validated['whatsapp']) >= 12) {
                $whatsappVariants[] = substr($validated['whatsapp'], 2);
            } elseif (strlen($validated['whatsapp']) === 10 || strlen($validated['whatsapp']) === 11) {
                $whatsappVariants[] = '55' . $validated['whatsapp'];
            }

            $whatsappInUse = Professional::whereIn('whatsapp', array_unique($whatsappVariants))
                ->whereKeyNot($professional->id)
                ->exists();

            if ($whatsappInUse) {
                throw ValidationException::withMessages([
                    'whatsapp' => 'Este número de WhatsApp já está cadastrado.',
                ]);
            }
        }
	
	$dataToUpdate = [];
	// Atualizar schedule_mode se fornecido
	if (isset($validated['schedule_mode'])) {
    	$dataToUpdate['schedule_mode'] = $validated['schedule_mode'];
	}

        // Atualizar name se fornecido
        if (isset($validated['name'])) {
            $dataToUpdate['name'] = $validated['name'];
        }

        // Atualizar profession se fornecido
        if (isset($validated['profession'])) {
            $dataToUpdate['profession'] = $validated['profession'];
        }

        // Atualizar whatsapp se fornecido
        if (isset($validated['whatsapp'])) {
            $dataToUpdate['whatsapp'] = $validated['whatsapp'];
        }

        // Atualizar working_days se fornecido
        if (isset($validated['working_days'])) {
            // Converter explicitamente para inteiros
            $dataToUpdate['working_days'] = array_map('intval', $validated['working_days']);
        }

        // Atualizar working_hours se fornecido
        if (isset($validated['working_hours_start']) && isset($validated['working_hours_end'])) {
            $dataToUpdate['working_hours'] = [
                'start' => $validated['working_hours_start'],
                'end' => $validated['working_hours_end'],
            ];
        }

        // Atualizar services se fornecido
        if (isset($validated['services'])) {
            // Filtrar e processar serviços
            $services = array_filter($validated['services'], function($service) {
                return !empty(trim($service['name']));
            });
            
            // Reindexar array e garantir formato correto
            $services = array_values(array_map(function($service) {
                return [
                    'name' => trim($service['name']),
                    'price' => floatval($service['price']) !== '' 
                    ? floatval($service['price']) : null,
                    'duration' => intval($service['duration'])
                ];
            }, $services));
            
            $dataToUpdate['services'] = $services;
        }

        try {
	    $professional->update($dataToUpdate);
        } catch (QueryException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                throw ValidationException::withMessages([
                    'whatsapp' => 'Este número de WhatsApp já está cadastrado.',
                ]);
            }

            throw $e;
        }
	
	if (
        ($validated['schedule_mode'] ?? null) === 'dated' &&
        $request->has('datas')
    ) {

        // Apaga agenda por data antiga
        $professional->datePeriods()->delete();

	        $activeDates = $request->datas_ativas ?? array_keys($request->datas ?? []);

	        foreach ($activeDates as $date) {

            $periodos = $request->datas[$date] ?? [];

            foreach ($periodos as $periodo) {

                if (
                    !empty($periodo['inicio']) &&
                    !empty($periodo['fim'])
                ) {

                    $professional->datePeriods()->create([
                        'date' => $date,
                        'start_time' => $periodo['inicio'],
                        'end_time' => $periodo['fim'],
                    ]);
                }
            }
        }
    }
     

	if (
	($validated['schedule_mode'] ?? null) === 'weekly' &&
    $request->has('periodos')) {

	    // Remove períodos antigos
	    $professional->workPeriods()->delete();

	    foreach ($request->periodos as $weekday => $periodos) {

		foreach ($periodos as $periodo) {

		    if (
			!empty($periodo['inicio']) &&
			!empty($periodo['fim'])
		    ) {

			$professional->workPeriods()->create([
			    'weekday' => intval($weekday),
			    'start_time' => $periodo['inicio'],
			    'end_time' => $periodo['fim'],
			]);
		    }
		}
	    }
	}
	
	if (($validated['schedule_mode'] ?? null) === 'weekly') {
	
	$workingDays = array_map('intval', $request->working_days ?? []);

    $professional->workPeriods()
    ->whereNotIn('weekday', $workingDays)
    ->delete();
    }



        $professional->refresh();

        if (!$professional->onboarding_completed && ($request->has('schedule_mode') || $request->has('services'))) {
            foreach ($professional->getPendingOnboardingSteps() as $step) {
                if (($step['route'] ?? null) === 'professional.settings') {
                    return redirect(route('professional.settings') . ($step['anchor'] ?? ''))
                        ->with('success', 'Configurações atualizadas com sucesso!');
                }
            }
        }

        return back()->with('success', 'Configurações atualizadas com sucesso!');
    }

    /**
     * Atualizar senha
     */
    public function updatePassword(Request $request)
    {
        $professional = Auth::guard('professional')->user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        if (!\Hash::check($validated['current_password'], $professional->password)) {
            return back()->withErrors(['current_password' => 'Senha atual incorreta']);
        }

        $professional->update([
            'password' => \Hash::make($validated['new_password']),
        ]);

        return back()->with('success', 'Senha alterada com sucesso!');
    }

    /**
     * Lista de agendamentos
     */
    public function appointments()
    {
        $professional = Auth::guard('professional')->user();
        
        $appointments = $professional->appointments()
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->paginate(20);

        return view('professional.appointments', compact('professional', 'appointments'));
    }

    /**
     * Cancelar agendamento
     */
    public function cancelAppointment($id)
    {
        $professional = Auth::guard('professional')->user();
        
        $appointment = $professional->appointments()->findOrFail($id);
        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Agendamento cancelado com sucesso!');
    }

    /**
     * Gerenciar bloqueios de horário
     */
    public function timeBlocks()
    {
        $professional = Auth::guard('professional')->user();
        
        $timeBlocks = $professional->timeBlocks()
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('professional.time-blocks', compact('professional', 'timeBlocks'));
    }

    /**
     * Criar bloqueio de horário
     */
    public function storeTimeBlock(Request $request)
    {
        $professional = Auth::guard('professional')->user();

        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'block_type' => 'required|in:full_day,partial',
            'start_time' => 'required_if:block_type,partial|nullable|date_format:H:i',
            'end_time' => 'required_if:block_type,partial|nullable|date_format:H:i|after:start_time',
            'reason' => 'nullable|string|max:255',
        ]);

        // Se for dia inteiro, usar horários do início ao fim do dia
        if ($validated['block_type'] === 'full_day') {
            $validated['start_time'] = '00:00';
            $validated['end_time'] = '23:59';
        }

        $blockStart = Carbon::parse($validated['date'].' '.$validated['start_time']);
        $blockEnd = Carbon::parse($validated['date'].' '.$validated['end_time']);

        $hasAppointment = $professional->appointments()
            ->whereDate('date', $validated['date'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->get()
            ->contains(function (Appointment $appointment) use ($blockStart, $blockEnd) {
                $appointmentStart = Carbon::parse($appointment->date->format('Y-m-d').' '.$appointment->time);
                $appointmentEnd = $appointmentStart->copy()->addMinutes($appointment->duration);

                return $blockStart->lt($appointmentEnd) && $blockEnd->gt($appointmentStart);
            });

        if ($hasAppointment) {
            throw ValidationException::withMessages([
                'start_time' => 'Esse período já possui um agendamento.',
            ]);
        }

        $hasBlock = $professional->timeBlocks()
            ->whereDate('date', $validated['date'])
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time'])
            ->exists();

        if ($hasBlock) {
            throw ValidationException::withMessages([
                'start_time' => 'Esse período já está bloqueado.',
            ]);
        }

        $professional->timeBlocks()->create([
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'reason' => $validated['reason'] ?? null,
        ]);

        return back()->with('success', 'Bloqueio criado com sucesso!');
    }

    private function buildTodaySchedule(Professional $professional, $appointments): array
    {
        $today = now()->startOfDay();

        $periods = $professional->usesDatedSchedule()
            ? $professional->datePeriods()->whereDate('date', $today)->orderBy('start_time')->get()
            : $professional->workPeriods()->where('weekday', $today->dayOfWeek)->orderBy('start_time')->get();

        $blocks = $professional->timeBlocks()->whereDate('date', $today)->get();
        $slots = [];

        foreach ($periods as $period) {
            $current = Carbon::parse($today->format('Y-m-d').' '.$period->start_time);
            $periodEnd = Carbon::parse($today->format('Y-m-d').' '.$period->end_time);

            while ($current->lt($periodEnd)) {
                $slotEnd = $current->copy()->addMinutes(15)->min($periodEnd);
                $appointment = $appointments->first(function (Appointment $appointment) use ($current, $slotEnd) {
                    if (!in_array($appointment->status, ['pending', 'confirmed'], true)) {
                        return false;
                    }

                    $start = Carbon::parse($appointment->date->format('Y-m-d').' '.$appointment->time);
                    $end = $start->copy()->addMinutes($appointment->duration);

                    return $current->lt($end) && $slotEnd->gt($start);
                });
                $block = $blocks->first(function (TimeBlock $block) use ($today, $current, $slotEnd) {
                    $start = Carbon::parse($today->format('Y-m-d').' '.Carbon::parse($block->start_time)->format('H:i:s'));
                    $end = Carbon::parse($today->format('Y-m-d').' '.Carbon::parse($block->end_time)->format('H:i:s'));

                    return $current->lt($end) && $slotEnd->gt($start);
                });

                $status = $appointment ? 'occupied' : ($block ? 'blocked' : ($slotEnd->isPast() ? 'past' : 'free'));
                $slots[] = [
                    'start' => $current->format('H:i'),
                    'end' => $slotEnd->format('H:i'),
                    'status' => $status,
                    'label' => $appointment?->client_name ?? $block?->reason,
                ];

                $current->addMinutes(15);
            }
        }

        return $slots;
    }

    /**
     * Remover bloqueio de horário
     */
    public function destroyTimeBlock($id)
    {
        $professional = Auth::guard('professional')->user();
        
        $timeBlock = $professional->timeBlocks()->findOrFail($id);
        $timeBlock->delete();

        return back()->with('success', 'Bloqueio removido com sucesso!');
    }

    /**
     * Marcar onboarding como completo
     */
    public function completeOnboarding()
    {
        $professional = Auth::guard('professional')->user();
        $professional->completeOnboarding();

        return back()->with('success', 'Bem-vindo ao Zapby! Você está pronto para começar!');
    }
}
