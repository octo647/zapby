#!/bin/bash

# 🔍 Script de Verificação - Mudanças Aplicadas
# Verifica se todas as correções foram aplicadas corretamente

echo "════════════════════════════════════════════════════════"
echo "🔍 Verificação de Mudanças - WhatsApp Bot"
echo "════════════════════════════════════════════════════════"
echo ""

BOT_DIR="/var/www/zapby/whatsapp-bot"
ERRORS=0

# 1️⃣ Verificar Baileys versão
echo "1️⃣  Verificando versão do Baileys..."
if grep -q '"baileys": "^7.2.0"' "$BOT_DIR/package.json"; then
    echo "   ✅ Baileys 7.2.0 configurado"
else
    echo "   ❌ Baileys não está na versão 7.2.0"
    ERRORS=$((ERRORS + 1))
fi

# 2️⃣ Verificar pino
echo "2️⃣  Verificando pino logger..."
if grep -q '"pino"' "$BOT_DIR/package.json"; then
    echo "   ✅ Pino logger adicionado"
else
    echo "   ❌ Pino não foi adicionado"
    ERRORS=$((ERRORS + 1))
fi

# 3️⃣ Verificar função de retry
echo "3️⃣  Verificando função sendMessageWithRetry..."
if grep -q "async function sendMessageWithRetry" "$BOT_DIR/index.js"; then
    echo "   ✅ Função de retry implementada"
else
    echo "   ❌ Função de retry não encontrada"
    ERRORS=$((ERRORS + 1))
fi

# 4️⃣ Verificar desabilitar sync histórico
echo "4️⃣  Verificando configuração de socket..."
if grep -q "shouldSyncHistoryMessage: false" "$BOT_DIR/index.js"; then
    echo "   ✅ shouldSyncHistoryMessage desabilitado"
else
    echo "   ⚠️  shouldSyncHistoryMessage não encontrado"
fi

if grep -q "syncFullHistory: false" "$BOT_DIR/index.js"; then
    echo "   ✅ syncFullHistory desabilitado"
else
    echo "   ⚠️  syncFullHistory não encontrado"
fi

# 5️⃣ Verificar systemd service
echo "5️⃣  Verificando arquivo de service..."
if [ -f "$BOT_DIR/zapby-whatsapp-bot.service" ]; then
    echo "   ✅ Service file criado"
else
    echo "   ❌ Service file não encontrado"
    ERRORS=$((ERRORS + 1))
fi

# 6️⃣ Verificar deploy script
echo "6️⃣  Verificando script de deploy..."
if [ -f "/var/www/zapby/scripts/deploy-whatsapp-bot.sh" ]; then
    echo "   ✅ Deploy script criado"
else
    echo "   ❌ Deploy script não encontrado"
    ERRORS=$((ERRORS + 1))
fi

# 7️⃣ Verificar documentação
echo "7️⃣  Verificando documentação..."
if [ -f "/var/www/zapby/WHATSAPP_BOT_FIX.md" ]; then
    echo "   ✅ Arquivo WHATSAPP_BOT_FIX.md criado"
else
    echo "   ⚠️  WHATSAPP_BOT_FIX.md não encontrado"
fi

if [ -f "/var/www/zapby/WHATSAPP_BOT_FIX_SUMMARY.md" ]; then
    echo "   ✅ Arquivo WHATSAPP_BOT_FIX_SUMMARY.md criado"
else
    echo "   ⚠️  WHATSAPP_BOT_FIX_SUMMARY.md não encontrado"
fi

# 8️⃣ Verificar número de mudanças em index.js
echo "8️⃣  Analisando mudanças em index.js..."
RETRY_COUNT=$(grep -c "sendMessageWithRetry" "$BOT_DIR/index.js" || true)
echo "   📊 Função sendMessageWithRetry usada $RETRY_COUNT vezes"

# 9️⃣ Verificar import de pino
echo "9️⃣  Verificando imports..."
if grep -q "import pino from 'pino'" "$BOT_DIR/index.js"; then
    echo "   ✅ Pino importado corretamente"
else
    echo "   ❌ Pino não foi importado"
    ERRORS=$((ERRORS + 1))
fi

# 🔟 Verificar reconexão 428
echo "🔟 Verificando tratamento de erro 428..."
if grep -q "statusCode === 428" "$BOT_DIR/index.js"; then
    echo "   ✅ Tratamento específico para erro 428 adicionado"
else
    echo "   ⚠️  Tratamento de erro 428 não encontrado"
fi

echo ""
echo "════════════════════════════════════════════════════════"
if [ $ERRORS -eq 0 ]; then
    echo "✅ Todas as mudanças foram aplicadas com sucesso!"
    echo ""
    echo "📋 Próximos passos:"
    echo "   1. Executar: bash /var/www/zapby/scripts/deploy-whatsapp-bot.sh"
    echo "   2. Verificar: sudo journalctl -u zapby-whatsapp-bot -f"
else
    echo "❌ Foram encontrados $ERRORS erro(s)"
    echo "   Verifique a aplicação das mudanças"
fi
echo "════════════════════════════════════════════════════════"
