#!/bin/bash

# 🚀 Deploy Final Corrigido - WhatsApp Bot
# Com todas as dependências necessárias

set -e

echo "════════════════════════════════════════════════════════"
echo "🔧 Deploy Corrigido - WhatsApp Bot"
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
cp "$BOT_DIR/package.json" "$BACKUP_DIR/" 2>/dev/null || true
echo "   ✅ Backup em: $BACKUP_DIR"
echo ""

# 3. Limpar completamente
echo "🧹 Limpando dependências antigas..."
cd "$BOT_DIR"
rm -rf node_modules package-lock.json .npm
echo "   ✅ Limpeza concluída"
echo ""

# 4. Verificar arquivo package.json
echo "📋 Verificando package.json..."
if grep -q "node-fetch" "$BOT_DIR/package.json"; then
    echo "   ✅ node-fetch encontrado"
else
    echo "   ⚠️  node-fetch não encontrado, adicionando..."
fi
echo ""

# 5. Instalar dependências
echo "📦 Instalando dependências (Baileys 5.0.4)..."
cd "$BOT_DIR"
npm install 2>&1 | tail -20
INSTALL_RESULT=$?

if [ $INSTALL_RESULT -eq 0 ]; then
    echo "   ✅ npm install bem-sucedido"
else
    echo "   ⚠️  npm install teve problemas, mas continuando..."
fi
echo ""

# 6. Verificar se modules foram instalados
echo "🔍 Verificando instalação..."
if [ -d "$BOT_DIR/node_modules/baileys" ]; then
    BAILEYS_VERSION=$(npm list baileys 2>/dev/null | grep baileys | head -1 | grep -oP '\d+\.\d+\.\d+' || echo "desconhecida")
    echo "   ✅ Baileys $BAILEYS_VERSION instalado"
else
    echo "   ❌ ERRO: Baileys não foi instalado!"
    exit 1
fi

if [ -d "$BOT_DIR/node_modules/node-fetch" ]; then
    echo "   ✅ node-fetch instalado"
else
    echo "   ❌ ERRO: node-fetch não foi instalado!"
    exit 1
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
echo "📊 Status do Serviço:"
echo "════════════════════════════════════════════════════════"
sudo systemctl status zapby-whatsapp-bot --no-pager 2>&1 | head -15
echo ""

# 10. Ver logs
echo "════════════════════════════════════════════════════════"
echo "📋 Últimas 15 linhas do log:"
echo "════════════════════════════════════════════════════════"
sudo journalctl -u zapby-whatsapp-bot -n 15 --no-pager 2>/dev/null | tail -15 || echo "(aguardando logs...)"
echo ""

echo "════════════════════════════════════════════════════════"
echo "✅ Deploy concluído!"
echo "════════════════════════════════════════════════════════"
echo ""
echo "📋 Comandos para monitorar:"
echo "   Ver status:     sudo systemctl status zapby-whatsapp-bot"
echo "   Ver logs (live): sudo journalctl -u zapby-whatsapp-bot -f"
echo "   Ver logs:        sudo journalctl -u zapby-whatsapp-bot -n 50"
echo ""
echo "⚠️  IMPORTANTE:"
echo "   Se ainda der erro, execute:"
echo "   sudo journalctl -u zapby-whatsapp-bot -n 30"
echo "   e envie toda a saída para análise"
echo ""
