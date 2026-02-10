#!/bin/bash

# Script para sincronizar o projeto AgendaZap com o VPS
# Configurações do VPS - AJUSTE CONFORME NECESSÁRIO

VPS_USER="helder"
VPS_HOST="pagby.com.br"
VPS_PORT="22022"
VPS_PATH="/var/www/agendazap"
LOCAL_PATH="/home/helder/projetos/agendazap"

# Cores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}=== Sincronização AgendaZap com VPS ===${NC}\n"

# Função para executar comandos remotos
remote_exec() {
    ssh -p ${VPS_PORT} ${VPS_USER}@${VPS_HOST} "$1"
}

# 1. Verificar conexão com VPS
echo -e "${YELLOW}[1/6] Verificando conexão com VPS...${NC}"
if ssh -p ${VPS_PORT} -q ${VPS_USER}@${VPS_HOST} exit; then
    echo -e "${GREEN}✓ Conexão estabelecida${NC}\n"
else
    echo -e "${RED}✗ Erro ao conectar no VPS${NC}"
    echo "Verifique as credenciais em sync-to-vps.sh"
    exit 1
fi

# 2. Sincronizar arquivos (excluindo vendor, node_modules, .env)
echo -e "${YELLOW}[2/6] Sincronizando arquivos...${NC}"
rsync -avz --progress -e "ssh -p ${VPS_PORT}" \
    --exclude='vendor/' \
    --exclude='node_modules/' \
    --exclude='.env' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='.git/' \
    --exclude='whatsapp-bot/node_modules/' \
    --exclude='whatsapp-bot/auth_info_baileys/' \
    ${LOCAL_PATH}/ ${VPS_USER}@${VPS_HOST}:${VPS_PATH}/

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Arquivos sincronizados${NC}\n"
else
    echo -e "${RED}✗ Erro na sincronização${NC}"
    exit 1
fi

# 3. Instalar dependências do Composer
echo -e "${YELLOW}[3/6] Instalando dependências do Composer...${NC}"
remote_exec "cd ${VPS_PATH} && composer install --no-dev --optimize-autoloader"
echo -e "${GREEN}✓ Dependências instaladas${NC}\n"

# 4. Executar migrations
echo -e "${YELLOW}[4/6] Executando migrations...${NC}"
remote_exec "cd ${VPS_PATH} && php artisan migrate --force"
echo -e "${GREEN}✓ Migrations executadas${NC}\n"

# 5. Limpar e otimizar cache
echo -e "${YELLOW}[5/6] Limpando e otimizando cache...${NC}"
remote_exec "cd ${VPS_PATH} && php artisan config:clear"
remote_exec "cd ${VPS_PATH} && php artisan cache:clear"
remote_exec "cd ${VPS_PATH} && php artisan route:clear"
remote_exec "cd ${VPS_PATH} && php artisan view:clear"
remote_exec "cd ${VPS_PATH} && php artisan config:cache"
remote_exec "cd ${VPS_PATH} && php artisan route:cache"
remote_exec "cd ${VPS_PATH} && php artisan view:cache"
echo -e "${GREEN}✓ Cache otimizado${NC}\n"

# 6. Ajustar permissões
echo -e "${YELLOW}[6/6] Ajustando permissões...${NC}"
remote_exec "cd ${VPS_PATH} && sudo chown -R apache:apache storage bootstrap/cache"
remote_exec "cd ${VPS_PATH} && sudo chmod -R 775 storage bootstrap/cache"
echo -e "${GREEN}✓ Permissões ajustadas${NC}\n"

echo -e "${GREEN}=== Sincronização concluída com sucesso! ===${NC}"
echo -e "\nPróximos passos:"
echo -e "1. Verificar se o .env está configurado no VPS"
echo -e "2. Testar a aplicação: http://${VPS_HOST}"
echo -e "3. Verificar logs: ${VPS_PATH}/storage/logs/laravel.log"
