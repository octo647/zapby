#!/bin/bash

# Deploy simples do PWA usando SCP

echo "🚀 Deploy PWA para 69.6.222.77 (zapby.com.br)"
echo ""

# Copiar arquivos
echo "📤 1/5 Copiando manifest.json..."
scp -P 22022 public/manifest.json helder@69.6.222.77:/var/www/zapby/public/

echo "📤 2/5 Copiando service worker..."
scp -P 22022 public/sw.js helder@69.6.222.77:/var/www/zapby/public/

echo "📤 3/5 Copiando .htaccess..."
scp -P 22022 public/.htaccess helder@69.6.222.77:/var/www/zapby/public/

echo "📤 4/5 Copiando welcome.blade.php..."
scp -P 22022 resources/views/welcome.blade.php helder@69.6.222.77:/var/www/zapby/resources/views/

echo "📤 5/5 Copiando configuração Apache..."
scp -P 22022 scripts/zapby.conf helder@69.6.222.77:/tmp/

echo ""
echo "⚙️  Aplicando configuração Apache..."
ssh -p 22022 helder@69.6.222.77 "sudo mv /tmp/zapby.conf /etc/httpd/conf.d/zapby.conf && sudo apachectl configtest && sudo systemctl reload httpd"

echo ""
echo "🧹 Limpando cache Laravel..."
ssh -p 22022 helder@69.6.222.77 "cd /var/www/zapby && php artisan cache:clear && php artisan view:clear"

echo ""
echo "✅ Deploy concluído!"
echo "🔗 Teste em: https://zapby.com.br"
