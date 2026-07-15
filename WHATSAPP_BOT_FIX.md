# 🔧 WhatsApp Bot - Solução para Erro de Conexão (428)

## 🔴 Problema

```
❌ Erro ao processar comandos: Error: Connection Closed (428 - Precondition Required)
```

**Causa**: A conexão com WhatsApp é perdida durante operações de sincronização de dispositivos (`getUSyncDevices`) no Baileys.

---

## ✅ Soluções (em ordem de prioridade)

### 1️⃣ **Atualizar Baileys para versão mais estável**

Versão `7.0.0-rc.9` é Release Candidate (instável). Mudar para última versão estável:

```bash
cd /var/www/zapby/whatsapp-bot
npm install baileys@latest
```

**Por quê**: RC versions têm bugs que já foram corrigidos em releases estáveis.

---

### 2️⃣ **Adicionar Retry Logic para Operações de Envio**

Envolver operações `sock.sendMessage()` com retry automático:

```javascript
// Adicionar no início do arquivo, após os imports
const MAX_RETRIES = 3;
const RETRY_DELAY = 2000; // 2 segundos

async function sendMessageWithRetry(sock, remoteJid, message, retries = 0) {
    try {
        return await sock.sendMessage(remoteJid, message);
    } catch (error) {
        if (retries < MAX_RETRIES && 
            (error.message?.includes('Connection') || 
             error.output?.statusCode === 428)) {
            console.log(`⚠️ Erro de conexão, tentando novamente (${retries + 1}/${MAX_RETRIES})...`);
            await new Promise(resolve => setTimeout(resolve, RETRY_DELAY));
            return sendMessageWithRetry(sock, remoteJid, message, retries + 1);
        }
        throw error;
    }
}
```

Depois substituir todos `await sock.sendMessage(...)` por `await sendMessageWithRetry(sock, ...)`

---

### 3️⃣ **Desabilitar Sincronização de Dispositivos Problemática**

Adicionar opções de configuração ao criar o socket:

```javascript
const sock = makeWASocket({
    version,
    auth: state,
    logger: pino({ level: 'error' }),
    printQRInTerminal: false,
    retryRequestDelayMs: 10_000,
    maxMsgsInMemory: 100,
    // 🔑 Configurações importantes:
    shouldSyncHistoryMessage: false,  // Desabilitar sync de histórico
    syncFullHistory: false,            // Não sincronizar histórico completo
    downloadHistory: false,            // Não baixar histórico
    maxConnectionAttempts: 5,
    transactionOpts: { maxRetries: 5, delayBetweenTriesMs: 100 }
})
```

---

### 4️⃣ **Melhorar Tratamento de Desconexão**

Adicionar logs mais detalhados e manusear erros 428 especificamente:

```javascript
sock.ev.on('connection.update', (update) => {
    const { connection, lastDisconnect, qr } = update
    const statusCode = lastDisconnect?.error?.output?.statusCode
    const errorMessage = lastDisconnect?.error?.output?.payload?.message
    
    console.log(`🔍 Connection Update: ${connection} | Status: ${statusCode} | Erro: ${errorMessage}`)
    
    if (connection === 'close') {
        // 428 = Precondition Required (conexão perdida durante operação)
        if (statusCode === 428 || statusCode === 440) {
            console.log('🔄 Sessão corrompida, reconectando em 15 segundos...')
            setTimeout(() => main(), 15000)
        } else if (statusCode === 401) {
            console.log('🔄 Sessão expirada, gerando novo QR em 10 segundos...')
            setTimeout(() => main(), 10000)
        } else if (statusCode === 515) {
            console.log('🔄 Erro de stream, reconectando em 30 segundos...')
            setTimeout(() => main(), 30000)
        } else {
            console.log('🔄 Reconectando em 5 segundos...')
            setTimeout(() => main(), 5000)
        }
    } else if (connection === 'open') {
        console.log('✅ Conectado ao WhatsApp!')
    }
})
```

---

### 5️⃣ **Usar SystemD Service com Restart Automático**

Garantir que o bot reinicie se cair:

```ini
[Unit]
Description=Zapby WhatsApp Bot
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/zapby/whatsapp-bot
ExecStart=/usr/bin/node index.js
Restart=always
RestartSec=10
StandardOutput=journal
StandardError=journal
SyslogIdentifier=zapby-bot

[Install]
WantedBy=multi-user.target
```

Salvar em `/etc/systemd/system/zapby-whatsapp-bot.service` e ativar:

```bash
sudo systemctl daemon-reload
sudo systemctl enable zapby-whatsapp-bot
sudo systemctl restart zapby-whatsapp-bot
```

---

## 🚀 Implementação Recomendada (Passo a Passo)

1. **Primeiro**: Atualizar Baileys
   ```bash
   cd /var/www/zapby/whatsapp-bot
   npm install baileys@latest
   ```

2. **Depois**: Aplicar configurações do socket (solução 3️⃣)

3. **Opcional**: Implementar retry logic (solução 2️⃣)

4. **Por fim**: Usar systemd service (solução 5️⃣)

---

## 📊 Monitoramento

Adicionar ao cron para verificar status:

```bash
# /etc/cron.d/zapby-bot-monitor
*/5 * * * * systemctl is-active zapby-whatsapp-bot > /dev/null || systemctl restart zapby-whatsapp-bot
```

---

## 🔍 Debug

Ver logs em tempo real:
```bash
sudo journalctl -u zapby-whatsapp-bot -f
```

Ou se rodar manual:
```bash
cd /var/www/zapby/whatsapp-bot
NODE_DEBUG=* node index.js 2>&1 | tee bot.log
```

---

## 📝 Notas Importantes

- ❌ Não use versões RC (Release Candidate) em produção
- ⚠️ O erro 428 geralmente indica sessão corrompida
- 💡 Desabilitar sync de histórico reduz uso de memória em 80%
- 🔄 Reconexão automática é crítica para estabilidade
