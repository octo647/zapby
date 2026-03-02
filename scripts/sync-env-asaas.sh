#!/bin/bash

# Script para sincronizar apenas as variáveis do Asaas no .env do VPS
# Configurações do VPS

VPS_USER="helder"
VPS_HOST="pagby.com.br"
VPS_PORT="22022"
VPS_PATH="/var/www/zapby"
LOCAL_ENV_PATH="/home/helder/projetos/zapby/.env"

# Cores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}=== Sincronização de Variáveis Asaas ===${NC}\n"

# Verificar se .env local existe
if [ ! -f "$LOCAL_ENV_PATH" ]; then
    echo -e "${RED}✗ Arquivo .env não encontrado em $LOCAL_ENV_PATH${NC}"
    exit 1
fi

# Extrair variáveis do Asaas do .env local
echo -e "${YELLOW}Extraindo variáveis do Asaas do .env local...${NC}"
ASAAS_API_URL=$(grep "^ASAAS_API_URL=" "$LOCAL_ENV_PATH" | cut -d '=' -f2-)
ASAAS_API_KEY=$(grep "^ASAAS_API_KEY=" "$LOCAL_ENV_PATH" | cut -d '=' -f2-)
WEBHOOK_TOKEN=$(grep "^WEBHOOK_TOKEN=" "$LOCAL_ENV_PATH" | cut -d '=' -f2-)

if [ -z "$ASAAS_API_URL" ] || [ -z "$ASAAS_API_KEY" ]; then
    echo -e "${RED}✗ Variáveis do Asaas não encontradas no .env local${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Variáveis extraídas:${NC}"
echo "  ASAAS_API_URL=${ASAAS_API_URL}"
echo "  ASAAS_API_KEY=***${ASAAS_API_KEY: -10}"
echo "  WEBHOOK_TOKEN=${WEBHOOK_TOKEN:+***${WEBHOOK_TOKEN: -10}}"
echo ""

# Verificar conexão com VPS
echo -e "${YELLOW}Verificando conexão com VPS...${NC}"
if ssh -p ${VPS_PORT} -q ${VPS_USER}@${VPS_HOST} exit; then
    echo -e "${GREEN}✓ Conexão estabelecida${NC}\n"
else
    echo -e "${RED}✗ Erro ao conectar no VPS${NC}"
    exit 1
fi

# Atualizar variáveis no VPS
echo -e "${YELLOW}Atualizando variáveis no VPS...${NC}"

# Criar comandos sed para atualizar cada variável
ssh -p ${VPS_PORT} ${VPS_USER}@${VPS_HOST} << EOF
cd ${VPS_PATH}

# Backup do .env atual
cp .env .env.backup.\$(date +%Y%m%d_%H%M%S)

# Atualizar ASAAS_API_URL
if grep -q "^ASAAS_API_URL=" .env; then
    sed -i 's|^ASAAS_API_URL=.*|ASAAS_API_URL=${ASAAS_API_URL}|' .env
else
    echo "ASAAS_API_URL=${ASAAS_API_URL}" >> .env
fi

# Atualizar ASAAS_API_KEY
if grep -q "^ASAAS_API_KEY=" .env; then
    sed -i 's|^ASAAS_API_KEY=.*|ASAAS_API_KEY=${ASAAS_API_KEY}|' .env
else
    echo "ASAAS_API_KEY=${ASAAS_API_KEY}" >> .env
fi

# Atualizar WEBHOOK_TOKEN (se existir)
if [ ! -z "${WEBHOOK_TOKEN}" ]; then
    if grep -q "^WEBHOOK_TOKEN=" .env; then
        sed -i 's|^WEBHOOK_TOKEN=.*|WEBHOOK_TOKEN=${WEBHOOK_TOKEN}|' .env
    else
        echo "WEBHOOK_TOKEN=${WEBHOOK_TOKEN}" >> .env
    fi
fi

echo "Variáveis atualizadas no .env"
EOF

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Variáveis atualizadas no VPS${NC}\n"
else
    echo -e "${RED}✗ Erro ao atualizar variáveis${NC}"
    exit 1
fi

# Limpar cache no VPS
echo -e "${YELLOW}Limpando cache no VPS...${NC}"
ssh -p ${VPS_PORT} ${VPS_USER}@${VPS_HOST} "cd ${VPS_PATH} && php artisan config:clear && php artisan cache:clear"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Cache limpo${NC}\n"
fi

echo -e "${GREEN}=== Sincronização concluída! ===${NC}"
echo -e "\n${YELLOW}Nota:${NC} Foi criado um backup do .env anterior no VPS"
echo -e "Para verificar as variáveis no VPS, execute:"
echo -e "  ssh -p ${VPS_PORT} ${VPS_USER}@${VPS_HOST} \"cd ${VPS_PATH} && grep ASAAS .env\""
