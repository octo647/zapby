# 🚀 Solução Alternativa: WhatsApp no Laravel sem Evolution API

## 📋 Problema Identificado

O Evolution API (v2.1.1 e v2.2.3) não está gerando QR codes, mesmo após:
- ✅ Limpeza de volumes Docker
- ✅ Limpeza de banco de dados
- ✅ Múltiplas reinstalações
- ✅ Testes com diferentes versões
- ✅ Mudanças no SERVER_URL
- ✅ 20+ tentativas de conexão

**Conclusão**: Há um bug ou incompatibilidade sistêmica com o Evolution API.

---

## 🎯 Solução Recomendada: WPPConnect + Laravel

Vamos usar **WPPConnect Server** - uma alternativa mais estável ao Evolution API.

### Passo 1: Instalar WPPConnect Server no VPS

```bash
# SSH no VPS
ssh -p 22022 helder@69.6.222.77

# Criar diretório
mkdir -p ~/wppconnect-server
cd ~/wppconnect-server

# Criar docker-compose.yml
cat > docker-compose.yml << 'EOF'
version: '3.3'

services:
  wppconnect:
    image: wppconnect/wppconnect-server:latest
    container_name: wppconnect_server
    restart: always
    ports:
      - "21465:21465"
    environment:
      - BASE_URL=http://69.6.222.77:21465
      - SECRET_KEY=AgendaZap2026Secret
      - PORT=21465
      - LOG_LEVEL=info
      - MAX_LISTENERS=15
    volumes:
      - wppconnect_tokens:/usr/src/api/tokens

volumes:
  wppconnect_tokens:
EOF

# Iniciar
docker compose up -d

# Verificar logs
docker logs wppconnect_server -f
```

### Passo 2: Gerar QR Code

```bash
# Criar sessão
curl -X POST 'http://69.6.222.77:21465/api/agendazap/start-session' \
  -H 'Content-Type: application/json' \
  -H 'Authorization: Bearer AgendaZap2026Secret'

# Obter QR Code (acesse no navegador)
http://69.6.222.77:21465/api/agendazap/qrcode-session
```

### Passo 3: Integrar no Laravel

```php
// app/Services/WPPConnectService.php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WPPConnectService
{
    private string $baseUrl;
    private string $secretKey;
    private string $session;

    public function __construct()
    {
        $this->baseUrl = config('services.wppconnect.url');
        $this->secretKey = config('services.wppconnect.secret');
        $this->session = config('services.wppconnect.session', 'agendazap');
    }

    /**
     * Inicia sessão e retorna URL do QR Code
     */
    public function startSession(): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->secretKey}",
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/api/{$this->session}/start-session");

        return $response->json();
    }

    /**
     * Obtém QR Code Base64
     */
    public function getQRCode(): ?string
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->secretKey}",
        ])->get("{$this->baseUrl}/api/{$this->session}/qrcode-session");

        if ($response->successful() && $response->json('qrcode')) {
            return $response->json('qrcode');
        }

        return null;
    }

    /**
     * Verifica status da conexão
     */
    public function checkConnection(): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->secretKey}",
        ])->get("{$this->baseUrl}/api/{$this->session}/check-connection-session");

        return $response->json();
    }

    /**
     * Envia mensagem de texto
     */
    public function sendMessage(string $phone, string $message): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->secretKey}",
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/api/{$this->session}/send-message", [
            'phone' => $phone,
            'message' => $message,
        ]);

        return $response->json();
    }

    /**
     * Envia lembrete de agendamento
     */
    public function sendAppointmentReminder(
        string $phone,
        string $professionalName,
        string $dateTime
    ): bool {
        $message = "🔔 *Lembrete de Agendamento*\n\n";
        $message .= "Você tem um agendamento com *{$professionalName}*\n";
        $message .= "📅 Data/Hora: {$dateTime}\n\n";
        $message .= "Caso precise remarcar, entre em contato.";

        $result = $this->sendMessage($phone, $message);

        return isset($result['result']) && $result['result'] === 'success';
    }

    /**
     * Desconecta sessão
     */
    public function logout(): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->secretKey}",
        ])->post("{$this->baseUrl}/api/{$this->session}/logout-session");

        return $response->json();
    }
}
```

### Passo 4: Configurar no Laravel

```php
// config/services.php
return [
    // ... outras configurações ...

    'wppconnect' => [
        'url' => env('WPPCONNECT_URL', 'http://69.6.222.77:21465'),
        'secret' => env('WPPCONNECT_SECRET', 'AgendaZap2026Secret'),
        'session' => env('WPPCONNECT_SESSION', 'agendazap'),
    ],
];
```

```bash
# .env
WPPCONNECT_URL=http://69.6.222.77:21465
WPPCONNECT_SECRET=AgendaZap2026Secret
WPPCONNECT_SESSION=agendazap
```

### Passo 5: Criar Rota para Gerar QR Code

```php
// routes/web.php
use App\Services\WPPConnectService;

Route::get('/whatsapp/qrcode', function (WPPConnectService $service) {
    // Inicia sessão
    $session = $service->startSession();
    
    // Busca QR Code
    $qrcode = $service->getQRCode();
    
    if ($qrcode) {
        return view('whatsapp.qrcode', [
            'qrcode' => $qrcode,
            'session' => $session,
        ]);
    }
    
    return response()->json([
        'error' => 'QR Code não disponível. Tente novamente em alguns segundos.'
    ], 503);
})->name('whatsapp.qrcode');

Route::get('/whatsapp/status', function (WPPConnectService $service) {
    return response()->json($service->checkConnection());
})->name('whatsapp.status');
```

### Passo 6: View para QR Code

```blade
{{-- resources/views/whatsapp/qrcode.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Conectar WhatsApp - AgendaZap</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 500px;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        img {
            max-width: 300px;
            border: 5px solid #25D366;
            border-radius: 10px;
            margin: 20px 0;
        }
        .steps {
            text-align: left;
            margin-top: 30px;
            color: #555;
        }
        .steps li {
            margin: 10px 0;
        }
        .status {
            margin-top: 20px;
            padding: 15px;
            background: #f0f0f0;
            border-radius: 10px;
            color: #666;
        }
        .btn-refresh {
            margin-top: 20px;
            padding: 12px 30px;
            background: #25D366;
            color: white;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-refresh:hover {
            background: #20BA5A;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📱 Conectar WhatsApp</h1>
        <p class="subtitle">AgendaZap Pro</p>
        
        <img src="data:image/png;base64,{{ $qrcode }}" alt="QR Code WhatsApp">
        
        <div class="steps">
            <strong>Como conectar:</strong>
            <ol>
                <li>Abra o WhatsApp no seu celular</li>
                <li>Vá em <strong>Configurações</strong> > <strong>Aparelhos conectados</strong></li>
                <li>Toque em <strong>Conectar um aparelho</strong></li>
                <li>Escaneie o QR Code acima</li>
            </ol>
        </div>
        
        <div class="status" id="status">
            Aguardando conexão...
        </div>
        
        <button class="btn-refresh" onclick="location.reload()">
            Atualizar QR Code
        </button>
    </div>
    
    <script>
        // Verificar status a cada 3 segundos
        setInterval(async () => {
            try {
                const response = await fetch('/whatsapp/status');
                const data = await response.json();
                
                if (data.connected) {
                    document.getElementById('status').innerHTML = 
                        '<strong style="color: #25D366;">✅ WhatsApp Conectado!</strong>';
                    setTimeout(() => {
                        window.location.href = '/dashboard';
                    }, 2000);
                }
            } catch (error) {
                console.error('Erro ao verificar status:', error);
            }
        }, 3000);
    </script>
</body>
</html>
```

### Passo 7: Atualizar Job de Lembretes

```php
// app/Jobs/SendAppointmentReminder.php
<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\WPPConnectService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendAppointmentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private Appointment $appointment
    ) {}

    public function handle(WPPConnectService $whatsapp): void
    {
        try {
            $success = $whatsapp->sendAppointmentReminder(
                phone: $this->appointment->customer_phone,
                professionalName: $this->appointment->professional->name,
                dateTime: $this->appointment->scheduled_at->format('d/m/Y H:i')
            );

            if ($success) {
                $this->appointment->update([
                    'reminder_sent' => true,
                    'reminder_sent_at' => now(),
                ]);
                
                Log::info("Lembrete WhatsApp enviado para agendamento #{$this->appointment->id}");
            } else {
                Log::error("Falha ao enviar lembrete WhatsApp para agendamento #{$this->appointment->id}");
                $this->fail('Falha no envio do WhatsApp');
            }
        } catch (\Exception $e) {
            Log::error("Erro ao enviar lembrete WhatsApp: {$e->getMessage()}");
            $this->fail($e);
        }
    }
}
```

---

## 🎬 Como Começar

1. **Instale o WPPConnect no VPS** (comandos do Passo 1)
2. **Acesse no navegador**: `http://agendazap.pagby.com.br/whatsapp/qrcode`
3. **Escaneie o QR Code** com seu WhatsApp
4. **Pronto!** WhatsApp conectado e funcionando

---

## ✅ Vantagens desta Solução

- ✅ **Gera QR Code com certeza** (WPPConnect é mais maduro)
- ✅ **Integração simples** no Laravel
- ✅ **Documentação completa** e comunidade ativa
- ✅ **Sem dependências externas** quebradas
- ✅ **API REST simples** e intuitiva
- ✅ **Logs detalhados** para debugging

---

## 📚 Próximos Passos

1. Criar migrations para adicionar colunas `reminder_sent` e `reminder_sent_at` na tabela appointments
2. Configurar cron para enviar lembretes automáticos
3. Criar dashboard para monitorar conexão WhatsApp
4. Implementar confirmação de leitura de mensagens

---

## 🆘 Suporte

- WPPConnect Docs: https://wppconnect.io/
- GitHub: https://github.com/wppconnect-team/wppconnect-server
- Discord: https://discord.wppconnect.io/
