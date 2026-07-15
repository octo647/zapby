#!/bin/bash

# 🚀 Script de Deploy Simples - WhatsApp Bot
# Versão simplificada que detecta o ambiente automaticamente

BOT_DIR="/var/www/zapby/whatsapp-bot"

echo "════════════════════════════════════════════════════════"
echo "🔧 Deploy Simples - WhatsApp Bot (Erro 428)"
echo "════════════════════════════════════════════════════════"
echo ""

# Detectar usuário web
WEB_USER=$(whoami)
echo "👤 Usuário atual: $WEB_USER"
echo ""

# 1. Parar serviço se existir
echo "⏹️  Parando serviço..."
systemctl stop zapby-whatsapp-bot 2>/dev/null || echo "   ℹ️  Serviço não estava ativo"
echo ""

# 2. Backup
echo "💾 Fazendo backup..."
BACKUP_DIR="/tmp/zapby-bot-backup-$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"
cp -r "$BOT_DIR/auth_info_baileys" "$BACKUP_DIR/" 2>/dev/null || true
cp "$BOT_DIR/package.json" "$BACKUP_DIR/" 2>/dev/null || true
echo "   ✅ Backup: $BACKUP_DIR"
echo ""

# 3. Instalar dependências
echo "📦 Instalando dependências..."
cd "$BOT_DIR"
npm install
echo "   ✅ npm install concluído"
echo ""

# 4. Copiar service
echo "⚙️  Configurando service systemd..."
# Substituir usuário no arquivo service
sed "s/User=www-data/User=$WEB_USER/g" "$BOT_DIR/zapby-whatsapp-bot.service" > /tmp/zapby-bot.service.tmp
cp /tmp/zapby-bot.service.tmp /etc/systemd/system/zapby-whatsapp-bot.service
rm -f /tmp/zapby-bot.service.tmp
systemctl daemon-reload
systemctl enable zapby-whatsapp-bot
echo "   ✅ Service instalado"
echo ""

# 5. Criar diretório de logs
echo "📁 Criando diretório de logs..."
mkdir -p /var/log/zapby-bot
chmod 755 /var/log/zapby-bot
echo "   ✅ Diretório criado"
echo ""

# 6. Iniciar serviço
echo "🚀 Iniciando serviço..."
systemctl start zapby-whatsapp-bot
sleep 2
systemctl status zapby-whatsapp-bot --no-pager || true
echo ""

echo "════════════════════════════════════════════════════════"
echo "✅ Deploy finalizado!"
echo "════════════════════════════════════════════════════════"
echo ""
echo "📋 Verificar status:"
echo "   sudo systemctl status zapby-whatsapp-bot"
echo ""
echo "📋 Ver logs em tempo real:"
echo "   sudo journalctl -u zapby-whatsapp-bot -f"
echo ""
echo "📋 Reiniciar:"
echo "   sudo systemctl restart zapby-whatsapp-bot"
echo ""
