<?php

namespace App\Http\Controllers;

use App\Models\Professional;

class PublicAgendaController extends Controller
{
    private const ZAPBY_WHATSAPP = '553298294948';

    /**
     * Exibe a página pública do profissional
     */
    public function show(Professional $professional)
    {
        if (!$professional->isActive()) {
            abort(404, 'Profissional não encontrado ou inativo');
        }

        $message = rawurlencode($professional->access_key);

        return view('public.agenda', [
            'professional' => $professional,
            'whatsappUrl' => "https://wa.me/" . self::ZAPBY_WHATSAPP . "?text={$message}",
        ]);
    }

    /**
     * Mantém compatibilidade com links antigos: agendamento é feito pelo WhatsApp.
     */
    public function store(Professional $professional)
    {
        return redirect()->route('agenda.show', ['professional' => $professional->slug])
            ->with('success', 'Para agendar, fale com o profissional pelo WhatsApp.');
    }

}
