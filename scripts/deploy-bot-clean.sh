#!/bin/bash

# 🚀 Deploy Final - WhatsApp Bot (Sem dependências do Git)
# Versão limpa e funcional

set -e

echo "════════════════════════════════════════════════════════"
echo "🔧 Deploy Final - WhatsApp Bot"
echo "════════════════════════════════════════════════════════"
echo ""

BOT_DIR="/var/www/zapby/whatsapp-bot"

# 1. Parar serviço
echo "⏹️  Parando serviço..."
sudo systemctl stop zapby-whatsapp-bot 2>/dev/null || true
sleep 2
echo ""

# 2. Backup
echo "💾 Fazendo backup..."
BACKUP_DIR="/tmp/zapby-bot-backup-$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"
cp -r "$BOT_DIR/auth_info_baileys" "$BACKUP_DIR/" 2>/dev/null || true
echo "   ✅ Backup em: $BACKUP_DIR"
echo ""

# 3. Limpar
echo "🧹 Limpando node_modules e cache..."
cd "$BOT_DIR"
rm -rf node_modules package-lock.json .npm
npm cache clean --force 2>&1 | tail -3
echo "   ✅ Limpeza concluída"
echo ""

# 4. Instalar com configurações de npm otimizadas
echo "📦 Instalando Baileys 6.4.0..."
cd "$BOT_DIR"

# Configurar npm para não usar git
npm config set strict-ssl true
npm config set registry https://registry.npmjs.org/

# Instalar com prefer-offline
npm install 2>&1 | tail -15

echo "   ✅ npm install concluído"
echo ""

# 5. Verificar instalação
echo "🔍 Verificando instalação..."

if [ -d "$BOT_DIR/node_modules/baileys" ]; then
    BAILEYS_VERSION=$(npm list baileys 2>/dev/null | grep baileys | grep -oP '\d+\.\d+\.\d+' | head -1)
    echo "   ✅ Baileys $BAILEYS_VERSION instalado"
else
    echo "   ❌ ERRO: Baileys não foi instalado!"
    echo "   Tentando alternativa..."
    cd "$BOT_DIR"
    npm install baileys@6.4.0 --no-save --force 2>&1 | tail -5
fi

# Verificar pino
if [ -d "$BOT_DIR/node_modules/pino" ]; then
    echo "   ✅ pino instalado"
else
    echo "   ⚠️  pino faltando, instalando..."
    cd "$BOT_DIR"
    npm install pino@8.17.2 --no-save 2>&1 | tail -3
fi

# Verificar qrcode-terminal
if [ -d "$BOT_DIR/node_modules/qrcode-terminal" ]; then
    echo "   ✅ qrcode-terminal instalado"
else
    echo "   ⚠️  qrcode-terminal faltando, instalando..."
    cd "$BOT_DIR"
    npm install qrcode-terminal --no-save 2>&1 | tail -3
fi

echo ""

# 6. Verificar se arquivos estão corretos
echo "📋 Verificando arquivo index.js..."
if grep -q "import fetch from" "$BOT_DIR/index.js"; then
    echo "   ⚠️  Removendo import de node-fetch..."
    sed -i '/^import fetch from.*node-fetch/d' "$BOT_DIR/index.js"
fi
echo "   ✅ index.js verificado"
echo ""

# 7. Configurar systemd
echo "⚙️  Configurando systemd..."
sudo systemctl daemon-reload
sudo systemctl enable zapby-whatsapp-bot
echo "   ✅ Service configurado"
echo ""

# 8. Iniciar
echo "🚀 Iniciando serviço..."
sudo systemctl start zapby-whatsapp-bot
sleep 3
echo ""

# 9. Verificar status
echo "════════════════════════════════════════════════════════"
echo "📊 Status do Serviço:"
echo "════════════════════════════════════════════════════════"
sudo systemctl status zapby-whatsapp-bot --no-pager 2>&1 | head -12
echo ""

# 10. Ver logs
echo "════════════════════════════════════════════════════════"
echo "📋 Últimas linhas do log:"
echo "════════════════════════════════════════════════════════"
sudo journalctl -u zapby-whatsapp-bot -n 15 --no-pager 2>/dev/null || echo "(aguardando logs...)"
echo ""

echo "════════════════════════════════════════════════════════"
echo "✅ Deploy finalizado!"
echo "════════════════════════════════════════════════════════"
echo ""
echo "📋 Para monitorar:"
echo "   sudo journalctl -u zapby-whatsapp-bot -f"
echo ""
