#!/bin/bash
# Script de teste do WhatsApp Bot

echo "🧪 Testando WhatsApp Bot do AgendaZap"
echo ""

# Verificar se bot está rodando
if pgrep -f "agendazap/whatsapp-bot/index.js" > /dev/null; then
    echo "✅ Bot está rodando"
else
    echo "❌ Bot NÃO está rodando"
    echo "   Execute: cd whatsapp-bot && ./start.sh"
    exit 1
fi

# Criar lembrete de teste
echo ""
echo "📝 Criando lembrete de teste..."

cat > storage/app/appointment_reminders.json << 'EOF'
[
  {
    "customer_phone": "32987007302",
    "customer_name": "Teste AgendaZap",
    "professional_name": "Dr. Pedro Silva",
    "scheduled_at": "10/02/2026 às 14:30",
    "service_name": "Consulta Teste"
  }
]
EOF

echo "✅ Lembrete criado!"
echo ""
echo "📱 Aguarde até 30 segundos..."
echo "   Você receberá uma mensagem no WhatsApp: 32987007302"
echo ""
echo "📋 Para ver logs do bot:"
echo "   tail -f /tmp/agendazap-bot.log"
echo ""
echo "✨ Teste enviado!"
