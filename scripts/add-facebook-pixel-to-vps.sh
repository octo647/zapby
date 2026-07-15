#!/bin/bash

# Script para adicionar FACEBOOK_PIXEL_ID no .env do VPS

echo "=== Configurando Facebook Pixel no VPS ==="

# Conectar via SSH e adicionar a variável
ssh zapby << 'ENDSSH'
cd /var/www/zapby

# Verificar se a variável já existe
if grep -q "FACEBOOK_PIXEL_ID" .env; then
    echo "✓ FACEBOOK_PIXEL_ID já existe no .env"
else
    echo "Adicionando FACEBOOK_PIXEL_ID ao .env..."
    echo "" >> .env
    echo "# Facebook Pixel" >> .env
    echo "FACEBOOK_PIXEL_ID=2150463045748997" >> .env
    echo "✓ FACEBOOK_PIXEL_ID adicionado"
fi

# Mostrar a configuração
echo ""
echo "Configuração atual:"
grep FACEBOOK_PIXEL_ID .env

# Limpar e cachear configuração
echo ""
echo "Limpando cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache

echo ""
echo "✓ Configuração aplicada com sucesso!"
ENDSSH

echo ""
echo "=== Concluído! ==="
echo ""
echo "Teste agora:"
echo "1. Abra https://zapby.com.br no navegador"
echo "2. Abra o console (F12) e digite: window.fbq"
echo "3. Se retornar uma função, o pixel está funcionando!"
