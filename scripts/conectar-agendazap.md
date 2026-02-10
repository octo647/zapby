# 📱 Conectar WhatsApp - Instância agendazap

## ⚡ Comandos para executar no VPS

### 1️⃣ Descobrir a API Key

```bash
# Ver a chave de API no docker-compose
cat docker-compose.yml | grep AUTHENTICATION_API_KEY

# Ou no .env
cat .env | grep AUTHENTICATION_API_KEY
```

### 2️⃣ Gerar QR Code

```bash
# Substitua SUA_API_KEY pela chave real
curl http://localhost:8080/instance/connect/agendazap \
  -H "apikey: SUA_API_KEY"
```

**Resposta esperada:**
```json
{
  "qrcode": {
    "base64": "iVBORw0KGgoAAAANSUhEUgAA...",
    "code": "1@ABC123..."
  }
}
```

### 3️⃣ Salvar QR Code como imagem

```bash
# Definir API key
API_KEY="sua_chave_aqui"

# Gerar e salvar QR Code
curl -s http://localhost:8080/instance/connect/agendazap \
  -H "apikey: $API_KEY" | \
  jq -r '.qrcode.base64' | \
  base64 -d > /tmp/qrcode-agendazap.png

echo "✅ QR Code salvo em: /tmp/qrcode-agendazap.png"
```

### 4️⃣ Ver QR Code no terminal (opcional)

Se tiver `qrencode` instalado:
```bash
curl -s http://localhost:8080/instance/connect/agendazap \
  -H "apikey: $API_KEY" | \
  jq -r '.qrcode.code' | \
  qrencode -t ANSIUTF8
```

## 🌐 Opção via navegador

Acesse no navegador (substitua a API key):
```
http://agenda.pagby.com.br:8080/instance/connect/agendazap?apikey=SUA_API_KEY
```

## 📱 Escanear no WhatsApp

1. Abra o WhatsApp no celular
2. Vá em **Configurações** > **Aparelhos conectados**
3. Toque em **Conectar um aparelho**
4. Escaneie o QR Code gerado
5. Aguarde a confirmação

## ✅ Verificar se conectou

```bash
curl http://localhost:8080/instance/connectionState/agendazap \
  -H "apikey: $API_KEY"
```

**Resposta quando conectado:**
```json
{
  "instance": "agendazap",
  "state": "open"
}
```

## 🔧 Se o QR Code não aparecer

### Opção A: Reiniciar a instância
```bash
curl -X PUT http://localhost:8080/instance/restart/agendazap \
  -H "apikey: $API_KEY"
```

### Opção B: Ver logs
```bash
# Ver logs do Evolution API
docker logs evolution_api --tail 50 -f

# Ou do container específico
docker ps | grep evolution
docker logs <CONTAINER_ID> --tail 50
```

### Opção C: Recriar a instância
```bash
# 1. Deletar instância
curl -X DELETE http://localhost:8080/instance/delete/agendazap \
  -H "apikey: $API_KEY"

# 2. Criar nova instância
curl -X POST http://localhost:8080/instance/create \
  -H "apikey: $API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "instanceName": "agendazap",
    "qrcode": true,
    "integration": "WHATSAPP-BAILEYS"
  }'

# 3. Conectar
curl http://localhost:8080/instance/connect/agendazap \
  -H "apikey: $API_KEY"
```

## 🎯 Script automatizado

Use o script criado:
```bash
cd /home/helder/projetos/agendazap/scripts

# Editar e adicionar sua API key
nano qrcode-evolution.sh

# Executar
./qrcode-evolution.sh
```

## 💡 Dica pró

Configure um webhook para receber mensagens:
```bash
curl -X PUT http://localhost:8080/webhook/set/agendazap \
  -H "apikey: $API_KEY" \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://agenda.pagby.com.br/webhook/whatsapp",
    "enabled": true,
    "events": [
      "messages.upsert"
    ]
  }'
```

---

**Qualquer dúvida, verifique os logs sempre!** 📋
