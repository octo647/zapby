#!/bin/bash

# Script para corrigir permissões no VPS
# Execute este script DIRETAMENTE no VPS

echo "🔧 Corrigindo permissões no VPS..."
echo ""

# Detectar usuário do servidor web
WEB_USER=$(ps aux | grep -E 'apache|httpd|nginx' | grep -v grep | head -1 | awk '{print $1}')
if [ -z "$WEB_USER" ]; then
    WEB_USER="apache"
    echo "⚠ Usando usuário padrão: ${WEB_USER}"
else
    echo "✓ Usuário do servidor web detectado: ${WEB_USER}"
fi

echo ""
echo "📁 Criando diretórios necessários..."
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p storage/app/public
mkdir -p bootstrap/cache

echo ""
echo "🔐 Ajustando proprietário dos arquivos..."
sudo chown -R ${WEB_USER}:${WEB_USER} storage bootstrap/cache

echo ""
echo "🔓 Ajustando permissões..."
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache

# Permissões específicas para arquivos
find storage -type f -exec sudo chmod 664 {} \;
find storage -type d -exec sudo chmod 775 {} \;

echo ""
echo "🧹 Limpando cache compilado..."
rm -rf storage/framework/views/*.php
php artisan view:clear
php artisan cache:clear

echo ""
echo "✅ Permissões corrigidas com sucesso!"
echo ""
echo "Estrutura de permissões:"
ls -la storage/framework/ | head -10
