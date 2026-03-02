# 🤖 Gerenciamento do Bot WhatsApp - Zapby

## ✅ Bot Configurado como Serviço Systemd

O bot do WhatsApp agora roda como um serviço do sistema, garantindo:
- ✅ Início automático quando o servidor reiniciar
- ✅ Reinício automático em caso de falha
- ✅ Logs centralizados e organizados
- ✅ Fácil gerenciamento com comandos systemd

---

## 🎮 Comandos de Gerenciamento

### Ver Status do Bot
```bash
sudo systemctl status zapby-bot
```

### Iniciar o Bot
```bash
sudo systemctl start zapby-bot
```

### Parar o Bot
```bash
sudo systemctl stop zapby-bot
```

### Reiniciar o Bot
```bash
sudo systemctl restart zapby-bot
```

### Habilitar Início Automático (já configurado)
```bash
sudo systemctl enable zapby-bot
```

### Desabilitar Início Automático
```bash
sudo systemctl disable zapby-bot
```

---

## 📊 Visualizar Logs

### Últimas 50 linhas do log
```bash
sudo journalctl -u zapby-bot -n 50
```

### Log em tempo real (acompanhar)
```bash
sudo journalctl -u zapby-bot -f
```

### Logs de hoje
```bash
sudo journalctl -u zapby-bot --since today
```

### Logs das últimas 2 horas
```bash
sudo journalctl -u zapby-bot --since "2 hours ago"
```

### Logs salvos em arquivo
```bash
tail -f /var/www/zapby/storage/logs/whatsapp-bot.log
tail -f /var/www/zapby/storage/logs/whatsapp-bot-error.log
```

---

## 🔍 Verificar se está Rodando

### Via systemctl
```bash
sudo systemctl is-active zapby-bot
```
Retorna: `active` ou `inactive`

### Via processos
```bash
ps aux | grep 'node.*index.js' | grep -v grep
```

### Status resumido
```bash
sudo systemctl status zapby-bot --no-pager
```

---

## 🔧 Resolução de Problemas

### Bot não inicia
```bash
# Ver logs de erro
sudo journalctl -u zapby-bot -n 100

# Verificar configuração do serviço
sudo systemctl cat zapby-bot

# Testar manualmente
cd /var/www/zapby/whatsapp-bot
node index.js
```

### Bot desconecta frequentemente
```bash
# Verificar conexão de rede
ping -c 5 web.whatsapp.com

# Limpar sessão e reconectar
sudo systemctl stop zapby-bot
rm -rf /var/www/zapby/whatsapp-bot/auth_info_baileys
sudo systemctl start zapby-bot
```

### Logs muito grandes
```bash
# Limpar logs do systemd
sudo journalctl --vacuum-time=7d

# Limpar logs personalizados
> /var/www/zapby/storage/logs/whatsapp-bot.log
> /var/www/zapby/storage/logs/whatsapp-bot-error.log
```

### Mensagens antigas sendo reenviadas
```bash
# Limpar fila de comandos do WhatsApp
cd /var/www/zapby
echo '[]' > storage/app/whatsapp_commands.json
sudo systemctl restart zapby-bot
```

---

## 📝 Atualizar o Bot

### 1. Parar o serviço
```bash
sudo systemctl stop zapby-bot
```

### 2. Atualizar arquivos (do local)
```bash
# No seu computador local
cd /home/helder/projetos/zapby
rsync -avz -e "ssh -p 22022" \
    --exclude='node_modules/' \
    --exclude='auth_info_baileys/' \
    whatsapp-bot/ helder@pagby.com.br:/var/www/zapby/whatsapp-bot/
```

### 3. Instalar dependências (se necessário)
```bash
ssh -p 22022 helder@pagby.com.br "cd /var/www/zapby/whatsapp-bot && npm install"
```

### 4. Reiniciar o serviço
```bash
sudo systemctl start zapby-bot
```

### 5. Verificar logs
```bash
sudo journalctl -u zapby-bot -f
```

---

## 🔄 Recarregar Configuração do Serviço

Se você modificar o arquivo `/etc/systemd/system/zapby-bot.service`:

```bash
sudo systemctl daemon-reload
sudo systemctl restart zapby-bot
```

---

## 🗑️ Desinstalar o Serviço

```bash
# Parar e desabilitar
sudo systemctl stop zapby-bot
sudo systemctl disable zapby-bot

# Remover arquivo de serviço
sudo rm /etc/systemd/system/zapby-bot.service

# Recarregar systemd
sudo systemctl daemon-reload
```

---

## 📍 Localização dos Arquivos

| Item | Caminho |
|------|---------|
| Serviço systemd | `/etc/systemd/system/zapby-bot.service` |
| Código do bot | `/var/www/zapby/whatsapp-bot/` |
| Logs principais | `/var/www/zapby/storage/logs/whatsapp-bot.log` |
| Logs de erro | `/var/www/zapby/storage/logs/whatsapp-bot-error.log` |
| Logs do systemd | `journalctl -u zapby-bot` |
| Sessão WhatsApp | `/var/www/zapby/whatsapp-bot/auth_info_baileys/` |
| **Fila de Mensagens** | `/var/www/zapby/storage/app/whatsapp_commands.json` |

> ⚠️ **Nota:** O arquivo `whatsapp_commands.json` armazena mensagens pendentes. Se houver mensagens antigas presas, limpe-o com: `echo '[]' > storage/app/whatsapp_commands.json`

---

## 📱 Status Atual

**Status:** ✅ Online e Conectado  
**PID:** 2159223  
**Uptime:** Iniciado em 26/02/2026 às 10:36:22  
**Número WhatsApp:** 553298294948  
**Início Automático:** Habilitado  

---

## 💡 Dicas

1. **Monitoramento:** Use `sudo journalctl -u zapby-bot -f` para acompanhar o bot em tempo real
2. **Backup da Sessão:** Faça backup regular da pasta `auth_info_baileys` para não precisar escanear QR Code novamente
3. **Logs Rotativos:** Configure logrotate para não deixar os logs crescerem indefinidamente
4. **Alertas:** Configure alertas do systemd para ser notificado se o bot cair

---

**Bot rodando em modo produção! 🚀**
