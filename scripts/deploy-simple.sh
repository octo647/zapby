#!/bin/bash

# 🚀 Deploy Simples Final - WhatsApp Bot
# Sem complexidade, só npm install e restart

echo "════════════════════════════════════════════════════════"
echo "🚀 Deploy Simples - WhatsApp Bot"
echo "════════════════════════════════════════════════════════"

BOT_DIR="/var/www/zapby/whatsapp-bot"

# 1. Parar
echo "⏹️  Parando..."
sudo systemctl stop zapby-whatsapp-bot 2>/dev/null || true
sleep 2

# 2. Limpar tudo
echo "🧹 Limpando..."
cd "$BOT_DIR"
rm -rf node_modules package-lock.json .npm _cacache

# 3. Instalar
echo "📦 Instalando Baileys 6.4.0..."
npm install
NPM_RESULT=$?

if [ $NPM_RESULT -eq 0 ]; then
    echo "✅ Instalação sucedida"
else
    echo "⚠️  Instalação teve problemas, mas continuando..."
fi

# 4. Verificar
echo ""
echo "🔍 Verificando módulos..."
if [ -f "$BOT_DIR/node_modules/baileys/package.json" ]; then
    echo "   ✅ Baileys encontrado"
else
    echo "   ❌ Baileys NÃO encontrado"
fi

# 5. Iniciar
echo ""
echo "🚀 Iniciando serviço..."
sudo systemctl start zapby-whatsapp-bot
sleep 3

# 6. Ver status
echo ""
echo "📊 Status:"
sudo systemctl status zapby-whatsapp-bot --no-pager 2>&1 | head -10

# 7. Ver logs
echo ""
echo "📋 Logs:"
sudo journalctl -u zapby-whatsapp-bot -n 20 --no-pager
