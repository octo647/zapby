#!/bin/bash
# Script para gerar e baixar QR Code do WhatsApp via Evolution API

set -e

API_KEY='AgendaZap2026!Secure'
INSTANCE='agendazap'
VPS_HOST='69.6.222.77'
VPS_PORT='22022'
VPS_USER='helder'

echo "🔄 Conectando ao VPS..."
echo ""

# Tentar conectar várias vezes até conseguir o QR code
for i in {1..20}; do
    echo "📱 Tentativa $i/20 - Buscando QR Code..."
    
    RESULT=$(ssh -p $VPS_PORT $VPS_USER@$VPS_HOST \
        "curl -s http://localhost:8080/instance/connect/$INSTANCE -H 'apikey: $API_KEY'")
    
    # Verificar se tem base64
    BASE64=$(echo "$RESULT" | jq -r '.qrcode.base64' 2>/dev/null)
    
    if [ "$BASE64" != "null" ] && [ ! -z "$BASE64" ]; then
        echo "✅ QR Code encontrado!"
        echo ""
        
        # Salvar no VPS
        ssh -p $VPS_PORT $VPS_USER@$VPS_HOST \
            "echo '$BASE64' | base64 -d > /tmp/qrcode-agendazap.png"
        
        # Baixar para a máquina local
        scp -P $VPS_PORT $VPS_USER@$VPS_HOST:/tmp/qrcode-agendazap.png ~/Downloads/qrcode-agendazap.png
        
        echo "✅ QR Code salvo em: ~/Downloads/qrcode-agendazap.png"
        echo ""
        echo "📱 Agora:"
        echo "   1. Abra o arquivo ~/Downloads/qrcode-agendazap.png"
        echo "   2. No WhatsApp: Configurações > Aparelhos conectados"
        echo "   3. Escaneie o QR Code"
        echo ""
        
        exit 0
    fi
    
    echo "⏳ Aguardando 3 segundos..."
    sleep 3
done

echo "❌ QR Code não foi gerado após 20 tentativas"
echo "   O Evolution API pode estar com problemas"
