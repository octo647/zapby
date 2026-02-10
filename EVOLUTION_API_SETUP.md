# 🔧 Configuração Evolution API para AgendaPro

## O que é Evolution API?

Evolution API é uma API open-source que permite conectar o WhatsApp ao seu sistema via webhooks, sem necessidade de usar WhatsApp Business API oficial (que é paga).

## 📦 Instalação da Evolution API

### Opção 1: Docker (Recomendado)

```bash
# Criar arquivo docker-compose.yml
version: '3.3'
services:
  evolution-api:
    container_name: evolution_api
    image: atendai/evolution-api:latest
    restart: always
    ports:
      - "8080:8080"
    environment:
      - SERVER_URL=http://localhost:8080
      - AUTHENTICATION_API_KEY=suachavesecreta123
    volumes:
      - evolution_instances:/evolution/instances
      - evolution_store:/evolution/store

volumes:
  evolution_instances:
  evolution_store:
```

```bash
# Iniciar
docker-compose up -d
```

### Opção 2: NPM

```bash
# Instalar
npm install -g evolution-api

# Configurar .env
cat > .env << EOF
SERVER_URL=http://localhost:8080
AUTHENTICATION_API_KEY=suachavesecreta123
EOF

# Iniciar
evolution-api
```

## 🔗 Conectar WhatsApp

### 1. Criar Instância

```bash
curl -X POST http://localhost:8080/instance/create \
  -H "apikey: suachavesecreta123" \
  -H "Content-Type: application/json" \
  -d '{
    "instanceName": "agendapro",
    "webhook": {
      "url": "https://seu-site.com/webhook/whatsapp",
      "enabled": true
    }
  }'
```

### 2. Gerar QR Code

```bash
curl http://localhost:8080/instance/connect/agendapro \
  -H "apikey: suachavesecreta123"
```

Resposta:
```json
{
  "qrcode": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUg..."
}
```

### 3. Escanear QR Code

1. Copie o QR code
2. Abra no navegador ou salve como imagem
3. Abra WhatsApp > Aparelhos conectados > Conectar um aparelho
4. Escaneie o QR code
5. Aguarde confirmação

## ⚙️ Configurar Webhook

### No AgendaPro (.env)

```env
WHATSAPP_API_URL=http://localhost:8080
WHATSAPP_API_KEY=suachavesecreta123
WHATSAPP_INSTANCE_NAME=agendapro
```

### Rota do Webhook

Já está configurada em: `routes/web.php`
```php
Route::post('/webhook/whatsapp', [WhatsAppController::class, 'webhook']);
```

### Formato de Mensagens Recebidas

A Evolution API enviará para seu webhook:
```json
{
  "event": "messages.upsert",
  "instance": "agendapro",
  "data": {
    "key": {
      "remoteJid": "5511999999999@s.whatsapp.net",
      "fromMe": false,
      "id": "..."
    },
    "message": {
      "conversation": "Olá"
    },
    "messageTimestamp": "1707321600",
    "pushName": "João Silva"
  }
}
```

## 📤 Enviar Mensagens

### Adaptar WhatsAppService

Atualize o método `sendMessage` em `app/Services/WhatsAppService.php`:

```php
public function sendMessage(string $phoneNumber, string $message): bool
{
    try {
        $phone = $this->formatPhoneNumber($phoneNumber);
        $instanceName = config('services.whatsapp.instance_name', 'agendapro');
        
        $response = Http::withHeaders([
            'apikey' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl . '/message/sendText/' . $instanceName, [
            'number' => $phone,
            'text' => $message,
        ]);

        if ($response->successful()) {
            Log::info('WhatsApp message sent', [
                'phone' => $phoneNumber,
                'message' => $message
            ]);
            return true;
        }

        Log::error('WhatsApp message failed', [
            'phone' => $phoneNumber,
            'response' => $response->body()
        ]);
        return false;
    } catch (\Exception $e) {
        Log::error('WhatsApp exception', [
            'phone' => $phoneNumber,
            'error' => $e->getMessage()
        ]);
        return false;
    }
}
```

## 🧪 Testar Conexão

### 1. Verificar Status da Instância

```bash
curl http://localhost:8080/instance/fetchInstances \
  -H "apikey: suachavesecreta123"
```

### 2. Enviar Mensagem de Teste

```bash
curl -X POST http://localhost:8080/message/sendText/agendapro \
  -H "apikey: suachavesecreta123" \
  -H "Content-Type: application/json" \
  -d '{
    "number": "5511999999999",
    "text": "Teste de mensagem do AgendaPro!"
  }'
```

### 3. Testar Webhook

Envie uma mensagem para o WhatsApp conectado e verifique os logs:

```bash
tail -f storage/logs/laravel.log
```

## 🔒 Segurança

### 1. Validar Webhook

No `WhatsAppController`, adicione validação:

```php
public function webhook(Request $request)
{
    // Validar API key (opcional)
    if ($request->header('apikey') !== config('services.whatsapp.api_key')) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }
    
    // Seu código aqui...
}
```

### 2. HTTPS em Produção

Use HTTPS para o webhook:
```
https://seu-site.com/webhook/whatsapp
```

## 📋 Checklist de Configuração

- [ ] Evolution API instalada e rodando
- [ ] Instância criada
- [ ] QR Code escaneado
- [ ] WhatsApp conectado
- [ ] Webhook configurado
- [ ] Variáveis .env configuradas
- [ ] Teste de envio funcionando
- [ ] Teste de recebimento funcionando
- [ ] Logs funcionando

## 🐛 Troubleshooting

### Problema: QR Code não aparece

**Solução:**
```bash
# Reiniciar instância
curl -X DELETE http://localhost:8080/instance/logout/agendapro \
  -H "apikey: suachavesecreta123"

# Gerar novo QR
curl http://localhost:8080/instance/connect/agendapro \
  -H "apikey: suachavesecreta123"
```

### Problema: Mensagens não chegam

**Verificar:**
1. WhatsApp está conectado?
2. URL do webhook está correta?
3. Webhook está acessível (use ngrok em desenvolvimento)?
4. Logs do Laravel mostram requisições?

```bash
# Verificar conexão
curl http://localhost:8080/instance/connectionState/agendapro \
  -H "apikey: suachavesecreta123"
```

### Problema: Não consegue enviar mensagens

**Verificar:**
1. API key está correta?
2. Nome da instância está correto?
3. Número está no formato correto? (5511999999999)

## 🌐 Desenvolvimento Local (ngrok)

Para testar webhook localmente:

```bash
# Instalar ngrok
npm install -g ngrok

# Criar túnel
ngrok http 8000

# Copiar URL gerada
https://abc123.ngrok.io
```

Atualizar webhook na Evolution API:
```bash
curl -X PUT http://localhost:8080/webhook/set/agendapro \
  -H "apikey: suachavesecreta123" \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://abc123.ngrok.io/webhook/whatsapp",
    "enabled": true
  }'
```

## 📚 Documentação Oficial

- Evolution API: https://doc.evolution-api.com/
- GitHub: https://github.com/EvolutionAPI/evolution-api

## 💡 Dicas

1. Use Docker para facilitar deploy
2. Configure logs para debug
3. Teste tudo localmente antes de colocar em produção
4. Mantenha a Evolution API atualizada
5. Faça backup das configurações

---

**Pronto para conectar!** 🚀
