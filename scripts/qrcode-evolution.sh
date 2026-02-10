#!/bin/bash

# Script para gerar QR Code da Evolution API
# Uso: ./qrcode-evolution.sh

# Configurações - AJUSTE CONFORME SUA API KEY
EVOLUTION_URL="http://agenda.pagby.com.br:8080"
API_KEY="SUA_API_KEY_AQUI"  # Substitua pela sua API key
INSTANCE_NAME="agendazap"

echo "🔍 Verificando status da instância agendazap..."
echo ""

# Verificar status da instância
curl -s "${EVOLUTION_URL}/instance/connectionState/${INSTANCE_NAME}" \
  -H "apikey: ${API_KEY}" | jq '.'

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "📱 Gerando QR Code..."
echo ""

# Gerar QR Code
RESPONSE=$(curl -s "${EVOLUTION_URL}/instance/connect/${INSTANCE_NAME}" \
  -H "apikey: ${API_KEY}")

echo "$RESPONSE" | jq '.'

# Extrair QR Code
QRCODE=$(echo "$RESPONSE" | jq -r '.qrcode.base64' 2>/dev/null)

if [ ! -z "$QRCODE" ] && [ "$QRCODE" != "null" ]; then
    echo ""
    echo "✅ QR Code gerado com sucesso!"
    echo ""
    echo "🔗 Acesse este link no navegador para ver o QR Code:"
    echo "data:image/png;base64,$QRCODE"
    echo ""
    echo "Ou salve como imagem:"
    echo "$QRCODE" | base64 -d > qrcode.png
    echo "✅ Salvo em: qrcode.png"
else
    echo ""
    echo "⚠️  Não foi possível gerar o QR Code."
    echo ""
    echo "Possíveis causas:"
    echo "1. Instância já está conectada"
    echo "2. API Key incorreta"
    echo "3. Instância não existe"
    echo ""
    echo "💡 Tente desconectar primeiro:"
    echo "curl -X DELETE ${EVOLUTION_URL}/instance/logout/${INSTANCE_NAME} -H \"apikey: ${API_KEY}\""
fi
