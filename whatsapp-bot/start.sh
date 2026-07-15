#!/bin/bash
# Inicia o bot WhatsApp do Zapby

cd "$(dirname "$0)")

echo "🚀 Iniciando Zapby WhatsApp Bot..."
echo ""

# Verificar se node_modules existe
if [ ! -d "node_modules" ]; then
    echo "📦 Instalando dependências..."
    npm install
    echo ""
fi

# Criar diretório para lembretes se não existir
mkdir -p ../storage/app

# Iniciar bot
node index.js
