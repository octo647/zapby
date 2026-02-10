#!/bin/bash
# Reset Evolution API WhatsApp Instance

set -e

API_KEY='AgendaZap2026!Secure'
INSTANCE='agendazap'
BASE_URL='http://localhost:8080'

echo "🗑️  Deletando instância antiga..."
curl -X DELETE "$BASE_URL/instance/delete/$INSTANCE" \
  -H "apikey: $API_KEY" || true

echo ""
echo "⏳ Aguardando 3 segundos..."
sleep 3

echo ""
echo "✨ Criando nova instância..."
curl -X POST "$BASE_URL/instance/create" \
  -H "apikey: $API_KEY" \
  -H "Content-Type: application/json" \
  -d "{
    \"instanceName\": \"$INSTANCE\",
    \"qrcode\": true,
    \"integration\": \"WHATSAPP-BAILEYS\"
  }"

echo ""
echo "⏳ Aguardando 5 segundos..."
sleep 5

echo ""
echo "📱 Gerando QR Code..."
curl -s "$BASE_URL/instance/connect/$INSTANCE" \
  -H "apikey: $API_KEY" | jq '.'

echo ""
echo "✅ Pronto! Escaneie o QR Code acima com seu WhatsApp"
echo "   Para ver como imagem PNG:"
echo "   curl -s $BASE_URL/instance/connect/$INSTANCE -H 'apikey: $API_KEY' | jq -r '.qrcode.base64' | base64 -d > /tmp/qrcode.png"
