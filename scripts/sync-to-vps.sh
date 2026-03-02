#!/bin/bash

# Script para sincronizar o projeto Zapby com o VPS
# Configurações do VPS - AJUSTE CONFORME NECESSÁRIO

VPS_USER="helder"
VPS_HOST="pagby.com.br"
VPS_PORT="22022"
VPS_PATH="/var/www/zapby"
LOCAL_PATH="/home/helder/projetos/zapby"

# Cores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}=== Sincronização Zapby com VPS ===${NC}\n"

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
rsync -avz --progress --no-perms --no-owner --no-group -e "ssh -p ${VPS_PORT}" \
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

# Verificar se houve erro crítico (código diferente de 0 e 23)
RSYNC_EXIT=$?
if [ $RSYNC_EXIT -eq 0 ] || [ $RSYNC_EXIT -eq 23 ]; then
    echo -e "${GREEN}✓ Arquivos sincronizados${NC}\n"
else
    echo -e "${RED}✗ Erro na sincronização (código: $RSYNC_EXIT)${NC}"
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
remote_exec "cd ${VPS_PATH} && php artisan route:clear"
remote_exec "cd ${VPS_PATH} && php artisan config:clear"
remote_exec "cd ${VPS_PATH} && php artisan view:clear"
remote_exec "cd ${VPS_PATH} && php artisan cache:clear"
echo -e "${GREEN}✓ Cache limpo${NC}"

# Recriar cache otimizado para produção
echo -e "${YELLOW}Recriando cache otimizado...${NC}"
remote_exec "cd ${VPS_PATH} && php artisan config:cache"
remote_exec "cd ${VPS_PATH} && php artisan route:cache"
remote_exec "cd ${VPS_PATH} && php artisan view:cache"
echo -e "${GREEN}✓ Cache otimizado${NC}\n"

# Verificar rotas registradas
echo -e "${YELLOW}Verificando rotas registradas...${NC}"
ROUTES_CHECK=$(remote_exec "cd ${VPS_PATH} && php artisan route:list | grep professional.subscription | wc -l")
if [ "$ROUTES_CHECK" -gt 0 ]; then
    echo -e "${GREEN}✓ Rotas 'professional.subscription' registradas corretamente${NC}\n"
else
    echo -e "${RED}⚠ AVISO: Rotas 'professional.subscription' não encontradas!${NC}"
    echo -e "${YELLOW}Execute manualmente no VPS:${NC}"
    echo -e "  php artisan route:list | grep subscription"
    echo ""
fi

# 6. Ajustar permissões
echo -e "${YELLOW}[6/6] Ajustando permissões...${NC}"

# Detectar usuário do servidor web (apache, www-data, nginx, etc)
WEB_USER=$(remote_exec "ps aux | grep -E 'apache|httpd|nginx' | grep -v grep | head -1 | awk '{print \$1}'")
if [ -z "$WEB_USER" ]; then
    WEB_USER="apache"
    echo -e "${YELLOW}⚠ Usando usuário padrão: ${WEB_USER}${NC}"
else
    echo -e "${GREEN}✓ Usuário do servidor web detectado: ${WEB_USER}${NC}"
fi

# Ajustar proprietário e permissões
remote_exec "cd ${VPS_PATH} && sudo chown -R ${WEB_USER}:${WEB_USER} storage bootstrap/cache"
remote_exec "cd ${VPS_PATH} && sudo chmod -R 775 storage bootstrap/cache"

# Garantir que subdiretórios existam e tenham permissões corretas
remote_exec "cd ${VPS_PATH} && mkdir -p storage/framework/{sessions,views,cache}"
remote_exec "cd ${VPS_PATH} && mkdir -p storage/logs"
remote_exec "cd ${VPS_PATH} && sudo chown -R ${WEB_USER}:${WEB_USER} storage"
remote_exec "cd ${VPS_PATH} && sudo chmod -R 775 storage"

echo -e "${GREEN}✓ Permissões ajustadas${NC}\n"

echo -e "${GREEN}=== Sincronização concluída com sucesso! ===${NC}"
echo -e "\nPróximos passos:"
echo -e "1. Verificar se o .env está configurado no VPS"
echo -e "2. Testar a aplicação: http://${VPS_HOST}"
echo -e "3. Verificar logs: ${VPS_PATH}/storage/logs/laravel.log"
