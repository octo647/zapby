# 📱 Comandos Evolution API - AgendaPro

## 🔧 Configuração
- **URL**: http://agenda.pagby.com.br:8080
- **Instância Nome**: agendazap
- **Instância ID**: d17123ad-64f2-4135-958a-8c5640d20a00
- **API Key**: AgendaZap2026!Secure
- **Status**: close (desconectada) - EM LOOP DE RECONEXÃO
- **Número**: 32987007302
- **Problema**: Instância com dados de sessão antigos causando loop

## 🚨 SOLUÇÃO: Deletar e recriar a instância

### Passo 1: Deletar a instância atual
```bash
curl -X DELETE http://localhost:8080/instance/delete/agendazap \
  -H 'apikey: AgendaZap2026!Secure'
```

### Passo 2: Criar nova instância limpa
```bash
curl -X POST http://localhost:8080/instance/create \
  -H 'apikey: AgendaZap2026!Secure' \
  -H 'Content-Type: application/json' \
  -d '{
    "instanceName": "agendazap",
    "qrcode": true,
    "integration": "WHATSAPP-BAILEYS"
  }'
```

### Passo 3: Gerar QR Code
```bash
curl -s http://localhost:8080/instance/connect/agendazap \
  -H 'apikey: AgendaZap2026!Secure' | jq '.'
```

### Passo 4: Salvar QR Code como PNG
```bash
curl -s http://localhost:8080/instance/connect/agendazap \
  -H 'apikey: AgendaZap2026!Secure' | \
  jq -r '.qrcode.base64' | \
  base64 -d > /tmp/qrcode-agendazap.png

echo "✅ QR Code salvo: /tmp/qrcode-agendazap.png"
```

## 📋 Comando único para executar tudo:
```bash
curl -X DELETE http://localhost:8080/instance/delete/agendazap \
  -H 'apikey: AgendaZap2026!Secure' && \
sleep 2 && \
curl -X POST http://localhost:8080/instance/create \
  -H 'apikey: AgendaZap2026!Secure' \
  -H 'Content-Type: application/json' \
  -d '{"instanceName":"agendazap","qrcode":true,"integration":"WHATSAPP-BAILEYS"}' && \
sleep 3 && \
curl -s http://localhost:8080/instance/connect/agendazap \
  -H 'apikey: AgendaZap2026!Secure' | jq '.'
```

---

## 🚀 CONECTAR AGORA (após recriar a instância)

### Opção 1: Gerar QR Code e ver no terminal
```bash
curl -s http://localhost:8080/instance/connect/agendazap \
  -H 'apikey: AgendaZap2026!Secure' | jq '.'
```

### Opção 2: Salvar QR Code como PNG
```bash
curl -s http://localhost:8080/instance/connect/agendazap \
  -H 'apikey: AgendaZap2026!Secure' | \
  jq -r '.qrcode.base64' | \
  base64 -d > /tmp/qrcode-agendazap.png

echo "✅ QR Code salvo em: /tmp/qrcode-agendazap.png"

# Transferir para sua máquina local:
# scp helder@agenda.pagby.com.br:/tmp/qrcode-agendazap.png ~/Downloads/
```

### Opção 3: Ver QR Code no navegador
Acesse esta URL:
```
http://agenda.pagby.com.br:8080/instance/connect/agendazap?apikey=AgendaZap2026!Secure
```

### 📱 Como escanear
1. Abra WhatsApp no celular (32987007302)
2. Vá em **Configurações** > **Aparelhos conectados**
3. Toque em **Conectar um aparelho**
4. Escaneie o QR Code
5. Aguarde a conexão

## ⚡ Comandos Rápidos

### 1. Verificar Status da Instância
```bash
curl http://agenda.pagby.com.br:8080/instance/connectionState/agendazap \
  -H 'apikey: AgendaZap2026!Secure'
```

### 2. Desconectar WhatsApp (se estiver com problema)
```bash
curl -X DELETE http://agenda.pagby.com.br:8080/instance/logout/agendazap \
  -H 'apikey: AgendaZap2026!Secure'
```

### 3. Gerar QR Code
```bash
curl http://agenda.pagby.com.br:8080/instance/connect/agendazap \
  -H 'apikey: AgendaZap2026!Secure'
```

### 4. Reiniciar Instância
```bash
curl -X PUT http://agenda.pagby.com.br:8080/instance/restart/agendazap \
  -H 'apikey: AgendaZap2026!Secure'
```

### 5. Buscar Informações da Instância
```bash
curl http://agenda.pagby.com.br:8080/instance/fetchInstances \
  -H 'apikey: AgendaZap2026!Secure' | grep -A 20 "agendazap"
```

## 🐛 Troubleshooting no Painel

### Se o QR Code não aparece no painel:

1. **Verificar se a instância está desconectada:**
   - No painel, clique em "Disconnect" ou "Logout" primeiro
   - Aguarde alguns segundos

2. **Tentar conectar novamente:**
   - Clique no botão "Connect" ou "QR Code"
   - Aguarde carregar (pode demorar 5-10 segundos)

3. **Se continuar sem aparecer:**
   - Abra o Console do navegador (F12)
   - Vá na aba "Network" 
   - Clique em "Connect" novamente
   - Veja se há erros de API

4. **Forçar reconexão via API:**
   ```bash
   # Desconectar
   curl -X DELETE http://agenda.pagby.com.br:8080/instance/logout/agendazap \
     -H 'apikey: AgendaZap2026!Secure'
   
   # Aguardar 3 segundos
   sleep 3
   
   # Conectar e pegar QR Code
   curl http://agenda.pagby.com.br:8080/instance/connect/agendazap \
     -H 'apikey: AgendaZap2026!Secure' | jq '.qrcode.base64'
   ```

5. **Verificar logs do Evolution:**
   ```bash
   # Se estiver rodando com Docker
   docker logs evolution_api --tail 50 -f
   
   # Ou do Manager
   docker logs evolution-manager --tail 50 -f
   ```

## 🔑 Onde encontrar a API Key?

A API key geralmente está em:
1. **Arquivo `.env` da Evolution API** no VPS
2. **Configuração do Docker Compose** (variável `AUTHENTICATION_API_KEY`)
3. **Painel do Evolution Manager** > Settings > API Key

## 💡 Solução Rápida via Terminal

Execute no seu VPS:
```bash
# Instalar jq se não tiver
sudo apt install jq -y

# Criar variável com sua API key (use aspas simples para evitar erro com !)
export API_KEY='AgendaZap2026!Secure'

# Forçar nova conexão
curl -X DELETE http://localhost:8080/instance/logout/agendazap \
  -H "apikey: $API_KEY" && sleep 3 && \
curl http://localhost:8080/instance/connect/agendazap \
  -H "apikey: $API_KEY" | jq -r '.qrcode.base64' > qrcode.txt

# Ver QR Code como base64
cat qrcode.txt
```

Depois copie o conteúdo e cole no navegador como:
```
data:image/png;base64,COLE_O_BASE64_AQUI
```

## 📲 Alternativa: QR Code via navegador direto

Acesse esta URL no navegador:
```
http://agenda.pagby.com.br:8080/instance/connect/agendazap?apikey=AgendaZap2026!Secure
```
