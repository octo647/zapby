# 🤖 Gerenciamento do Bot WhatsApp - Pagby

## ✅ Bot Configurado como Serviço PM2

O bot do WhatsApp roda via PM2 (Process Manager), garantindo:
- ✅ Início automático quando o servidor reiniciar
- ✅ Reinício automático em caso de falha
- ✅ Logs centralizados e organizados
- ✅ Monitoramento de CPU e memória
- ✅ Gerenciamento simplificado

---

## 🎮 Comandos de Gerenciamento

### Ver Status do Bot
```bash
pm2 status Lembrete_PagBy
# ou listar todos os processos
pm2 list
```

### Ver Informações Detalhadas
```bash
pm2 info Lembrete_PagBy
```

### Iniciar o Bot
```bash
pm2 start Lembrete_PagBy
# ou se estiver parado completamente
cd /var/www/pagby/scripts/whatsapp-bot
pm2 start index.js --name Lembrete_PagBy
```

### Parar o Bot
```bash
pm2 stop Lembrete_PagBy
```

### Reiniciar o Bot
```bash
pm2 restart Lembrete_PagBy
```

### Recarregar o Bot (zero downtime)
```bash
pm2 reload Lembrete_PagBy
```

### Remover do PM2
```bash
pm2 delete Lembrete_PagBy
```

---

## 📊 Visualizar Logs

### Logs em tempo real
```bash
pm2 logs Lembrete_PagBy
```

### Últimas 50 linhas de log
```bash
pm2 logs Lembrete_PagBy --lines 50
```

### Últimas linhas sem streaming
```bash
pm2 logs Lembrete_PagBy --lines 50 --nostream
```

### Logs apenas de erro
```bash
pm2 logs Lembrete_PagBy --err
```

### Logs apenas de saída padrão
```bash
pm2 logs Lembrete_PagBy --out
```

### Ver arquivos de log diretamente
```bash
# Log de saída
tail -f /home/helder/.pm2/logs/Lembrete_PagBy-out.log

# Log de erro
tail -f /home/helder/.pm2/logs/Lembrete_PagBy-error.log
```

### Limpar logs antigos
```bash
pm2 flush Lembrete_PagBy
```

---

## 🔍 Monitoramento

### Dashboard interativo
```bash
pm2 monit
```

### Informações de processo
```bash
# Ver PID e uso de recursos
pm2 status

# Informações completas
pm2 show pagby-whatsapp-bot
```

### Ver uso de CPU e memória
```bash
pm2 list
```

---

## 🔧 Resolução de Problemas

### Bot parou inesperadamente
```bash
# Ver logs de erro recentes
pm2 logs Lembrete_PagBy --err --lines 100

# Ver informações do processo
pm2 info Lembrete_PagBy

# Ver número de restarts
pm2 list  # coluna "↺" mostra restarts

# Verificar dependências faltantes
cd /var/www/pagby/scripts/whatsapp-bot
node index.js  # Testa manualmente para ver erros

# Se houver erro de módulo faltante (ERR_MODULE_NOT_FOUND)
npm install  # Reinstala todas as dependências
npm install nome-do-modulo  # Instala módulo específico

# Reiniciar após corrigir
pm2 restart Lembrete_PagBy
```

### Bot não inicia
```bash
# Ver logs de erro
pm2 logs Lembrete_PagBy --err --lines 100

# Verificar se está registrado no PM2
pm2 list

# Se não estiver listado, iniciar manualmente
cd /var/www/pagby/scripts/whatsapp-bot
pm2 start index.js --name Lembrete_PagBy

# Salvar configuração do PM2
pm2 save

# Testar manualmente
cd /var/www/pagby/scripts/whatsapp-bot
node index.js
```

### Bot desconecta frequentemente (sessão corrompida)
```bash
# Parar o bot
pm2 stop Lembrete_PagBy

# Limpar sessão e reconectar
cd /var/www/pagby/scripts/whatsapp-bot
rm -rf auth_info_baileys/

# Reiniciar
pm2 restart Lembrete_PagBy

# Acompanhar logs para ver QR Code
pm2 logs Lembrete_PagBy --lines 50
```

### Dependências faltantes (ERR_MODULE_NOT_FOUND)
```bash
# Ver qual módulo está faltando
pm2 logs Lembrete_PagBy --err --lines 50 | grep "ERR_MODULE_NOT_FOUND"

# Reinstalar todas as dependências
cd /var/www/pagby/scripts/whatsapp-bot
npm install

# Ou instalar módulo específico
npm install node-fetch

# Reiniciar bot
pm2 restart Lembrete_PagBy

# Verificar logs
pm2 logs Lembrete_PagBy --lines 30
```

### Bot travado ou não responde
```bash
# Ver uso de recursos
pm2 list

# Forçar restart
pm2 restart Lembrete_PagBy

# Se não responder, matar processo
pm2 delete Lembrete_PagBy
cd /var/www/pagby/scripts/whatsapp-bot
pm2 start index.js --name Lembrete_PagBy
pm2 save
```

### Muitos restarts (loop infinito)
```bash
# Ver número de restarts
pm2 list  # coluna "↺"

# Verificar logs de erro
pm2 logs Lembrete_PagBy --err --lines 100

# Causas comuns:
# 1. Sessão WhatsApp corrompida → rm -rf auth_info_baileys/
# 2. Dependências faltando → npm install
# 3. Erro no código → verificar logs

# Parar temporariamente
pm2 stop Lembrete_PagBy

# Corrigir problema, depois
pm2 restart Lembrete_PagBy
```

---

## 📝 Atualizar o Bot

### 1. Parar o serviço
```bash
pm2 stop Lembrete_PagBy
```

### 2. Atualizar arquivos (do local)
```bash
# No seu computador local
cd /home/helder/projetos/pagby
rsync -avz -e "ssh -p 22022" \
    --exclude='node_modules/' \
    --exclude='auth_info_baileys/' \
    scripts/whatsapp-bot/ helder@pagby.com.br:/var/www/pagby/scripts/whatsapp-bot/
```

### 3. Instalar dependências (se necessário)
```bash
ssh -p 22022 helder@pagby.com.br "cd /var/www/pagby/scripts/whatsapp-bot && npm install"
```

### 4. Reiniciar o serviço
```bash
pm2 restart Lembrete_PagBy
```

### 5. Verificar logs
```bash
pm2 logs Lembrete_PagBy --lines 50
```

---

## 🔄 Configuração de Startup Automático

### Configurar PM2 para iniciar no boot
```bash
# Gerar script de startup (executar apenas uma vez)
pm2 startup

# Seguir instruções exibidas (copiar e executar comando)

# Salvar lista atual de processos
pm2 save
```

### Atualizar configuração salva
```bash
# Após adicionar/remover processos
pm2 save
```

### Desabilitar startup automático
```bash
pm2 unstartup
```

---

## 🗑️ Limpar e Resetar

### Limpar logs
```bash
pm2 flush Lembrete_PagBy  # Limpa logs do bot
pm2 flush  # Limpa logs de todos os processos
```

### Resetar contador de restarts
```bash
pm2 reset Lembrete_PagBy
```

### Remover processo completamente
```bash
pm2 delete Lembrete_PagBy
pm2 save  # Atualizar configuração salva
```

---

## 📍 Localização dos Arquivos

| Item | Caminho |
|------|---------|
| Código do bot | `/var/www/pagby/scripts/whatsapp-bot/` |
| Logs PM2 (saída) | `/home/helder/.pm2/logs/Lembrete_PagBy-out.log` |
| Logs PM2 (erro) | `/home/helder/.pm2/logs/Lembrete_PagBy-error.log` |
| PID do processo | `/home/helder/.pm2/pids/Lembrete_PagBy-0.pid` |
| Sessão WhatsApp | `/var/www/pagby/scripts/whatsapp-bot/auth_info_baileys/` |
| Arquivo de leads | `/var/www/pagby/scripts/whatsapp-bot/leads.json` |
| Configuração PM2 | `/home/helder/.pm2/dump.pm2` |

---

## 📱 Status Atual

**Status:** ✅ Online e Conectado  
**PID:** 912738  
**Nome:** Lembrete_PagBy  
**Uptime:** Iniciado em 22/04/2026 às 10:00:04  
**Número WhatsApp:** 553298621569  
**Gerenciador:** PM2 (ID: 0)  
**Restarts:** 0 (após rename)  

**Última Correção:** 22/04/2026 - Instalada dependência faltante (node-fetch) + Reconexão WhatsApp + Renomeado para Lembrete_PagBy  

---

## 💡 Dicas PM2

1. **Monitoramento:** Use `pm2 monit` para dashboard interativo em tempo real
2. **Backup da Sessão:** Faça backup regular da pasta `auth_info_baileys` para não precisar escanear QR Code novamente
3. **Startup Automático:** Configure `pm2 startup` para garantir que o bot inicie no boot
4. **Logs Limpos:** Execute `pm2 flush` periodicamente para evitar logs gigantes
5. **Salvar Configuração:** Sempre execute `pm2 save` após mudanças importantes

---

## 🔗 Comandos Rápidos

```bash
# Ver tudo de uma vez
pm2 list && pm2 logs Lembrete_PagBy --lines 20 --nostream

# Reiniciar e acompanhar
pm2 restart Lembrete_PagBy && pm2 logs Lembrete_PagBy

# Ver status detalhado
pm2 info Lembrete_PagBy

# Limpar e reiniciar
pm2 flush Lembrete_PagBy && pm2 restart Lembrete_PagBy
```

---

## ⚙️ Configuração Avançada (Opcional)

### Criar arquivo de configuração ecosystem
```bash
cd /var/www/pagby/scripts/whatsapp-bot
pm2 ecosystem
```

Editar `ecosystem.config.js`:
```javascript
module.exports = {
  apps: [{
    name: 'Lembrete_PagBy',
    script: './index.js',
    instances: 1,
    autorestart: true,
    watch: false,
    max_memory_restart: '500M',
    error_file: '/home/helder/.pm2/logs/Lembrete_PagBy-error.log',
    out_file: '/home/helder/.pm2/logs/Lembrete_PagBy-out.log',
    log_date_format: 'YYYY-MM-DD HH:mm:ss Z',
    env: {
      NODE_ENV: 'production'
    }
  }]
}
```

Iniciar com arquivo de config:
```bash
pm2 start ecosystem.config.js
pm2 save
```

---

**Bot rodando em modo produção! 🚀**
