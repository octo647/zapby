#!/bin/bash

# 🚀 Script de Deploy - Correção do WhatsApp Bot
# Este script aplica todas as correções necessárias para resolver o erro de Conexão (428)

set -e

echo "════════════════════════════════════════════════════════"
echo "🔧 Deploy - Correção WhatsApp Bot (Erro 428)"
echo "════════════════════════════════════════════════════════"

BOT_DIR="/var/www/zapby/whatsapp-bot"

# Detectar usuário web correto
echo "🔍 Detectando usuário web do sistema..."
WEB_USER=""
for user in www-data nginx apache _www; do
    if id "$user" &>/dev/null 2>&1; then
        WEB_USER="$user"
        echo "   ✅ Usuário detectado: $WEB_USER"
        break
    fi
done

if [ -z "$WEB_USER" ]; then
    echo "   ⚠️  Usuário web não detectado, usando usuário atual"
    WEB_USER=$(whoami)
    echo "   📝 Usuário: $WEB_USER"
fi

# 1️⃣ Parar o serviço atual
echo "⏹️  Parando o serviço atual..."
sudo systemctl stop zapby-whatsapp-bot 2>/dev/null || echo "   ℹ️  Serviço não estava rodando"

# 2️⃣ Backup das sessões
echo "💾 Fazendo backup das sessões..."
# Tentar criar em /var/backups, se falhar tenta /tmp
BACKUP_DIR=""
if sudo mkdir -p /var/backups 2>/dev/null; then
    BACKUP_DIR="/var/backups/zapby-bot-$(date +%Y%m%d_%H%M%S)"
else
    BACKUP_DIR="/tmp/zapby-bot-backup-$(date +%Y%m%d_%H%M%S)"
    echo "   ⚠️  Não pude criar /var/backups, usando /tmp"
fi

mkdir -p "$BACKUP_DIR"
cp -r "$BOT_DIR/auth_info_baileys" "$BACKUP_DIR/" 2>/dev/null || true
cp "$BOT_DIR/package.json" "$BACKUP_DIR/" 2>/dev/null || true
echo "✅ Backup criado em: $BACKUP_DIR"

# 3️⃣ Atualizar package.json
echo "📦 Atualizando dependências..."
cd "$BOT_DIR"
sudo -u $WEB_USER npm install 2>&1 || {
    echo "   ⚠️  npm install com sudo falhou, tentando sem sudo"
    npm install
}

# 4️⃣ Instalar o service systemd
echo "⚙️  Instalando service systemd..."
# Criar arquivo service com usuário correto
TEMP_SERVICE=$(mktemp)
sed "s/User=www-data/User=$WEB_USER/g" "$BOT_DIR/zapby-whatsapp-bot.service" > "$TEMP_SERVICE"
sudo cp "$TEMP_SERVICE" /etc/systemd/system/zapby-whatsapp-bot.service
rm -f "$TEMP_SERVICE"

sudo systemctl daemon-reload
sudo systemctl enable zapby-whatsapp-bot

# 5️⃣ Criar diretório de logs
echo "📁 Criando diretório de logs..."
sudo mkdir -p /var/log/zapby-bot
sudo chown $WEB_USER:$WEB_USER /var/log/zapby-bot 2>/dev/null || true

# 6️⃣ Inicial o service
echo "🚀 Iniciando o serviço..."
sudo systemctl start zapby-whatsapp-bot

# 7️⃣ Verificar status
echo ""
echo "════════════════════════════════════════════════════════"
echo "📊 Status do Serviço:"
echo "════════════════════════════════════════════════════════"
sudo systemctl status zapby-whatsapp-bot --no-pager || true

echo ""
echo "════════════════════════════════════════════════════════"
echo "✅ Deploy concluído com sucesso!"
echo "════════════════════════════════════════════════════════"
echo ""
echo "📋 Próximos passos:"
echo "1. Verificar logs: sudo journalctl -u zapby-whatsapp-bot -f"
echo "2. Se solicitar QR code, procurar nos logs por 'QR Code'"
echo "3. Monitor em tempo real: sudo tail -f /var/log/syslog | grep zapby-bot"
echo ""
echo "ℹ️  Usuário web detectado: $WEB_USER"
echo "ℹ️  Backup salvo em: $BACKUP_DIR"
echo ""
