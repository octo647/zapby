<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    protected $botService;

    public function __construct(WhatsAppBotService $botService)
    {
        $this->botService = $botService;
    }

    /**
     * Webhook para receber mensagens do WhatsApp
     */
    public function webhook(Request $request)
    {
        Log::info('WhatsApp webhook received', $request->all());

        try {
            // Formato esperado do webhook (Evolution API ou similar)
            $phone = $request->input('from') ?? $request->input('phone');
            $message = $request->input('body') ?? $request->input('message');
            $name = $request->input('pushname') ?? $request->input('name');
            
            if ($phone && $message) {
                $this->botService->processMessage($phone, $message, $name);
            }
            
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('WhatsApp webhook error', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);
            
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
