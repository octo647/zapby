#!/bin/bash

# Script para limpar arquivos desnecessários do projeto Zapby
# Remove documentação antiga, logs, scripts obsoletos, etc.

if [ "$1" == "--remote" ]; then
    BASE_DIR="/var/www/zapby"
    echo "🌐 Limpando VPS: $BASE_DIR"
else
    BASE_DIR="/home/helder/projetos/zapby"
    echo "💻 Limpando LOCAL: $BASE_DIR"
fi

cd "$BASE_DIR" || exit 1

# Cores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "\n${YELLOW}=== Limpeza de Arquivos Desnecessários ===${NC}\n"

# 1. Remover documentação antiga/duplicada
echo -e "${YELLOW}[1/8] Removendo documentação antiga...${NC}"
rm -f ASSINATURA_IMPLEMENTADA.md
rm -f SISTEMA_COMPLETO.md
rm -f RESUMO_WHATSAPP.md
rm -f WHATSAPP_FUNCIONANDO.md
rm -f WHATSAPP_PRONTO.md
rm -f SOLUCAO_WHATSAPP_ALTERNATIVA.md
rm -f GUIA_WHATSAPP.md
rm -f RESOLVER_ERRO_VPS.md
rm -f PRODUTO_AUTONOMOS.md
rm -f README_AGENDAPRO.md
rm -f CONFIGURACAO_DOMINIO_ZAPBY.md
rm -f EVOLUTION_API_SETUP.md
echo -e "${GREEN}✓ Documentação antiga removida${NC}\n"

# 2. Remover scripts obsoletos
echo -e "${YELLOW}[2/8] Removendo scripts obsoletos...${NC}"
rm -f scripts/agendazap-bot.service
rm -f scripts/evolution-commands.md
rm -f scripts/migrate-to-zapby.sh
rm -f scripts/update-local-files.sh
rm -f scripts/conectar-agendazap.md
rm -f scripts/connect-whatsapp.sh
rm -f scripts/diagnostico-evolution.sh
rm -f scripts/fix-e-conectar.sh
rm -f scripts/gerar-qrcode.sh
rm -f scripts/instalar-baileys-node.sh
rm -f scripts/instalar-wppconnect.sh
rm -f scripts/qrcode-evolution.sh
rm -f scripts/recriar-instancia.sh
rm -f scripts/reset-whatsapp.sh
rm -f scripts/test-whatsapp.sh
echo -e "${GREEN}✓ Scripts obsoletos removidos${NC}\n"

# 3. Limpar logs antigos e filas de mensagens (manter estrutura)
echo -e "${YELLOW}[3/8] Limpando logs e filas de mensagens...${NC}"
if [ -f storage/logs/laravel.log ]; then
    > storage/logs/laravel.log
fi
if [ -f storage/logs/whatsapp-bot.log ]; then
    > storage/logs/whatsapp-bot.log
fi
if [ -f storage/logs/whatsapp-bot-error.log ]; then
    > storage/logs/whatsapp-bot-error.log
fi
rm -f whatsapp-bot/nohup.out
# Limpar fila de comandos do WhatsApp
if [ -f storage/app/whatsapp_commands.json ]; then
    echo '[]' > storage/app/whatsapp_commands.json
fi
echo -e "${GREEN}✓ Logs e filas limpos${NC}\n"

# 4. Remover views compiladas antigas
echo -e "${YELLOW}[4/8] Removendo views compiladas...${NC}"
rm -f storage/framework/views/*.php
echo -e "${GREEN}✓ Views compiladas removidas${NC}\n"

# 5. Remover arquivos HTML públicos duplicados/antigos
echo -e "${YELLOW}[5/8] Removendo arquivos públicos duplicados...${NC}"
rm -f public/whatsapp-qrcode-novo.html
echo -e "${GREEN}✓ Arquivos públicos duplicados removidos${NC}\n"

# 6. Remover arquivos temporários e cache
echo -e "${YELLOW}[6/8] Removendo arquivos temporários...${NC}"
find storage/framework/cache/data -type f -name "*.php" -delete 2>/dev/null
find storage/framework/sessions -type f -delete 2>/dev/null
echo -e "${GREEN}✓ Arquivos temporários removidos${NC}\n"

# 7. Remover arquivos duplicados da raiz (se existirem)
echo -e "${YELLOW}[7/8] Verificando arquivos duplicados na raiz...${NC}"
rm -f ProfessionalAuthController.php
rm -rf Http/
rm -rf Services/
rm -f professional-login.blade.php
rm -f professional-register.blade.php
rm -f professional-verify-registration.blade.php
echo -e "${GREEN}✓ Arquivos duplicados da raiz removidos${NC}\n"

# 8. Remover arquivos de exemplo e testes não utilizados
echo -e "${YELLOW}[8/8] Removendo arquivos de exemplo...${NC}"
rm -f resources/views/welcome-old.blade.php
rm -f zapby_redondo.xcf
echo -e "${GREEN}✓ Arquivos de exemplo removidos${NC}\n"

# Resumo
echo -e "${GREEN}=== Limpeza Concluída! ===${NC}\n"
echo "Arquivos mantidos:"
echo "  ✓ Código-fonte (app/, resources/, routes/)"
echo "  ✓ Configurações (config/, .env)"
echo "  ✓ Scripts úteis (scripts/sync-to-vps.sh, etc)"
echo "  ✓ Bot WhatsApp (whatsapp-bot/)"
echo "  ✓ Documentação essencial (README.md, BOT_GERENCIAMENTO.md)"
echo ""
echo "Arquivos removidos:"
echo "  ✗ Documentação antiga (12 arquivos)"
echo "  ✗ Scripts obsoletos (15 arquivos)"
echo "  ✗ Logs antigos (truncados)"
echo "  ✗ Views compiladas"
echo "  ✗ Arquivos duplicados"
echo ""
