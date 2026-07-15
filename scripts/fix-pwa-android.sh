#!/bin/bash

# Script para corrigir PWA no Android
# Aplica as configurações de segurança necessárias

set -e  # Sair em caso de erro

echo "🔧 Aplicando correções para PWA no Android..."
echo ""

# Verificar se está no diretório correto
if [ ! -f "artisan" ]; then
    echo "❌ Execute este script a partir da raiz do projeto"
    exit 1
fi

# Variáveis
VPS_USER="helder"
VPS_HOST="69.6.222.77"
PROJECT_PATH="/var/www/zapby"

# Testar conexão SSH primeiro
echo "🔍 Testando conexão com o servidor..."
if ! ssh -p 22022 -o ConnectTimeout=5 $VPS_USER@$VPS_HOST "echo '✅ Conexão OK'" 2>/dev/null; then
    echo "❌ Não foi possível conectar ao servidor $VPS_HOST"
    echo "   Verifique sua conexão e tente novamente"
    exit 1
fi
echo ""

echo "📤 Sincronizando arquivos do PWA para o servidor..."
echo "   (Ctrl+C para cancelar)"
echo ""

# Sincronizar manifest.json
echo "→ manifest.json"
rsync -avz --progress -e "ssh -p 22022" public/manifest.json $VPS_USER@$VPS_HOST:$PROJECT_PATH/public/

# Sincronizar service worker
echo "→ sw.js"
rsync -avz --progress -e "ssh -p 22022" public/sw.js $VPS_USER@$VPS_HOST:$PROJECT_PATH/public/

# Sincronizar .htaccess
echo "→ .htaccess"
rsync -avz --progress -e "ssh -p 22022" public/.htaccess $VPS_USER@$VPS_HOST:$PROJECT_PATH/public/

# Sincronizar view
echo "→ welcome.blade.php"
rsync -avz --progress -e "ssh -p 22022" resources/views/welcome.blade.php $VPS_USER@$VPS_HOST:$PROJECT_PATH/resources/views/

echo "🔐 Aplicando configuração do Apache..."

# Copiar configuração do Apache
rsync -avz -e "ssh -p 22022" scripts/zapby.conf $VPS_USER@$VPS_HOST:/tmp/

# Aplicar configuração no servidor
ssh -p 22022 $VPS_USER@$VPS_HOST << 'ENDSSH'
    # Mover configuração
    sudo mv /tmp/zapby.conf /etc/httpd/conf.d/zapby.conf
    
    # Ativar módulo headers se não estiver ativo
    sudo dnf install -y mod_headers 2>/dev/null || true
    
    # Testar configuração
    sudo apachectl configtest
    
    # Se o teste passou, recarregar Apache
    if [ $? -eq 0 ]; then
        sudo systemctl reload httpd
        echo "✅ Apache recarregado com sucesso"
    else
        echo "❌ Erro na configuração do Apache"
        exit 1
    fi
    
    # Limpar cache do Laravel
    cd /var/www/zapby
    php artisan cache:clear
    php artisan config:clear
    php artisan view:clear
    
    echo "✅ Cache do Laravel limpo"
ENDSSH

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ PWA atualizado com sucesso!"
    echo ""
    echo "📋 Próximos passos:"
    echo "1. Acesse https://zapby.com.br no seu Android"
    echo "2. Abra o Chrome DevTools (chrome://inspect no desktop)"
    echo "3. Verifique o manifest em Application > Manifest"
    echo "4. Verifique o Service Worker em Application > Service Workers"
    echo "5. Teste a instalação do PWA"
    echo ""
    echo "🔍 Para depuração:"
    echo "   - Acesse chrome://inspect#devices no desktop"
    echo "   - Conecte seu Android via USB"
    echo "   - Inspecione a página do ZapBy"
    echo ""
else
    echo "❌ Erro ao aplicar as correções"
    exit 1
fi
