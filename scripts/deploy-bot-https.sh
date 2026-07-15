#!/bin/bash

# 🚀 Deploy com Configuração de Git HTTPS
# Soluciona erro de acesso SSH ao GitHub

set -e

echo "════════════════════════════════════════════════════════"
echo "🔧 Deploy com Git HTTPS - WhatsApp Bot"
echo "════════════════════════════════════════════════════════"
echo ""

BOT_DIR="/var/www/zapby/whatsapp-bot"

# 1. Configurar git para usar HTTPS
echo "🔐 Configurando npm para usar HTTPS..."
npm config set url.https://github.com/.insteadOf ssh://git@github.com/
git config --global url."https://github.com/".insteadOf ssh://git@github.com/
echo "   ✅ HTTPS configurado"
echo ""

# 2. Parar serviço
echo "⏹️  Parando serviço..."
sudo systemctl stop zapby-whatsapp-bot 2>/dev/null || true
sleep 2
echo ""

# 3. Backup
echo "💾 Fazendo backup..."
BACKUP_DIR="/tmp/zapby-bot-backup-$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"
cp -r "$BOT_DIR/auth_info_baileys" "$BACKUP_DIR/" 2>/dev/null || true
cp "$BOT_DIR/package.json" "$BACKUP_DIR/" 2>/dev/null || true
echo "   ✅ Backup em: $BACKUP_DIR"
echo ""

# 4. Limpar
echo "🧹 Limpando dependências antigas..."
cd "$BOT_DIR"
rm -rf node_modules package-lock.json .npm
echo "   ✅ Limpeza concluída"
echo ""

# 5. Instalar com --legacy-peer-deps
echo "📦 Instalando dependências..."
cd "$BOT_DIR"
npm install --legacy-peer-deps 2>&1 | tail -30
INSTALL_RESULT=$?

if [ $INSTALL_RESULT -eq 0 ]; then
    echo "   ✅ npm install bem-sucedido"
else
    echo "   ⚠️  npm install teve problemas"
fi
echo ""

# 6. Verificar instalação
echo "🔍 Verificando instalação..."
if [ -d "$BOT_DIR/node_modules/baileys" ]; then
    echo "   ✅ Baileys instalado"
else
    echo "   ⚠️  Baileys não foi instalado, tentando fallback..."
    npm install baileys@5.0.4 --no-save --force 2>&1 | tail -10
fi

if [ -d "$BOT_DIR/node_modules/node-fetch" ]; then
    echo "   ✅ node-fetch instalado"
else
    echo "   ⚠️  node-fetch não foi instalado"
fi
echo ""

# 7. Configurar service
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
echo "📊 Status:"
echo "════════════════════════════════════════════════════════"
sudo systemctl status zapby-whatsapp-bot --no-pager 2>&1 | head -15
echo ""

# 10. Ver logs
echo "📋 Últimas linhas do log:"
echo "════════════════════════════════════════════════════════"
sudo journalctl -u zapby-whatsapp-bot -n 20 --no-pager 2>/dev/null | tail -20 || echo "(aguardando logs...)"
echo ""

echo "════════════════════════════════════════════════════════"
echo "✅ Deploy concluído!"
echo "════════════════════════════════════════════════════════"
