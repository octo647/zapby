# 🎯 AgendaZap - Sistema de Lembretes Automáticos

## ✅ Status: TOTALMENTE FUNCIONAL

**Data de Implementação:** 09/02/2026  
**Ambiente:** VPS Produção (mail.pagby.com.br)  
**WhatsApp:** +55 (32) 98700-7302

---

## 📋 Funcionalidades Implementadas

### 1. ✅ Envio Automático de Lembretes
- **Horário:** Diariamente às 09:00 (horário de Brasília)
- **Regra:** Envia lembretes para todos os agendamentos do dia seguinte
- **Status:** Apenas agendamentos com status `pending` ou `confirmed`
- **Formato:** Mensagem WhatsApp personalizada com emojis

### 2. ✅ Mensagem Formatada
```
🗓️ *Lembrete de Agendamento*

Olá *[Nome do Cliente]*!

Você tem um agendamento marcado:

👤 Profissional: *[Nome do Profissional]*
📅 Data: *10/02/2026*
⏰ Horário: *14:00*
⏱️ Duração: *60 minutos*

📝 Observações: [Se houver]

Por favor, confirme sua presença ou avise caso precise remarcar.

Até breve! 😊
```

### 3. ✅ Bot WhatsApp (Baileys)
- **Status:** Rodando como serviço systemd
- **Verificação:** A cada 30 segundos
- **Arquivo:** `/var/www/agendazap/storage/app/appointment_reminders.json`
- **Ação:** Lê, envia e limpa automaticamente

---

## 🔧 Comandos Disponíveis

### Testar Lembrete Manualmente
```bash
# No VPS
ssh -p 22022 helder@69.6.222.77
cd /var/www/agendazap

# Listar agendamentos
php artisan appointments:test-reminder

# Enviar lembrete específico
php artisan appointments:test-reminder 1
```

### Enviar Todos os Lembretes do Dia Seguinte
```bash
php artisan appointments:send-reminders
```

### Gerenciar Bot WhatsApp
```bash
# Status
sudo systemctl status agendazap-whatsapp

# Reiniciar
sudo systemctl restart agendazap-whatsapp

# Logs em tempo real
sudo journalctl -u agendazap-whatsapp -f
```

---

## 📊 Estrutura do Banco de Dados

### Tabela: `appointments`
```sql
- id
- professional_id (FK para professionals)
- client_name (varchar)
- client_whatsapp (varchar) - Formatado: (32) 98700-7302 ou 32987007302
- date (date)
- time (time)
- duration (int) - Em minutos
- status (enum) - pending, confirmed, cancelled, completed
- notes (text, nullable)
- created_at
- updated_at
```

### Tabela: `professionals`
```sql
- id
- name
- slug
- whatsapp
- profession
- default_duration
- working_days (json)
- working_hours (json)
- subscription_status
- trial_ends_at
- password
- remember_token
- created_at
- updated_at
```

---

## 🚀 Como Criar Novos Agendamentos

### No Laravel (Código)
```php
use App\Models\Appointment;
use App\Models\Professional;
use Carbon\Carbon;

$professional = Professional::first();

Appointment::create([
    'professional_id' => $professional->id,
    'client_name' => 'João Silva',
    'client_whatsapp' => '(32) 98700-7302', // Aceita com ou sem formatação
    'date' => Carbon::tomorrow(),
    'time' => '14:00',
    'duration' => 60,
    'status' => 'confirmed',
    'notes' => 'Trazer exames anteriores',
]);
```

### Via Seeder
```bash
php artisan db:seed --class=AppointmentSeeder
```

---

## ⚙️ Automação Configurada

### Cron Job (VPS)
```cron
# Todos os dias às 9h
0 9 * * * cd /var/www/agendazap && /usr/bin/php artisan appointments:send-reminders >> /var/www/agendazap/storage/logs/scheduler.log 2>&1
```

### Verificar Cron
```bash
ssh -p 22022 helder@69.6.222.77 'crontab -l | grep agendazap'
```

### Logs do Scheduler
```bash
ssh -p 22022 helder@69.6.222.77 'tail -f /var/www/agendazap/storage/logs/scheduler.log'
```

---

## 🔄 Fluxo Completo

```
1. CRON (09:00) 
   ↓
2. appointments:send-reminders
   ↓
3. Busca agendamentos para amanhã (status: pending/confirmed)
   ↓
4. Para cada agendamento:
   ↓
5. SendAppointmentReminder Job
   ↓
6. Formata mensagem personalizada
   ↓
7. Formata telefone (55 + DDD + número)
   ↓
8. Salva em storage/app/appointment_reminders.json
   ↓
9. Bot WhatsApp (a cada 30s)
   ↓
10. Lê JSON, envia mensagens
    ↓
11. Limpa arquivo JSON
    ↓
12. ✅ Cliente recebe lembrete no WhatsApp
```

---

## 📱 Formatação de Telefone

O sistema aceita qualquer formato e converte automaticamente:

**Entrada aceita:**
- `(32) 98700-7302`
- `32 98700-7302`
- `32987007302`
- `5532987007302`

**Saída para WhatsApp:**
- `5532987007302` (DDI + DDD + número limpo)

---

## 🧪 Testar Sistema Completo

### 1. Criar agendamento de teste para você
```bash
ssh -p 22022 helder@69.6.222.77 'cd /var/www/agendazap && php artisan tinker'
```

```php
use App\Models\Appointment;
use Carbon\Carbon;

$professional = \App\Models\Professional::first();

Appointment::create([
    'professional_id' => $professional->id,
    'client_name' => 'Helder (Teste)',
    'client_whatsapp' => '(32) 98700-7302',
    'date' => Carbon::tomorrow(),
    'time' => '14:00',
    'duration' => 60,
    'status' => 'confirmed',
    'notes' => 'Teste do sistema de lembretes',
]);
```

### 2. Enviar lembrete imediatamente
```bash
php artisan appointments:test-reminder 1
```

### 3. Verificar se chegou no WhatsApp
Aguarde até 30 segundos e verifique seu celular!

---

## 📦 Sincronização Local → VPS

```bash
# Da sua máquina local
cd ~/projetos/agendazap

# Sincronizar código
rsync -avz --exclude 'node_modules' --exclude 'vendor' --exclude '.git' \
  app/ helder@69.6.222.77:/var/www/agendazap/app/ -e "ssh -p 22022"

rsync -avz routes/console.php \
  helder@69.6.222.77:/var/www/agendazap/routes/console.php -e "ssh -p 22022"

rsync -avz database/seeders/ \
  helder@69.6.222.77:/var/www/agendazap/database/seeders/ -e "ssh -p 22022"
```

---

## 🐛 Troubleshooting

### Lembrete não chegou?

1. **Verificar agendamento existe:**
   ```bash
   php artisan appointments:test-reminder
   ```

2. **Verificar bot está rodando:**
   ```bash
   sudo systemctl status agendazap-whatsapp
   ```

3. **Verificar arquivo JSON:**
   ```bash
   cat /var/www/agendazap/storage/app/appointment_reminders.json
   ```

4. **Verificar logs Laravel:**
   ```bash
   tail -f /var/www/agendazap/storage/logs/laravel.log
   ```

5. **Verificar logs do bot:**
   ```bash
   sudo journalctl -u agendazap-whatsapp -f
   ```

### Bot desconectou?

```bash
# Parar serviço
sudo systemctl stop agendazap-whatsapp

# Remover autenticação
rm -rf /home/helder/agendazap-whatsapp-bot/auth_info_baileys/

# Iniciar e escanear novo QR
cd /home/helder/agendazap-whatsapp-bot
node index.js
# Escanear QR Code

# Reiniciar serviço
sudo systemctl start agendazap-whatsapp
```

---

## 📊 Monitoramento

### Ver quantos lembretes foram enviados hoje
```bash
tail -100 /var/www/agendazap/storage/logs/scheduler.log | grep "Enviados"
```

### Ver agendamentos de amanhã
```bash
ssh -p 22022 helder@69.6.222.77 'cd /var/www/agendazap && php artisan tinker'
```

```php
use App\Models\Appointment;
use Carbon\Carbon;

$tomorrow = Carbon::tomorrow()->format('Y-m-d');

$appointments = Appointment::where('date', $tomorrow)
    ->whereIn('status', ['pending', 'confirmed'])
    ->with('professional')
    ->get();

foreach ($appointments as $apt) {
    echo sprintf(
        "%s - %s às %s - %s (%s)\n",
        $apt->date->format('d/m/Y'),
        $apt->time->format('H:i'),
        $apt->client_name,
        $apt->client_whatsapp,
        $apt->status
    );
}
```

---

## 🎯 Próximos Passos Sugeridos

- [ ] Interface web para gerenciar agendamentos
- [ ] Confirmação de agendamento via WhatsApp (bot interativo)
- [ ] Notificação 1h antes do agendamento
- [ ] Relatórios de lembretes enviados
- [ ] Múltiplos profissionais com horários diferentes
- [ ] Integração com calendário (Google Calendar)

---

## 🔐 Segurança

- Autenticação WhatsApp: `/home/helder/agendazap-whatsapp-bot/auth_info_baileys/`
- **NÃO compartilhar** esta pasta - contém credenciais do WhatsApp
- Bot roda com usuário `helder` (sem root)
- Logs armazenados com permissões restritas

---

## 📞 Informações do Sistema

**VPS:** mail.pagby.com.br (69.6.222.77)  
**Porta SSH:** 22022  
**Usuário:** helder  
**Laravel:** /var/www/agendazap  
**Bot:** /home/helder/agendazap-whatsapp-bot  
**Node:** v24.12.0  
**PHP:** 8.x  
**MySQL:** 8.x

---

**Última Atualização:** 09/02/2026 às 09:50  
**Status:** ✅ Sistema 100% operacional e testado em produção
