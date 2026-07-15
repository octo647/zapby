#!/bin/bash

# Script para limpeza automática de disco no VPS
# Deve ser executado via cron job regularmente

VPS_USER="helder"
VPS_HOST="pagby.com.br"
VPS_PORT="22022"
VPS_PATH="/var/www/zapby"

# Cores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}=== Limpeza de Disco - Zapby VPS ===${NC}\n"

# Conectar e limpar
ssh -p ${VPS_PORT} ${VPS_USER}@${VPS_HOST} << 'EOFREMOTE'
echo "📊 Espaço ANTES da limpeza:"
df -h / | tail -1

echo -e "\n🧹 Limpando logs do Laravel..."
sudo find /var/www/zapby/storage/logs -type f -delete 2>/dev/null
sudo find /var/www/zapby/storage/framework/cache -type f -delete 2>/dev/null
sudo find /var/www/zapby/storage/framework/sessions -type f -delete 2>/dev/null

echo "🧹 Limpando logs do sistema..."
sudo find /var/log -type f \( -name "*.log" -o -name "*.gz" -o -name "*.1" \) -size +100M -delete 2>/dev/null

echo "🧹 Sincronizando filesystem..."
sudo sync

echo -e "\n✅ Espaço DEPOIS da limpeza:"
df -h / | tail -1

echo -e "\n📈 Uso por diretório:"
sudo du -sh /var/log /var/www/zapby/storage 2>/dev/null | grep -v "^0"
EOFREMOTE

echo -e "\n${GREEN}✓ Limpeza concluída!${NC}"
