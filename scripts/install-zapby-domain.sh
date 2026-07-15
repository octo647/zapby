#!/bin/bash

echo "🔧 Instalando configuração do domínio zapby.com.br..."

# Copia arquivo de configuração
sudo cp /var/www/zapby/scripts/zapby.conf /etc/httpd/conf.d/zapby.conf
echo "✅ Arquivo de configuração copiado"

# Verifica se o domínio está apontando para o servidor
echo "📡 Verificando DNS do domínio..."
ping -c 1 zapby.com.br 2>/dev/null || echo "⚠️  DNS pode não estar configurado ainda"

# Pergunta se deve instalar certificado SSL
read -p "Deseja instalar o certificado SSL agora? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]
then
    echo "🔒 Instalando certificado SSL com Let's Encrypt..."
    sudo certbot --apache -d zapby.com.br -d www.zapby.com.br --non-interactive --agree-tos --email helder@pagby.com.br || echo "⚠️  Erro ao instalar SSL. Verifique se o DNS está configurado."
else
    echo "⚠️  SSL não instalado. Execute manualmente: sudo certbot --apache -d zapby.com.br -d www.zapby.com.br"
fi

# Testa configuração do Apache
echo "🧪 Testando configuração do Apache..."
sudo apachectl configtest

# Reinicia Apache
echo "🔄 Reiniciando Apache..."
sudo systemctl restart httpd

echo "✅ Configuração concluída!"
echo "🌐 Aguarde a propagação do DNS e acesse: https://zapby.com.br"
