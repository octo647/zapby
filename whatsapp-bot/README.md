# 🤖 AgendaZap WhatsApp Bot

Bot de lembretes automáticos para agendamentos usando Baileys.

## 🚀 Como Usar

### 1. Instalar dependências
```bash
npm install
```

### 2. Iniciar bot
```bash
./start.sh
# ou
node index.js
```

### 3. Escanear QR Code
- Um QR Code aparecerá no terminal
- Abra WhatsApp no celular
- Vá em **Configurações** > **Aparelhos conectados**
- Escaneie o QR Code

### 4. Bot conectado!
- O bot ficará rodando em segundo plano
- Verificará lembretes a cada 30 segundos
- Enviará mensagens automaticamente

## 📋 Integração com Laravel

O bot lê lembretes do arquivo:
```
storage/app/appointment_reminders.json
```

### Formato do arquivo JSON:
```json
[
  {
    "customer_phone": "32987007302",
    "customer_name": "João Silva",
    "professional_name": "Dr. Pedro",
    "scheduled_at": "10/02/2026 14:30",
    "service_name": "Consulta"
  }
]
```

### No Laravel (Job):
```php
// app/Jobs/SendAppointmentReminder.php
$reminders = [];

foreach ($appointmentsToRemind as $appointment) {
    $reminders[] = [
        'customer_phone' => $appointment->customer_phone,
        'customer_name' => $appointment->customer_name,
        'professional_name' => $appointment->professional->name,
        'scheduled_at' => $appointment->scheduled_at->format('d/m/Y H:i'),
        'service_name' => $appointment->service_name ?? null,
    ];
}

Storage::put('appointment_reminders.json', json_encode($reminders));
```

## 🔄 Rodar como Serviço (Systemd)

```bash
sudo tee /etc/systemd/system/agendazap-whatsapp.service > /dev/null << 'EOF'
[Unit]
Description=AgendaZap WhatsApp Bot
After=network.target

[Service]
Type=simple
User=helder
WorkingDirectory=/home/helder/projetos/agendazap/whatsapp-bot
ExecStart=/usr/bin/node index.js
Restart=on-failure
RestartSec=10

[Install]
WantedBy=multi-user.target
EOF

sudo systemctl daemon-reload
sudo systemctl enable agendazap-whatsapp
sudo systemctl start agendazap-whatsapp
```

### Ver logs:
```bash
sudo journalctl -u agendazap-whatsapp -f
```

### Parar serviço:
```bash
sudo systemctl stop agendazap-whatsapp
```

## 🐛 Troubleshooting

### QR Code não aparece
- Verifique se tem sessão antiga: `rm -rf auth_info_baileys`
- Reinicie o bot

### Bot desconecta
- Normal após 2 semanas de inatividade
- Basta escanear QR Code novamente

### Mensagens não enviam
- Verifique formato do telefone (deve ter DDD)
- Verifique se o arquivo JSON está correto
- Veja logs para erros específicos

## 📱 Telefones Suportados

- ✅ Formato: `32987007302` (DDD + número)
- ✅ Com código país: `5532987007302`  
- ❌ Formato errado: `(32) 98700-7302`

O bot automaticamente adiciona `55` se necessário.

## ⚙️ Configurações

- **Intervalo de verificação**: 30 segundos (ajuste em `index.js`)
- **Arquivo de lembretes**: `storage/app/appointment_reminders.json`
- **Sessão WhatsApp**: `auth_info_baileys/` (não commitar!)

## 🔒 Segurança

⚠️ **NÃO COMMITE** a pasta `auth_info_baileys/` no Git!
Ela contém as credenciais do WhatsApp.

## ⚠️ Problema Comum: Import do Baileys

### ❌ ERRADO (não funciona):
```javascript
import makeWASocket, { useMultiFileAuthState } from '@whiskeysockets/baileys'
// TypeError: makeWASocket is not a function
```

### ✅ CORRETO (funciona):
```javascript
import baileys from '@whiskeysockets/baileys'
const { makeWASocket, useMultiFileAuthState, fetchLatestBaileysVersion } = baileys
```

**Motivo:** O Baileys exporta todas as funções como um objeto default, não como named exports individuais.
