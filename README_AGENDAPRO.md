# AgendaPro 📅

Sistema de agendamento ultra-simplificado para **profissionais de beleza autônomos** que alugam cadeira/espaço em salões.

## 🎯 Objetivo

Criar uma solução simples e acessível para profissionais autônomos (cabeleireiros, manicures, barbeiros, esteticistas) gerenciarem seus agendamentos sem complexidade.

## ✨ Core Features (MVP)

### 1. Cadastro via WhatsApp
- Link único para cadastro
- Bot coleta informações básicas
- Gera link de agenda personalizado

### 2. Agenda Pública
- Cliente acessa via link: `agendapro.com/{profissional}`
- Visualiza horários disponíveis
- Agenda online em poucos cliques
- Confirmação automática via WhatsApp

### 3. Painel do Profissional (Mobile-First)
- Lista de agendamentos do dia/semana
- Confirmar/Cancelar agendamentos
- Gerenciar horários de trabalho
- Bloquear horários específicos

### 4. Notificações WhatsApp
- Novo agendamento → notifica profissional
- 24h antes → lembra cliente
- Confirmação/cancelamento → notifica ambos

## 🛠️ Stack Tecnológica

- **Backend**: Laravel 11
- **Frontend**: Blade + Alpine.js + Tailwind CSS
- **Database**: MySQL/SQLite
- **WhatsApp**: Evolution API ou Baileys
- **Pagamentos**: Asaas (R$ 14,90/mês por profissional)

## 📦 Instalação

### Requisitos
- PHP 8.2+
- Composer
- MySQL ou SQLite

### Passos

1. Clone o repositório
```bash
git clone <repo-url>
cd agendapro
```

2. Instale as dependências
```bash
composer install
```

3. Configure o ambiente
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure o banco de dados no `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agendapro
DB_USERNAME=root
DB_PASSWORD=
```

5. Execute as migrations
```bash
php artisan migrate
```

6. Configure as APIs externas no `.env`
```env
# WhatsApp API (Evolution API ou similar)
WHATSAPP_API_URL=http://localhost:8080
WHATSAPP_API_KEY=your_api_key

# Asaas (Pagamentos)
ASAAS_API_URL=https://sandbox.asaas.com/api/v3
ASAAS_API_KEY=your_api_key
```

7. Inicie o servidor
```bash
php artisan serve
```

8. Configure a queue para notificações (em outro terminal)
```bash
php artisan queue:work
```

## 📱 Estrutura do Banco de Dados

### professionals
- Dados do profissional
- Slug único para URL
- WhatsApp para contato
- Horários de trabalho
- Status de assinatura

### appointments
- Agendamentos dos clientes
- Data, horário e duração
- Status (pending, confirmed, cancelled, completed)
- Informações do cliente

### time_blocks
- Bloqueios de horário
- Para almoço, pausas, etc.

### subscriptions
- Controle de assinaturas
- Integração com Asaas
- Status de pagamento

## 🔄 Fluxo de Uso

### Cliente
1. Acessa link do profissional
2. Visualiza horários disponíveis
3. Escolhe data/hora
4. Preenche nome e WhatsApp
5. Recebe confirmação no WhatsApp

### Profissional
1. Recebe notificação de novo agendamento
2. Confirma ou cancela pelo dashboard
3. Gerencia horários de trabalho
4. Visualiza agenda do dia/semana

## 🚀 Roadmap

### Fase 1: MVP (Atual)
- [x] Setup Laravel básico
- [x] Estrutura de banco de dados
- [x] Models e relacionamentos
- [x] Sistema de WhatsApp
- [x] Agenda pública
- [x] Dashboard do profissional
- [x] Notificações WhatsApp
- [ ] Sistema de autenticação
- [ ] Integração Asaas

### Fase 2: Refinamento
- [ ] Múltiplos serviços com preços
- [ ] Upload de fotos (portfolio)
- [ ] Melhorias UX
- [ ] Analytics básico
- [ ] Testes automatizados

### Fase 3: Crescimento
- [ ] Pagamento PIX para clientes
- [ ] Sistema de avaliações
- [ ] Integrações (Instagram, Google)
- [ ] Plano para espaços (múltiplos profissionais)
- [ ] Programa de afiliados

## 💰 Modelo de Negócio

### Plano Único
- **R$ 14,90/mês** por profissional
- 14 dias grátis (sem cartão)
- Agendamentos ilimitados
- Notificações WhatsApp incluídas
- Link personalizado

## 🔧 Comandos Úteis

### Enviar lembretes de agendamento (configurar no cron)
```bash
php artisan appointments:send-reminders
```

### Configurar cron (Linux)
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## 📝 Licença

Este projeto é proprietário.

## 👨‍💻 Desenvolvimento

Desenvolvido com foco em simplicidade e usabilidade para profissionais autônomos.

---

**AgendaPro** - Seu horário, sua agenda, sua liberdade! 💈✂️💅
