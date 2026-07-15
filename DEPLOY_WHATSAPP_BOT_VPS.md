# 🚀 Guia de Deploy - WhatsApp Bot no VPS

## ⚠️ Problema Encontrado

O deploy automático falhou porque:
- ❌ Usuário `www-data` não existe no VPS
- ❌ Permissões de `/var/backups` insuficientes
- ❌ Script assumia ambiente com www-data

---

## ✅ Solução: Deploy Manual Simples

### **Pré-requisitos**

- Você está logado no VPS como `helder`
- Ter permissão para executar `sudo`
- Arquivos sincronizados via `sync-to-vps.sh`

---

## 🔧 Passos para Deploy Correto

### **1️⃣ Parar o serviço atual (se existir)**

```bash
sudo systemctl stop zapby-whatsapp-bot 2>/dev/null || echo "Serviço não estava rodando"
```

---

### **2️⃣ Fazer backup das sessões**

```bash
BOT_DIR="/var/www/zapby/whatsapp-bot"
BACKUP_DIR="/tmp/zapby-bot-backup-$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"
cp -r "$BOT_DIR/auth_info_baileys" "$BACKUP_DIR/" 2>/dev/null || true
echo "✅ Backup salvo em: $BACKUP_DIR"
```

---

### **3️⃣ Instalar dependências Node.js**

```bash
cd /var/www/zapby/whatsapp-bot
sudo npm install
```

**Output esperado:**
```
added 123 packages in 45s
```

---

### **4️⃣ Configurar o systemd service**

```bash
# Detectar usuário (substitua 'helder' se for outro)
WEB_USER=helder

# Criar arquivo service
sudo bash -c "sed 's/User=www-data/User=$WEB_USER/g' \
  /var/www/zapby/whatsapp-bot/zapby-whatsapp-bot.service > \
  /etc/systemd/system/zapby-whatsapp-bot.service"

# Recarregar systemd
sudo systemctl daemon-reload
sudo systemctl enable zapby-whatsapp-bot

echo "✅ Service instalado"
```

---

### **5️⃣ Criar diretório de logs**

```bash
sudo mkdir -p /var/log/zapby-bot
sudo chmod 755 /var/log/zapby-bot
echo "✅ Diretório de logs criado"
```

---

### **6️⃣ Iniciar o serviço**

```bash
sudo systemctl start zapby-whatsapp-bot
sleep 2
sudo systemctl status zapby-whatsapp-bot
```

**Output esperado:**
```
● zapby-whatsapp-bot.service - Zapby WhatsApp Bot Service
   Loaded: loaded
   Active: active (running)
```

---

### **7️⃣ Verificar se está funcionando**

```bash
sudo journalctl -u zapby-whatsapp-bot -n 20
```

**Procurar por:**
- ✅ "Iniciando Zapby WhatsApp Bot"
- ✅ "Conectado ao WhatsApp" (após escanear QR)
- ❌ "Erro de conexão" (se houver, verificar logs completos)

---

## 📊 Verificação Completa

```bash
# Ver status
sudo systemctl status zapby-whatsapp-bot

# Ver logs em tempo real
sudo journalctl -u zapby-whatsapp-bot -f

# Ver apenas erros
sudo journalctl -u zapby-whatsapp-bot | grep -i error

# Ver QR Code (primeira execução)
sudo journalctl -u zapby-whatsapp-bot | grep -i "QR Code"
```

---

## 🔄 Se Houver Erro: QR Code Expirado

Se o erro for "QR Code expirado", remova a sessão antiga e reinicie:

```bash
sudo systemctl stop zapby-whatsapp-bot
rm -rf /var/www/zapby/whatsapp-bot/auth_info_baileys
sudo systemctl start zapby-whatsapp-bot

# Ver QR code nos logs
sudo journalctl -u zapby-whatsapp-bot -f | grep -i "QR"
```

---

## ⚡ Script Automático (Versão Simples)

Criamos uma versão simplificada do script que detecta o usuário:

```bash
sudo bash /var/www/zapby/scripts/deploy-whatsapp-bot-simple.sh
```

Este script:
- ✅ Detecta automaticamente o usuário correto
- ✅ Não requer www-data
- ✅ Funciona com qualquer usuário
- ✅ Faz backup em /tmp
- ✅ Instala service systemd
- ✅ Inicia o serviço

---

## 🆘 Troubleshooting

### **Problema: "npm: command not found"**
```bash
# Verificar Node.js
node --version
npm --version

# Se não tiver, instalar
sudo apt install nodejs npm
# ou
sudo yum install nodejs npm
```

### **Problema: "Permissão negada"**
```bash
# Verificar permissões
ls -la /var/www/zapby/whatsapp-bot/

# Corrigir (substitua 'helder' pelo usuário correto)
sudo chown -R helder:helder /var/www/zapby/whatsapp-bot
```

### **Problema: Serviço não inicia**
```bash
# Ver erro específico
sudo systemctl status zapby-whatsapp-bot -l

# Ver logs mais completos
sudo journalctl -u zapby-whatsapp-bot -n 50 -e
```

### **Problema: "port already in use"**
```bash
# Buscar qual processo está usando a porta
sudo lsof -i :3000  # ou a porta que o bot usa

# Matar o processo
sudo kill -9 <PID>
```

---

## 📝 Resumo dos Comandos Principais

```bash
# Deploy rápido (manual)
cd /var/www/zapby/whatsapp-bot
sudo npm install
sudo systemctl restart zapby-whatsapp-bot

# Deploy automático
sudo bash /var/www/zapby/scripts/deploy-whatsapp-bot-simple.sh

# Verificar status
sudo systemctl status zapby-whatsapp-bot

# Ver logs
sudo journalctl -u zapby-whatsapp-bot -f

# Reiniciar
sudo systemctl restart zapby-whatsapp-bot

# Parar
sudo systemctl stop zapby-whatsapp-bot
```

---

## ✅ Checklist Pós-Deploy

Após o deploy, verificar:

- [ ] `sudo systemctl is-active zapby-whatsapp-bot` retorna "active"
- [ ] Nenhum erro de "Connection Closed" nos logs
- [ ] Bot aceita mensagens e responde
- [ ] Nenhum erro de QR Code nos últimos 30 minutos
- [ ] Serviço não reinicia frequentemente

---

## 📞 Se Ainda Houver Erros

Coletar informações para debug:

```bash
# 1. Salvar logs
sudo journalctl -u zapby-whatsapp-bot -n 500 > /tmp/bot-logs.txt

# 2. Verificar recursos
free -h
df -h
ps aux | grep node

# 3. Verificar configuração
cat /etc/systemd/system/zapby-whatsapp-bot.service
ls -la /var/www/zapby/whatsapp-bot/

# 4. Enviar para análise
cat /tmp/bot-logs.txt
```

