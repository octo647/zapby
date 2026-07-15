# 🚀 Sincronização com VPS - Zapby

## Configuração Inicial

### 1. Edite o script de sincronização

Abra o arquivo `scripts/sync-to-vps.sh` e configure suas credenciais:

```bash
VPS_USER="seu_usuario"        # Usuário SSH do VPS
VPS_HOST="seu_vps.com.br"     # IP ou domínio do VPS
VPS_PATH="/var/www/zapby" # Caminho da aplicação no VPS
```

### 2. Configure acesso SSH sem senha (Recomendado)

```bash
# Gerar chave SSH (se ainda não tiver)
ssh-keygen -t rsa -b 4096

# Copiar chave para o VPS
ssh-copy-id seu_usuario@seu_vps.com.br

# Testar conexão
ssh seu_usuario@seu_vps.com.br
```

### 3. Prepare o VPS

No VPS, certifique-se de ter:

```bash
# PHP 8.2+
php -v

# Composer
composer --version

# Permissões corretas
sudo chown -R seu_usuario:www-data /var/www/zapby
sudo chmod -R 775 /var/www/zapby
```

## 🔄 Como Sincronizar

### Sincronização Completa

```bash
./scripts/sync-to-vps.sh
```

Este comando irá:
1. ✅ Verificar conexão com VPS
2. 📦 Sincronizar arquivos (rsync)
3. 🔧 Instalar dependências do Composer
4. 🗄️ Executar migrations
5. 🚀 Limpar e otimizar cache
6. 🔒 Ajustar permissões

### Sincronização Rápida (apenas arquivos)

```bash
rsync -avz --exclude='vendor/' --exclude='node_modules/' --exclude='.env' \
  ./ seu_usuario@seu_vps.com.br:/var/www/zapby/
```

## 📋 Checklist Pós-Sincronização

### No VPS:

1. **Verificar .env**
```bash
ssh seu_usuario@seu_vps.com.br
cd /var/www/zapby
cat .env  # Verificar se existe e está configurado
```

2. **Executar migrations (se necessário)**
```bash
php artisan migrate --force
```

3. **Limpar cache**
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:cache
php artisan view:cache
```

4. **Verificar permissões**
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

5. **Reiniciar serviços**
```bash
# Nginx
sudo systemctl restart nginx

# PHP-FPM
sudo systemctl restart php8.2-fpm

# Queue workers (se estiver usando)
sudo supervisorctl restart zapby-worker:*
```

## 🔍 Verificação

### Testar a aplicação

```bash
# No VPS
cd /var/www/zapby

# Testar conexão com banco
php artisan tinker
>>> \DB::connection()->getPdo();

# Verificar rotas
php artisan route:list

# Ver logs
tail -f storage/logs/laravel.log
```

### Testar no navegador

1. Acesse: `http://seu_vps.com.br`
2. Teste login: `http://seu_vps.com.br/professional/login`
3. Digite um número de WhatsApp cadastrado
4. Verifique se o código OTP é enviado

## 🐛 Solução de Problemas

### Erro de permissão

```bash
ssh seu_usuario@seu_vps.com.br "cd /var/www/zapby && sudo chown -R www-data:www-data storage bootstrap/cache && sudo chmod -R 775 storage bootstrap/cache"
```

### Erro de dependências

```bash
ssh seu_usuario@seu_vps.com.br "cd /var/www/zapby && composer install --no-dev --optimize-autoloader"
```

### Cache desatualizado

```bash
ssh seu_usuario@seu_vps.com.br "cd /var/www/zapby && php artisan config:clear && php artisan cache:clear && php artisan route:cache && php artisan view:cache"
```

### Migration falhou

```bash
ssh seu_usuario@seu_vps.com.br "cd /var/www/zapby && php artisan migrate:status"
ssh seu_usuario@seu_vps.com.br "cd /var/www/zapby && php artisan migrate --force"
```

## 📝 Variáveis de Ambiente Importantes

Certifique-se de que o `.env` no VPS possui:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu_vps.com.br

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agendazap
DB_USERNAME=seu_usuario_db
DB_PASSWORD=sua_senha_db

# WhatsApp Service
WHATSAPP_API_URL=http://localhost:8080
WHATSAPP_API_KEY=sua_chave_api
```

## 🔐 Segurança

1. **Nunca sincronize o arquivo .env**
2. **Use chaves SSH em vez de senhas**
3. **Mantenha o APP_DEBUG=false em produção**
4. **Use HTTPS em produção**
5. **Configure firewall no VPS**

## 📦 Estrutura de Arquivos Sincronizados

Arquivos/pastas **INCLUÍDOS**:
- ✅ `app/`
- ✅ `config/`
- ✅ `database/migrations/`
- ✅ `resources/`
- ✅ `routes/`
- ✅ `public/`
- ✅ `scripts/`

Arquivos/pastas **EXCLUÍDOS**:
- ❌ `vendor/` (reinstalado via composer)
- ❌ `node_modules/`
- ❌ `.env` (configurado manualmente no VPS)
- ❌ `storage/logs/`
- ❌ `storage/framework/cache/`
- ❌ `.git/`

## 🎯 Atalhos Úteis

Crie aliases no seu `~/.bashrc` ou `~/.zshrc`:

```bash
# Sincronizar com VPS
alias sync-vps='cd ~/projetos/zapby && ./scripts/sync-to-vps.sh'

# Conectar no VPS
alias vps='ssh seu_usuario@seu_vps.com.br'

# Ver logs do VPS
alias vps-logs='ssh seu_usuario@seu_vps.com.br "tail -f /var/www/zapby/storage/logs/laravel.log"'
```

Depois recarregue:
```bash
source ~/.bashrc  # ou source ~/.zshrc
```
