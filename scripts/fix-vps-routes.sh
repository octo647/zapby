#!/bin/bash

# Script para executar DIRETAMENTE no VPS
# Resolve o erro: Route [professional.subscription] not defined

echo "🧹 Limpando cache no VPS..."
echo ""

# Limpar TODOS os caches
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear

echo ""
echo "✅ Cache limpo!"
echo ""

# Recriar cache otimizado
echo "♻️  Recriando cache otimizado..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo ""
echo "✅ Cache recriado!"
echo ""

# Verificar rotas
echo "🔍 Verificando rotas registradas..."
echo ""
php artisan route:list | grep "professional\."

echo ""
echo "✅ Processo concluído!"
echo ""
echo "Se você vê rotas como 'professional.subscription' acima, está tudo OK!"
echo "Caso contrário, verifique se os arquivos foram sincronizados corretamente."
