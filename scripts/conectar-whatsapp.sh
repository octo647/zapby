#!/bin/bash

# Script para conectar WhatsApp na Evolution API
# Instância: zapby

API_KEY='Zapby2026!Secure'
INSTANCE='zapby'

echo "🚀 Conectando WhatsApp - Instância: $INSTANCE"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

# Gerar QR Code
echo "📱 Gerando QR Code..."
echo ""

RESPONSE=$(curl -s http://localhost:8080/instance/connect/$INSTANCE \
  -H "apikey: $API_KEY")

# Verificar se retornou QR Code
if echo "$RESPONSE" | jq -e '.qrcode.base64' > /dev/null 2>&1; then
    echo "✅ QR Code gerado com sucesso!"
    echo ""
    
    # Salvar como PNG
    echo "$RESPONSE" | jq -r '.qrcode.base64' | base64 -d > /tmp/qrcode-$INSTANCE.png
    echo "💾 Salvo em: /tmp/qrcode-$INSTANCE.png"
    echo ""
    
    # Mostrar base64 (para copiar e colar no navegador)
    echo "🌐 Cole esta URL no navegador para ver o QR Code:"
    echo ""
    BASE64=$(echo "$RESPONSE" | jq -r '.qrcode.base64')
    echo "data:image/png;base64,$BASE64"
    echo ""
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "📱 COMO ESCANEAR:"
    echo "1. Abra WhatsApp no celular"
    echo "2. Configurações > Aparelhos conectados"
    echo "3. Conectar um aparelho"
    echo "4. Escaneie o QR Code"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    
else
    echo "❌ Erro ao gerar QR Code"
    echo ""
    echo "Resposta da API:"
    echo "$RESPONSE" | jq '.'
    echo ""
    echo "Possíveis causas:"
    echo "1. Instância já está conectada"
    echo "2. Instância com problema - tente reiniciar:"
    echo "   curl -X PUT http://localhost:8080/instance/restart/$INSTANCE -H 'apikey: $API_KEY'"
fi

echo ""
echo "🔍 Status atual:"
curl -s http://localhost:8080/instance/connectionState/$INSTANCE \
  -H "apikey: $API_KEY" | jq '.'
