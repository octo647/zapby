#!/bin/bash

# Script completo para conectar WhatsApp na Evolution API
# Instância: agendazap

API_KEY='AgendaZap2026!Secure'
INSTANCE='agendazap'

echo "🔧 Preparando conexão WhatsApp - Instância: $INSTANCE"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

echo "1️⃣ Verificando status atual..."
STATUS=$(curl -s http://localhost:8080/instance/connectionState/$INSTANCE \
  -H "apikey: $API_KEY")
echo "$STATUS" | jq '.'
echo ""

echo "2️⃣ Desconectando instância anterior (se houver)..."
curl -s -X DELETE http://localhost:8080/instance/logout/$INSTANCE \
  -H "apikey: $API_KEY" | jq '.'
echo ""

echo "3️⃣ Aguardando 3 segundos..."
sleep 3
echo ""

echo "4️⃣ Reiniciando instância..."
curl -s -X PUT http://localhost:8080/instance/restart/$INSTANCE \
  -H "apikey: $API_KEY" | jq '.'
echo ""

echo "5️⃣ Aguardando 5 segundos..."
sleep 5
echo ""

echo "6️⃣ Gerando QR Code..."
RESPONSE=$(curl -s http://localhost:8080/instance/connect/$INSTANCE \
  -H "apikey: $API_KEY")

echo "$RESPONSE" | jq '.'
echo ""

# Verificar se retornou QR Code
if echo "$RESPONSE" | jq -e '.qrcode.base64' > /dev/null 2>&1; then
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "✅ QR CODE GERADO COM SUCESSO!"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
    
    # Salvar como PNG
    echo "$RESPONSE" | jq -r '.qrcode.base64' | base64 -d > /tmp/qrcode-$INSTANCE.png
    echo "💾 Salvo em: /tmp/qrcode-$INSTANCE.png"
    echo ""
    
    # Mostrar URL para copiar
    echo "🌐 Copie e cole esta URL no navegador:"
    echo ""
    BASE64=$(echo "$RESPONSE" | jq -r '.qrcode.base64')
    echo "data:image/png;base64,$BASE64" | head -c 100
    echo "..."
    echo ""
    echo "Ou acesse:"
    echo "http://agenda.pagby.com.br:8080/instance/connect/$INSTANCE?apikey=$API_KEY"
    echo ""
    
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "📱 PRÓXIMOS PASSOS:"
    echo "1. Abra WhatsApp no celular"
    echo "2. Configurações > Aparelhos conectados"
    echo "3. Conectar um aparelho"
    echo "4. Escaneie o QR Code"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    
else
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo "❌ ERRO: Não foi possível gerar o QR Code"
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    echo ""
    echo "🔍 Verificando logs..."
    echo "Execute: docker logs evolution_api --tail 50"
    echo ""
    echo "💡 Possíveis soluções:"
    echo "1. Verificar se o container está rodando: docker ps"
    echo "2. Reiniciar container: docker restart evolution_api"
    echo "3. Ver logs de erro: docker logs evolution_api --tail 100"
fi
