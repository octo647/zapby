#!/bin/bash

# Script de sincronização via Git
# Use este script se você usa GitHub/GitLab/Bitbucket

# Cores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${YELLOW}=== Sincronização via Git ===${NC}\n"

# 1. Verificar se há mudanças
echo -e "${YELLOW}[1/5] Verificando mudanças...${NC}"
if [[ -z $(git status -s) ]]; then
    echo -e "${GREEN}✓ Nenhuma mudança para commitar${NC}\n"
else
    echo -e "${YELLOW}Mudanças detectadas:${NC}"
    git status -s
    echo ""
    
    # 2. Adicionar mudanças
    echo -e "${YELLOW}[2/5] Adicionando mudanças ao Git...${NC}"
    git add .
    echo -e "${GREEN}✓ Mudanças adicionadas${NC}\n"
    
    # 3. Commitar
    echo -e "${YELLOW}[3/5] Criando commit...${NC}"
    read -p "Mensagem do commit: " commit_message
    git commit -m "$commit_message"
    echo -e "${GREEN}✓ Commit criado${NC}\n"
fi

# 4. Push para repositório remoto
echo -e "${YELLOW}[4/5] Enviando para repositório remoto...${NC}"
git push origin master
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Push realizado com sucesso${NC}\n"
else
    echo -e "${RED}✗ Erro no push${NC}"
    echo "Verifique se o repositório remoto está configurado:"
    echo "  git remote -v"
    exit 1
fi

# 5. Instruções para o VPS
echo -e "${YELLOW}[5/5] Execute no VPS:${NC}"
echo -e "${GREEN}"
echo "ssh seu_usuario@seu_vps.com.br"
echo "cd /var/www/zapby"
echo "git pull origin master"
echo "composer install --no-dev --optimize-autoloader"
echo "php artisan migrate --force"
echo "php artisan config:cache"
echo "php artisan route:cache"
echo "php artisan view:cache"
echo "sudo systemctl restart php8.2-fpm"
echo -e "${NC}"

echo -e "${GREEN}=== Sincronização local concluída! ===${NC}"
echo -e "Agora execute os comandos acima no VPS para atualizar a aplicação."
