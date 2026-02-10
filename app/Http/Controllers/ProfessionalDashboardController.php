<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\TimeBlock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        return view('professional.dashboard', [
            'professional' => $professional,
            'todayAppointments' => $todayAppointments,
            'upcomingAppointments' => $upcomingAppointments,
            'monthlyTotal' => $monthlyTotal,
        ]);
    }

    /**
     * Configurações do profissional
     */
    public function settings()
    {
        $professional = Auth::guard('professional')->user();
        return view('professional.settings', compact('professional'));
    }

    /**
     * Atualizar configurações
     */
    public function updateSettings(Request $request)
    {
        $professional = Auth::guard('professional')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'profession' => 'required|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'working_days' => 'required|array',
            'working_days.*' => 'integer|between:0,6',
            'working_hours_start' => 'required|date_format:H:i',
            'working_hours_end' => 'required|date_format:H:i|after:working_hours_start',
            'default_duration' => 'required|integer|min:15|max:240',
        ]);

        $professional->update([
            'name' => $validated['name'],
            'profession' => $validated['profession'],
            'whatsapp' => $validated['whatsapp'],
            'working_days' => json_encode($validated['working_days']),
            'working_hours' => json_encode([
                'start' => $validated['working_hours_start'],
                'end' => $validated['working_hours_end'],
            ]),
            'default_duration' => $validated['default_duration'],
        ]);

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

        $professional->timeBlocks()->create([
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'reason' => $validated['reason'] ?? null,
        ]);

        return back()->with('success', 'Bloqueio criado com sucesso!');
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
}
