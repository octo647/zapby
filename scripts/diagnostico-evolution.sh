#!/bin/bash

# Script de diagnóstico Evolution API
API_KEY='AgendaZap2026!Secure'
INSTANCE_NAME='agendazap'

echo "🔍 Diagnóstico Evolution API - Instância: $INSTANCE_NAME"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

echo "1️⃣ Verificando todas as instâncias..."
curl -s http://localhost:8080/instance/fetchInstances \
  -H "apikey: $API_KEY" | jq '.'

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

echo "2️⃣ Status da instância $INSTANCE_NAME..."
curl -s http://localhost:8080/instance/connectionState/$INSTANCE_NAME \
  -H "apikey: $API_KEY" | jq '.'

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
