<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->middleware('auth');
        $this->whatsappService = $whatsappService;
    }

    /**
     * Confirma agendamento
     */
    public function confirm(Appointment $appointment)
    {
        if ($appointment->professional_id !== Auth::id()) {
            abort(403);
        }

        $appointment->update(['status' => 'confirmed']);

        $this->whatsappService->confirmAppointmentToClient($appointment);

        return back()->with('success', 'Agendamento confirmado!');
    }

    /**
     * Cancela agendamento
     */
    public function cancel(Appointment $appointment)
    {
        if ($appointment->professional_id !== Auth::id()) {
            abort(403);
        }

        $appointment->update(['status' => 'cancelled']);

        $this->whatsappService->notifyCancellation($appointment);

        return back()->with('success', 'Agendamento cancelado.');
    }

    /**
     * Marca agendamento como completo
     */
    public function complete(Appointment $appointment)
    {
        if ($appointment->professional_id !== Auth::id()) {
            abort(403);
        }

        $appointment->update(['status' => 'completed']);

        return back()->with('success', 'Agendamento marcado como concluído!');
    }
}
