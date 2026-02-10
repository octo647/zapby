# 🚀 Guia Rápido: WhatsApp Bot Funcionando!

## ✅ O que foi feito

Replicamos a solução **comprovadamente funcionando** do Pagby para o AgendaZap!

## 📱 Como conectar (AGORA)

### 1. Abra um terminal e execute:

```bash
cd /home/helder/projetos/agendazap/whatsapp-bot
./start.sh
```

### 2. Um QR Code vai aparecer no terminal

### 3. Escaneie com seu WhatsApp:
- Abra WhatsApp no celular
- Vá em **Configurações** > **Aparelhos conectados**  
- Toque em **Conectar um aparelho**
- Escaneie o QR Code que apareceu

### 4. Pronto! ✅
Quando ver a mensagem:
```
✅ ============================================
   BOT AGENDAZAP CONECTADO AO WHATSAPP!
============================================
```

O bot está funcionando!

---

## 🤖 Como funciona

1. **Laravel cria lembretes** → arquivo JSON
2. **Bot Node.js lê JSON** a cada 30 segundos
3. **Envia mensagens** pelo WhatsApp
4. **Limpa o arquivo** JSON

### Fluxo:
```
Laravel Job → storage/app/appointment_reminders.json → Bot lê → Envia WhatsApp
```

---

## 💻 Testando

### Criar lembrete manualmente:

```bash
cat > storage/app/appointment_reminders.json << 'EOF'
[
  {
    "customer_phone": "32987007302",
    "customer_name": "Teste",
    "professional_name": "Dr. Pedro",
    "scheduled_at": "10/02/2026 14:30",
    "service_name": "Consulta"
  }
]
EOF
```

Em 30 segundos, você receberá a mensagem no WhatsApp!

---

## 🔄 Rodando em Background (Produção)

### Criar serviço systemd:

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

### Comandos úteis:

```bash
# Ver status
sudo systemctl status agendazap-whatsapp

# Ver logs em tempo real
sudo journalctl -u agendazap-whatsapp -f

# Parar serviço
sudo systemctl stop agendazap-whatsapp

# Reiniciar
sudo systemctl restart agendazap-whatsapp
```

---

## 📊 Integração Laravel

### O Job já está configurado!

```php
// app/Jobs/SendAppointmentReminder.php
SendAppointmentReminder::dispatch($appointment);
```

Quando despachar o Job, ele automaticamente:
1. Cria entrada no arquivo JSON
2. Bot processa em até 30 segundos
3. Mensagem é enviada
4. Log é registrado

### Command para testar:

```php
// routes/console.php ou criar Artisan Command
Artisan::command('whatsapp:test', function () {
    $appointment = Appointment::first();
    SendAppointmentReminder::dispatch($appointment);
    $this->info('Lembrete enviado!');
});
```

```bash
php artisan whatsapp:test
```

---

## 🔍 Troubleshooting

### Bot não conecta?
```bash
# Deletar sessão antiga
rm -rf /home/helder/projetos/agendazap/whatsapp-bot/auth_info_baileys
# Reiniciar
cd /home/helder/projetos/agendazap/whatsapp-bot && ./start.sh
```

### Mensagens não enviam?
1. Verifique se o bot está rodando: `ps aux | grep node`
2. Veja os logs do bot
3. Verifique formato do telefone (DDD + número, sem caracteres especiais)

### Bot desconecta sozinho?
- Normal após ~14 dias de inatividade
- Basta escanear QR code novamente

---

## 📈 Próximos Passos

1. ✅ **Hoje**: Bot funcionando!
2. **Amanhã**: Configurar serviço systemd
3. **Esta semana**: Testar com clientes reais
4. **Próxima semana**: Adicionar métricas e logs

---

## 🎯 Vantagens desta Solução

- ✅ **Já testada** no Pagby (funcionando há meses)
- ✅ **Zero custo** (sem APIs pagas)
- ✅ **Simples** de manter
- ✅ **Rápida** de implementar (30 minutos)
- ✅ **Confiável** (baseado em Baileys oficial)

---

## 💡 Dicas

### Formato de telefone:
- ✅ `32987007302` (DDD + número)
- ✅ `5532987007302` (com código país)
- ❌ `(32) 98700-7302` (formatado)

### Horário de envio:
- Bot verifica a cada 30 segundos
- Latência máxima: 30 segundos
- Para tempo real, diminua intervalo no `index.js`

### Segurança:
- ⚠️ **NÃO COMMITE** `auth_info_baileys/` no Git
- Adicione ao `.gitignore`
- Backups regulares da pasta de sessão

---

Feito! 🎉 Agora você tem WhatsApp funcionando igual ao Pagby!
