#!/bin/bash
# Script para instalar WPPConnect Server no VPS

set -e

VPS_HOST="69.6.222.77"
VPS_PORT="22022"
VPS_USER="helder"

echo "🚀 Instalando WPPConnect Server no VPS..."
echo ""

# Criar diretório e docker-compose no VPS
ssh -p $VPS_PORT $VPS_USER@$VPS_HOST << 'ENDSSH'

# Criar diretório
mkdir -p ~/wppconnect-server
cd ~/wppconnect-server

# Criar docker-compose.yml
cat > docker-compose.yml << 'EOF'
services:
  wppconnect:
    image: wppconnect/wppconnect-server:latest
    container_name: wppconnect_server
    restart: always
    ports:
      - "21465:21465"
    environment:
      - BASE_URL=http://69.6.222.77:21465
      - SECRET_KEY=AgendaZap2026Secret
      - PORT=21465
      - LOG_LEVEL=info
      - MAX_LISTENERS=15
      - AUTHENTICATION_ENABLED=true
    volumes:
      - wppconnect_tokens:/usr/src/api/tokens

volumes:
  wppconnect_tokens:
EOF

echo "✅ docker-compose.yml criado"

# Parar Evolution API antigo para liberar recursos
echo "🔄 Parando Evolution API..."
cd ~/evolution-api 2>/dev/null && docker compose down || echo "Evolution API não encontrado"

# Iniciar WPPConnect
echo "🚀 Iniciando WPPConnect Server..."
cd ~/wppconnect-server
docker compose pull
docker compose up -d

echo ""
echo "⏳ Aguardando WPPConnect iniciar..."
sleep 10

# Verificar logs
echo ""
echo "📋 Logs do WPPConnect:"
docker logs wppconnect_server --tail 20

ENDSSH

echo ""
echo "✅ WPPConnect Server instalado!"
echo ""
echo "📱 Próximos passos:"
echo "   1. Acesse: http://agendazap.pagby.com.br:21465/api/agendazap/start-session"
echo "   2. QR Code: http://agendazap.pagby.com.br:21465/api/agendazap/qrcode-session"
echo "   3. Status: http://agendazap.pagby.com.br:21465/api/agendazap/check-connection-session"
echo ""

# Testar API
echo "🧪 Testando API..."
sleep 5

echo ""
echo "Iniciando sessão..."
curl -X POST "http://$VPS_HOST:21465/api/agendazap/start-session" \
  -H "Authorization: Bearer AgendaZap2026Secret" \
  -H "Content-Type: application/json" | jq '.' || echo "Aguarde mais alguns segundos..."

echo ""
echo "✅ Instalação concluída!"
echo "   Abra no navegador: http://agendazap.pagby.com.br:21465/api/agendazap/qrcode-session"
