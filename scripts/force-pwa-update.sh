#!/bin/bash

# Força atualização do PWA e ativa headers de segurança

echo "🔧 Forçando atualização do PWA..."
echo ""

# 1. Ativar módulo headers no Apache
echo "📦 1/4 Ativando módulo mod_headers..."
ssh -p 22022 helder@69.6.222.77 << 'ENDSSH'
    # Verificar se mod_headers existe
    if [ -f /etc/httpd/conf.modules.d/00-base.conf ]; then
        # Descomentar ou adicionar LoadModule headers_module
        if ! grep -q "^LoadModule headers_module" /etc/httpd/conf.modules.d/00-base.conf; then
            echo "LoadModule headers_module modules/mod_headers.so" | sudo tee -a /etc/httpd/conf.modules.d/00-base.conf
        fi
    fi
    
    # Para AlmaLinux/Rocky, o módulo já vem instalado mas pode estar desabilitado
    sudo dnf install -y httpd-devel 2>/dev/null || true
    
    echo "✅ Módulo headers configurado"
ENDSSH

# 2. Recarregar Apache
echo ""
echo "🔄 2/4 Recarregando Apache com nova configuração..."
ssh -p 22022 helder@69.6.222.77 "sudo systemctl restart httpd && echo '✅ Apache reiniciado'"

# 3. Incrementar versão do Service Worker para forçar atualização
echo ""
echo "🔄 3/4 Incrementando versão do Service Worker..."
NEW_VERSION="zapby-v3-$(date +%Y%m%d-%H%M%S)"
sed -i "s/const CACHE_VERSION = 'zapby-v2-2026'/const CACHE_VERSION = '$NEW_VERSION'/" public/sw.js
echo "   Nova versão: $NEW_VERSION"

# Enviar novo SW para servidor
scp -P 22022 public/sw.js helder@69.6.222.77:/var/www/zapby/public/

# 4. Limpar tudo no servidor
echo ""
echo "🧹 4/4 Limpando cache e forçando refresh..."
ssh -p 22022 helder@69.6.222.77 << 'ENDSSH'
    cd /var/www/zapby
    
    # Limpar cache do Laravel
    php artisan cache:clear
    php artisan config:clear
    php artisan view:clear
    php artisan route:clear
    
    # Limpar cache do OPcache
    sudo systemctl reload php-fpm 2>/dev/null || true
    
    echo "✅ Cache limpo"
ENDSSH

echo ""
echo "✅ Atualização forçada concluída!"
echo ""
echo "📱 NO SEU ANDROID, FAÇA:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "1. Abra o Chrome e vá para chrome://serviceworker-internals"
echo "2. Procure por 'zapby.com.br' e clique em 'Unregister'"
echo "3. Vá para Configurações > Apps > Chrome > Armazenamento"
echo "4. Clique em 'Limpar cache' e 'Limpar dados'"
echo "5. Se já instalou o PWA, desinstale-o primeiro"
echo "6. Acesse https://zapby.com.br novamente"
echo "7. Aguarde o novo prompt de instalação"
echo ""
echo "OU MAIS SIMPLES:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "1. No Chrome, acesse https://zapby.com.br"
echo "2. Menu (⋮) > Mais ferramentas > Ferramentas do desenvolvedor"
echo "3. Aba 'Application' > 'Service Workers'"
echo "4. Marque 'Update on reload'"
echo "5. Clique em 'Unregister'"
echo "6. Recarregue a página (F5 ou puxar para baixo)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
