#!/bin/bash

# Script para limpar cache no VPS
echo "🧹 Limpando cache no VPS..."

# Limpar cache de configuração
php artisan config:clear
echo "✅ Config cache limpo"

# Limpar cache de rotas
php artisan route:clear
echo "✅ Route cache limpo"

# Limpar cache de views
php artisan view:clear
echo "✅ View cache limpo"

# Limpar cache da aplicação
php artisan cache:clear
echo "✅ Application cache limpo"

# Recriar cache otimizado (opcional - use apenas em produção)
# php artisan config:cache
# php artisan route:cache
# php artisan view:cache

# Listar todas as rotas para verificar
echo ""
echo "📋 Rotas registradas:"
php artisan route:list --path=professional

echo ""
echo "✅ Cache limpo com sucesso!"
