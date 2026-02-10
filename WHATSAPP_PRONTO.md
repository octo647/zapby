# ✅ WhatsApp Bot AgendaZap - Sistema Completo

## 🎉 Status: FUNCIONANDO EM PRODUÇÃO

**Data de Deploy:** 09/02/2026  
**Número WhatsApp:** +55 (32) 98700-7302  
**Servidor:** VPS Pagby (69.6.222.77)

---

## 📋 Solução Implementada

Após 5+ horas testando Evolution API (falhou), WPPConnect e Venom-bot (imagens Docker inexistentes), implementamos solução robusta baseada em **Baileys 6.7.0** copiada do projeto Pagby que já estava em produção.

### Arquitetura

```
Laravel Job ━━━━━> JSON File ━━━━━> WhatsApp Bot ━━━━━> WhatsApp API
                    ↓
        /var/www/agendazap/storage/app/
          appointment_reminders.json
```

### Fluxo de Funcionamento

1. **Laravel cria lembrete** → `SendAppointmentReminder.php` escreve JSON
2. **Bot lê arquivo** → A cada 30 segundos verifica JSON
3. **Envia WhatsApp** → Usa Baileys para enviar mensagens
4. **Limpa arquivo** → Remove JSON após envio bem-sucedido

---

## 🚀 Arquivos do Sistema

### VPS (Produção)
- **Bot:** `/home/helder/agendazap-whatsapp-bot/`
- **Laravel:** `/var/www/agendazap/`
- **JSON:** `/var/www/agendazap/storage/app/appointment_reminders.json`
- **Autenticação:** `/home/helder/agendazap-whatsapp-bot/auth_info_baileys/`
- **Serviço:** `/etc/systemd/system/agendazap-whatsapp.service`

### Local (Desenvolvimento)
- **Bot:** `/home/helder/projetos/agendazap/whatsapp-bot/`

---

## 🔧 Comandos Úteis

### Gerenciar Serviço
```bash
# Status
sudo systemctl status agendazap-whatsapp

# Iniciar
sudo systemctl start agendazap-whatsapp

# Parar
sudo systemctl stop agendazap-whatsapp

# Reiniciar
sudo systemctl restart agendazap-whatsapp

# Ver logs
sudo journalctl -u agendazap-whatsapp -f
```

### Testar Manualmente
```bash
# Criar lembrete de teste
cat > /var/www/agendazap/storage/app/appointment_reminders.json << 'EOF'
[
  {
    "phone": "553287007302",
    "message": "Teste de lembrete às $(date +%H:%M)"
  }
]
EOF

# Aguardar até 30 segundos e verificar se foi limpo
cat /var/www/agendazap/storage/app/appointment_reminders.json
```

### Sincronizar Local → VPS
```bash
# Da máquina local
rsync -avz whatsapp-bot/ helder@69.6.222.77:/home/helder/agendazap-whatsapp-bot/ -e "ssh -p 22022" --exclude 'auth_info_baileys' --exclude 'node_modules'

# Reiniciar serviço
ssh -p 22022 helder@69.6.222.77 'sudo systemctl restart agendazap-whatsapp'
```

---

## 🐛 Problema Resolvido: Import do Baileys

### ❌ Código Errado (não funciona)
```javascript
import makeWASocket, { useMultiFileAuthState } from '@whiskeysockets/baileys'
```

### ✅ Código Correto (funciona)
```javascript
import baileys from '@whiskeysockets/baileys'
const { makeWASocket, useMultiFileAuthState, fetchLatestBaileysVersion } = baileys
```

**Causa:** O Baileys exporta tudo como objeto default, não como named exports.

---

## 📝 Formato do JSON

```json
[
  {
    "phone": "553287007302",
    "message": "Olá! Você tem um agendamento amanhã às 14:00."
  },
  {
    "phone": "5532912345678", 
    "message": "Lembrete: Consulta hoje às 10:00."
  }
]
```

**Observações:**
- Telefone com DDI + DDD + número (sem formatação)
- Array de objetos para envios múltiplos
- Bot limpa arquivo automaticamente após enviar

---

## 🔐 Permissões Necessárias

```bash
# Diretório storage deve ser acessível pelo bot
sudo chown -R helder:helder /var/www/agendazap/storage/app/
sudo chmod -R 775 /var/www/agendazap/storage/app/
```

**Usuário do serviço:** `helder`  
**Grupo:** `helder`

---

## 📦 Dependências

```json
{
  "dependencies": {
    "@whiskeysockets/baileys": "^6.7.0",
    "qrcode-terminal": "^0.12.0"
  },
  "type": "module"
}
```

**Node.js:** v24.12.0

---

## ✅ Validações Realizadas

- [x] Bot conecta ao WhatsApp automaticamente
- [x] Lê arquivo JSON corretamente
- [x] Envia mensagens com sucesso
- [x] Limpa arquivo após enviar
- [x] Serviço inicia automaticamente no boot
- [x] Logs funcionando no journalctl
- [x] Permissões corretas de escrita
- [x] Testes de integração completos

---

## 📊 Logs de Sucesso

```
✅ Conectado ao WhatsApp!
📤 Enviando lembrete para 553287007302
✅ Lembrete enviado para 553287007302
```

---

## 🆘 Troubleshooting

### Bot não conecta
```bash
# Verificar se serviço está rodando
sudo systemctl status agendazap-whatsapp

# Ver logs de erro
sudo journalctl -u agendazap-whatsapp -n 50
```

### Arquivo não é limpo
```bash
# Verificar permissões
ls -la /var/www/agendazap/storage/app/appointment_reminders.json

# Ajustar se necessário
sudo chown helder:helder /var/www/agendazap/storage/app/appointment_reminders.json
sudo chmod 664 /var/www/agendazap/storage/app/appointment_reminders.json
```

### Mensagem não envia
```bash
# Verificar formato do telefone (DDI + DDD + número)
# Exemplo correto: 553287007302
# Exemplo errado: (32) 98700-7302
```

---

## 🎯 Próximos Passos Sugeridos

1. Implementar retry em caso de falha de envio
2. Adicionar validação de formato de telefone
3. Criar dashboard de monitoramento
4. Implementar log estruturado com Winston
5. Adicionar health check endpoint

---

## 📞 Suporte

- **Desenvolvedor:** Helder
- **Projeto:** AgendaZap
- **Repositório:** `/home/helder/projetos/agendazap/`
- **VPS:** mail.pagby.com.br (69.6.222.77)

---

**Última Atualização:** 09/02/2026 09:20  
**Status Final:** ✅ Sistema 100% operacional em produção
