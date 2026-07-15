#!/bin/bash

# 🚀 Script de Deploy Final - WhatsApp Bot (CORRIGIDO)
# Versão que funciona com Baileys 6.6.18

BOT_DIR="/var/www/zapby/whatsapp-bot"

echo "════════════════════════════════════════════════════════"
echo "🔧 Deploy Final - WhatsApp Bot (Baileys 6.6.18)"
echo "════════════════════════════════════════════════════════"
echo ""

# 1. Parar serviço
echo "⏹️  Parando serviço..."
sudo systemctl stop zapby-whatsapp-bot 2>/dev/null || echo "   ℹ️  Serviço não estava ativo"
echo ""

# 2. Backup
echo "💾 Fazendo backup..."
BACKUP_DIR="/tmp/zapby-bot-backup-$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"
cp -r "$BOT_DIR/auth_info_baileys" "$BACKUP_DIR/" 2>/dev/null || true
echo "   ✅ Backup: $BACKUP_DIR"
echo ""

# 3. Limpar node_modules antigos
echo "🧹 Limpando dependências antigas..."
cd "$BOT_DIR"
rm -rf node_modules package-lock.json
echo "   ✅ Limpeza concluída"
echo ""

# 4. Instalar dependências corretas
echo "📦 Instalando Baileys 6.6.18..."
npm install 2>&1 | grep -E "added|up to date|ERR"
echo "   ✅ npm install concluído"
echo ""

# 5. Verificar versão instalada
echo "🔍 Verificando versão do Baileys..."
BAILEYS_VERSION=$(npm list baileys | grep baileys | head -1 | grep -oP '\d+\.\d+\.\d+')
echo "   ℹ️  Baileys instalado: $BAILEYS_VERSION"
echo ""

# 6. Configurar service
echo "⚙️  Configurando service systemd..."
sudo systemctl daemon-reload
sudo systemctl enable zapby-whatsapp-bot
echo "   ✅ Service configurado"
echo ""

# 7. Iniciar
echo "🚀 Iniciando serviço..."
sudo systemctl start zapby-whatsapp-bot
sleep 3
echo ""

# 8. Verificar status
echo "════════════════════════════════════════════════════════"
echo "📊 Status:"
echo "════════════════════════════════════════════════════════"
sudo systemctl status zapby-whatsapp-bot --no-pager | head -10
echo ""

# 9. Ver logs
echo "📋 Últimas 10 linhas de log:"
echo "════════════════════════════════════════════════════════"
sudo journalctl -u zapby-whatsapp-bot -n 10 --no-pager 2>/dev/null || echo "   (sem logs ainda)"
echo ""

echo "════════════════════════════════════════════════════════"
echo "✅ Deploy concluído!"
echo "════════════════════════════════════════════════════════"
echo ""
echo "📋 Comandos úteis:"
echo "   Ver status: sudo systemctl status zapby-whatsapp-bot"
echo "   Ver logs:   sudo journalctl -u zapby-whatsapp-bot -f"
echo "   Reiniciar:  sudo systemctl restart zapby-whatsapp-bot"
echo "   Parar:      sudo systemctl stop zapby-whatsapp-bot"
echo ""
