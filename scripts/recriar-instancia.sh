#!/bin/bash

# Script para deletar e recriar instância agendazap
# Resolve problema de loop de reconexão

API_KEY='AgendaZap2026!Secure'
INSTANCE='agendazap'

echo "🔧 Resolvendo problema da instância $INSTANCE"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

echo "1️⃣ Deletando instância antiga (com dados corrompidos)..."
DELETED=$(curl -s -X DELETE http://localhost:8080/instance/delete/$INSTANCE \
  -H "apikey: $API_KEY")

echo "$DELETED" | jq '.'
echo ""

echo "⏳ Aguardando 3 segundos..."
sleep 3
echo ""

echo "2️⃣ Criando nova instância limpa..."
CREATED=$(curl -s -X POST http://localhost:8080/instance/create \
  -H "apikey: $API_KEY" \
  -H 'Content-Type: application/json' \
  -d '{
    "instanceName": "'$INSTANCE'",
    "qrcode": true,
    "integration": "WHATSAPP-BAILEYS"
  }')

echo "$CREATED" | jq '.'
echo ""

echo "⏳ Aguardando 5 segundos para instância inicializar..."
sleep 5
echo ""

echo "3️⃣ Gerando QR Code..."
RESPONSE=$(curl -s http://localhost:8080/instance/connect/$INSTANCE \
  -H "apikey: $API_KEY")

echo "$RESPONSE" | jq '.'
echo ""

# Verificar se retornou QR Code
if echo "$RESPONSE" | jq -e '.qrcode.base64' > /dev/null 2>&1; then
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "✅ SUCESSO! QR CODE GERADO!"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
    
    # Salvar como PNG
    echo "$RESPONSE" | jq -r '.qrcode.base64' | base64 -d > /tmp/qrcode-$INSTANCE.png
    echo "💾 Arquivo salvo: /tmp/qrcode-$INSTANCE.png"
    echo ""
    
    # Mostrar URL
    echo "🌐 Acesse no navegador:"
    echo "http://agenda.pagby.com.br:8080/instance/connect/$INSTANCE?apikey=$API_KEY"
    echo ""
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "📱 COMO ESCANEAR:"
    echo "1. Abra WhatsApp no celular"
    echo "2. Configurações > Aparelhos conectados"
    echo "3. Conectar um aparelho"
    echo "4. Escaneie o QR Code"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    
else
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "❌ Erro ao gerar QR Code"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
    echo "Tente executar manualmente:"
    echo "curl -s http://localhost:8080/instance/connect/$INSTANCE -H 'apikey: $API_KEY' | jq '.'"
fi
