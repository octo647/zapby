#!/bin/bash
# Clean Evolution API database completely

set -e

echo "🔍 Checking database for instances..."
ssh -p 22022 helder@69.6.222.77 'docker exec evolution_postgres psql -U evolution -d evolution -c "SELECT instance_name, owner_jid, connection_status FROM \"Instance\";"'

echo ""
echo "🗑️  Deleting ALL instance data from database..."
ssh -p 22022 helder@69.6.222.77 'docker exec evolution_postgres psql -U evolution -d evolution -c "DELETE FROM \"Instance\" WHERE instance_name = '\''zapby'\'';"'

echo ""
echo "✅ Database cleaned!"

echo ""
echo "🔄 Restarting Evolution API container..."
ssh -p 22022 helder@69.6.222.77 'cd /home/helder/evolution-api && docker compose restart evolution-api'

echo ""
echo "⏳ Waiting 10 seconds for Evolution API to start..."
sleep 10

echo ""
echo "✨ Creating fresh instance..."
ssh -p 22022 helder@69.6.222.77 'curl -X POST http://localhost:8080/instance/create -H "apikey: Zapby2026!Secure" -H "Content-Type: application/json" -d "{\"instanceName\":\"zapby\",\"qrcode\":true,\"integration\":\"WHATSAPP-BAILEYS\"}"'

echo ""
echo "⏳ Waiting 5 seconds..."
sleep 5

echo ""
echo "📱 Generating QR Code..."
ssh -p 22022 helder@69.6.222.77 'curl -s http://localhost:8080/instance/connect/zapby -H "apikey: Zapby2026!Secure" | jq "."'
