
<?php

use Illuminate\Http\Request;
use App\Models\Professional;
use App\Models\Appointment;
use Illuminate\Support\Facades\Route;
use Carbon\Carbon;

Route::get('/check-user-type', function(Request $request) {
    $phone = $request->query('phone');
    $type = 'cliente';
    
    if ($phone) {
        // Tentar buscar com o número completo
        if (Professional::where('whatsapp', $phone)->exists()) {
            $type = 'profissional';
        } 
        // Se não encontrar, tentar sem o código do país (55)
        else if (str_starts_with($phone, '55') && Professional::where('whatsapp', substr($phone, 2))->exists()) {
            $type = 'profissional';
        }
    }
    
    return response()->json(['type' => $type]);
});

Route::get('/validate-access-key', function(Request $request) {
    $accessKey = $request->query('key');
    
    if (!$accessKey) {
        return response()->json(['valid' => false, 'message' => 'Chave não fornecida']);
    }
    
    $professional = Professional::where('access_key', $accessKey)->first();
    
    if (!$professional) {
        return response()->json(['valid' => false, 'message' => 'Chave inválida']);
    }
    
    return response()->json([
        'valid' => true,
        'professional' => [
            'id' => $professional->id,
            'name' => $professional->name,
            'profession' => $professional->profession,
            'slug' => $professional->slug,
            'whatsapp' => $professional->whatsapp,
            'working_days' => $professional->working_days,
            'working_hours' => $professional->working_hours,
        ]
    ]);
});

Route::get('/check-availability', function(Request $request) {
    $professionalId = $request->query('professional_id');
    $date = $request->query('date'); // Formato: Y-m-d
    $time = $request->query('time'); // Formato: H:i
    
    if (!$professionalId || !$date || !$time) {
        return response()->json(['available' => false, 'message' => 'Parâmetros incompletos']);
    }
    
    try {
        $dateTime = Carbon::parse("$date $time");
    } catch (\Exception $e) {
        return response()->json(['available' => false, 'message' => 'Data ou hora inválida']);
    }
    
    $professional = Professional::find($professionalId);
    if (!$professional) {
        return response()->json(['available' => false, 'message' => 'Profissional não encontrado']);
    }
    
    // Verificar se já existe agendamento neste horário
    $existing = Appointment::where('professional_id', $professionalId)
        ->where('scheduled_at', $dateTime)
        ->whereIn('status', ['confirmed', 'pending'])
        ->exists();
    
    if ($existing) {
        return response()->json(['available' => false, 'message' => 'Horário já ocupado']);
    }
    
    return response()->json(['available' => true, 'message' => 'Horário disponível']);
});

Route::get('/professional-services', function(Request $request) {
    $professionalId = $request->query('professional_id');
    
    if (!$professionalId) {
        return response()->json(['success' => false, 'message' => 'ID do profissional não fornecido']);
    }
    
    $professional = Professional::find($professionalId);
    if (!$professional) {
        return response()->json(['success' => false, 'message' => 'Profissional não encontrado']);
    }
    
    // Buscar serviços do profissional ou usar padrão
    $services = $professional->services ?? [
        ['name' => 'Consulta', 'price' => 50.00, 'duration' => 30],
        ['name' => 'Atendimento', 'price' => 50.00, 'duration' => 30],
        ['name' => 'Sessão', 'price' => 50.00, 'duration' => 60]
    ];
    
    // Garantir compatibilidade com serviços antigos (strings)
    $services = array_map(function($service) {
        if (is_string($service)) {
            return ['name' => $service, 'price' => 50.00, 'duration' => 30];
        }
        return $service;
    }, $services);
    
    return response()->json(['success' => true, 'services' => $services]);
});

Route::get('/available-times', function(Request $request) {
    $professionalId = $request->query('professional_id');
    $date = $request->query('date'); // Formato: Y-m-d
    $duration = (int) $request->query('duration', 30); // Duração do serviço em minutos (padrão: 30)
    
    if (!$professionalId || !$date) {
        return response()->json(['success' => false, 'message' => 'Parâmetros incompletos']);
    }
    
    $professional = Professional::find($professionalId);
    if (!$professional) {
        return response()->json(['success' => false, 'message' => 'Profissional não encontrado']);
    }
    
   
    
    // Gerar horários dentro do horário de trabalho do profissional
    $dayOfWeek = Carbon::parse($date)->dayOfWeek;
     
    if ($professional->schedule_mode === 'dated') {

    $workPeriods = $professional->datePeriods()
        ->where('date', $date)
        ->orderBy('start_time')
        ->get();

    } else {

        $workPeriods = $professional->workPeriods()
            ->where('weekday', $dayOfWeek)
            ->orderBy('start_time')
            ->get();
    }
        
   $times = [];

foreach ($workPeriods as $period) {

    $periodStart = Carbon::parse("$date {$period->start_time}");
    $periodEnd = Carbon::parse("$date {$period->end_time}");

    $current = $periodStart->copy();

    while ($current -> lt($periodEnd)) {

        $time = $current->format('H:i');
        $timeObj = $current->copy();

        // Pular horários que já passaram (se for hoje)
        if ($timeObj->isPast()) {
            $current->addMinutes(30);
            continue;
        }

        // Verificar se há tempo suficiente até o fim do período
        $endTime = $timeObj->copy()->addMinutes($duration);

        if ($endTime->greaterThan($periodEnd)) {
            $current->addMinutes(30);
            continue;
        }
            // Verificar se o horário está disponível (considerando a duração)
            $hasConflict = false;
            
            // Buscar agendamentos no dia
            $appointments = Appointment::where('professional_id', $professionalId)
                ->where('date', $date)
                ->whereIn('status', ['confirmed', 'pending'])
                ->get();
            
            foreach ($appointments as $appointment) {
                $appointmentStart = \Carbon\Carbon::parse("$date {$appointment->time}");
                $appointmentEnd = $appointmentStart->copy()->addMinutes($appointment->duration);
                
                $newStart = $timeObj;
                $newEnd = $endTime;
                
                // Verifica se há conflito de horário
                if (($newStart >= $appointmentStart && $newStart < $appointmentEnd) ||
                    ($newEnd > $appointmentStart && $newEnd <= $appointmentEnd) ||
                    ($newStart <= $appointmentStart && $newEnd >= $appointmentEnd)) {
                    $hasConflict = true;
                    break;
                }
            }
            
            if (!$hasConflict) {
                $times[] = $time;
            }
            $current->addMinutes(30);
        }
    }
    
    return response()->json(['success' => true, 'times' => $times]);
});

Route::post('/create-appointment', function(Request $request) {
    try {
        $professionalId = $request->input('professional_id');
        $clientName = $request->input('client_name');
        $clientPhone = $request->input('client_phone');
        $phoneNumber = preg_replace('/[^0-9]/', '', (string) $request->input('phone_number', ''));
        $cleanClientPhone = preg_replace('/[^0-9]/', '', (string) $clientPhone);
        $service = $request->input('service');
        $serviceDuration = $request->input('service_duration', 30); // Duração do serviço
        $date = $request->input('date');
        $time = $request->input('time');
        
        if (!$professionalId || !$clientName || !$clientPhone || !$service || !$date || !$time) {
            return response()->json(['success' => false, 'message' => 'Dados incompletos'], 400);
        }

        if (!$phoneNumber && strlen($cleanClientPhone) >= 10 && strlen($cleanClientPhone) <= 13) {
            $phoneNumber = $cleanClientPhone;
        }
        
        $appointment = Appointment::create([
            'professional_id' => $professionalId,
            'client_name' => $clientName,
            'client_whatsapp' => $clientPhone,
            'phone_number' => $phoneNumber ?: null,
            'service' => $service,
            'date' => $date,
            'time' => $time,
            'duration' => $serviceDuration,
            'status' => 'confirmed'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Agendamento criado com sucesso',
            'appointment' => [
                'id' => $appointment->id,
                'date' => Carbon::parse($appointment->date)->format('d/m/Y'),
                'time' => $appointment->time,
                'service' => $service
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Erro ao criar agendamento: ' . $e->getMessage()], 500);
    }
});

Route::get('/client-appointments', function(Request $request) {
    $whatsapp = $request->query('whatsapp');
    
    if (!$whatsapp) {
        return response()->json(['success' => false, 'message' => 'Número do WhatsApp não fornecido'], 400);
    }
    
    try {
        $now = Carbon::now();
        $today = $now->format('Y-m-d');
        $currentTime = $now->format('H:i:s'); // Usar formato completo com segundos
        
        // Preparar variações do número (com e sem código do país)
        $cleanWhatsapp = preg_replace('/[^0-9]/', '', $whatsapp);
        $phoneVariations = array_filter(array_unique([$whatsapp, $cleanWhatsapp]));

        if (strlen($cleanWhatsapp) >= 10 && strlen($cleanWhatsapp) <= 13) {
            if (str_starts_with($cleanWhatsapp, '55')) {
                $phoneVariations[] = substr($cleanWhatsapp, 2); // Remove o 55
            } else {
                $phoneVariations[] = '55' . $cleanWhatsapp; // Adiciona o 55
            }
        }
        $phoneVariations = array_values(array_unique(array_filter($phoneVariations)));
        
        // Buscar agendamentos futuros ou de hoje
        $appointments = Appointment::where(function($query) use ($phoneVariations) {
                $query->whereIn('client_whatsapp', $phoneVariations)
                    ->orWhereIn('phone_number', $phoneVariations);
            })
            ->where(function($query) use ($today, $currentTime) {
                $query->where('date', '>', $today)
                    ->orWhere(function($q) use ($today, $currentTime) {
                        $q->where('date', '=', $today)
                          ->whereRaw('time >= ?', [$currentTime]);
                    });
            })
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('date')
            ->orderBy('time')
            ->get();
        
        if ($appointments->isEmpty()) {
            return response()->json([
                'success' => true,
                'count' => 0,
                'appointments' => []
            ]);
        }
        
        $formattedAppointments = $appointments->map(function($appointment) {
            $professional = Professional::find($appointment->professional_id);
            
            return [
                'id' => $appointment->id,
                'professional_name' => $professional ? $professional->name : 'Profissional',
                'service' => $appointment->service,
                'date' => Carbon::parse($appointment->date)->format('d/m/Y'),
                'day_of_week' => Carbon::parse($appointment->date)->locale('pt_BR')->dayName,
                'time' => substr($appointment->time, 0, 5),
                'duration' => $appointment->duration,
                'status' => $appointment->status
            ];
        });
        
        return response()->json([
            'success' => true,
            'count' => $appointments->count(),
            'appointments' => $formattedAppointments
        ]);
        
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Erro ao buscar agendamentos: ' . $e->getMessage()], 500);
    }
});

Route::get('/client-professionals', function(Request $request) {
    $whatsapp = $request->query('whatsapp');
    
    if (!$whatsapp) {
        return response()->json(['success' => false, 'message' => 'Número do WhatsApp não fornecido'], 400);
    }
    
    try {
        // Preparar variações do número (com e sem código do país)
        $cleanWhatsapp = preg_replace('/[^0-9]/', '', $whatsapp);
        $phoneVariations = array_filter(array_unique([$whatsapp, $cleanWhatsapp]));

        if (strlen($cleanWhatsapp) >= 10 && strlen($cleanWhatsapp) <= 13) {
            if (str_starts_with($cleanWhatsapp, '55')) {
                $phoneVariations[] = substr($cleanWhatsapp, 2); // Remove o 55
            } else {
                $phoneVariations[] = '55' . $cleanWhatsapp; // Adiciona o 55
            }
        }
        $phoneVariations = array_values(array_unique(array_filter($phoneVariations)));
        
        // Buscar IDs únicos de profissionais com quem o cliente já agendou
        $professionalIds = Appointment::where(function($query) use ($phoneVariations) {
                $query->whereIn('client_whatsapp', $phoneVariations)
                    ->orWhereIn('phone_number', $phoneVariations);
            })
            ->distinct()
            ->pluck('professional_id');
        
        if ($professionalIds->isEmpty()) {
            return response()->json([
                'success' => true,
                'count' => 0,
                'professionals' => []
            ]);
        }
        
        // Buscar dados dos profissionais
        $professionals = Professional::whereIn('id', $professionalIds)
            ->get()
            ->map(function($prof) {
                return [
                    'id' => $prof->id,
                    'name' => $prof->name,
                    'profession' => $prof->profession,
                    'access_key' => $prof->access_key,
		    'slug' => $prof->slug,
		    'whatsapp' => $prof->whatsapp,
                ];
            });
        
        return response()->json([
            'success' => true,
            'count' => $professionals->count(),
            'professionals' => $professionals
        ]);
        
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Erro ao buscar profissionais: ' . $e->getMessage()], 500);
    }
});
