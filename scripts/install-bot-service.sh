#!/bin/bash

# Script para instalar e iniciar o serviço do WhatsApp Bot como systemd

echo "🔧 Instalando serviço Zapby WhatsApp Bot..."
echo ""

# Copiar arquivo de serviço para systemd
echo "📝 Copiando arquivo de serviço..."
sudo cp zapby-bot.service /etc/systemd/system/

# Recarregar systemd
echo "🔄 Recarregando systemd..."
sudo systemctl daemon-reload

# Habilitar serviço para iniciar no boot
echo "✅ Habilitando serviço para iniciar automaticamente..."
sudo systemctl enable zapby-bot.service

# Iniciar serviço
echo "🚀 Iniciando serviço..."
sudo systemctl start zapby-bot.service

# Aguardar um momento
sleep 2

# Verificar status
echo ""
echo "📊 Status do serviço:"
sudo systemctl status zapby-bot.service --no-pager

echo ""
echo "✅ Instalação concluída!"
echo ""
echo "Comandos úteis:"
echo "  sudo systemctl status zapby-bot    # Ver status"
echo "  sudo systemctl start zapby-bot     # Iniciar"
echo "  sudo systemctl stop zapby-bot      # Parar"
echo "  sudo systemctl restart zapby-bot   # Reiniciar"
echo "  sudo systemctl logs zapby-bot      # Ver logs"
echo "  sudo journalctl -u zapby-bot -f    # Ver logs em tempo real"
echo ""
