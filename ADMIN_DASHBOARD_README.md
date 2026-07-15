# Dashboard Administrativo - Zapby

Este documento descreve como configurar e usar o Dashboard Administrativo do Zapby.

## 🚀 Instalação

### 1. Executar a Migration

Primeiro, execute a migration para adicionar o campo `is_admin` na tabela de profissionais:

```bash
php artisan migrate
```

Isso criará o campo `is_admin` (boolean) na tabela `professionals`.

### 2. Criar o Primeiro Administrador

Você pode criar o primeiro administrador de duas formas:

#### Opção A: Via Tinker (Recomendado)

```bash
php artisan tinker
```

Então, execute:

```php
$professional = \App\Models\Professional::where('whatsapp', 'SEU_WHATSAPP')->first();
$professional->is_admin = true;
$professional->save();
```

Substitua `SEU_WHATSAPP` pelo número de WhatsApp do profissional que será admin.

#### Opção B: Via SQL Direto

```sql
UPDATE professionals 
SET is_admin = 1 
WHERE whatsapp = 'SEU_WHATSAPP';
```

### 3. Acessar o Dashboard

Após configurar o administrador, você tem duas opções de acesso:

#### Opção 1: Login Automático (Recomendado)

Administradores são **automaticamente redirecionados** para o dashboard admin ao fazer login:

1. Acesse: `https://seu-dominio.com/professional/login`
2. Faça login com suas credenciais (CPF e senha)
3. Você será redirecionado automaticamente para `/admin/dashboard`

#### Opção 2: Acesso Direto

Se você já está autenticado como profissional:

- Acesse: `https://seu-dominio.com/admin` ou `https://seu-dominio.com/admin/dashboard`
- Clique no link "Admin" no menu do painel profissional

**Importante**: Apenas profissionais com `is_admin = true` podem acessar essas rotas. Usuários sem permissão receberão erro 403.

## 📊 Funcionalidades

### Dashboard Principal

O dashboard administrativo oferece:

#### Estatísticas Gerais
- **Total de Profissionais**: Número total de profissionais cadastrados
- **Em Trial**: Profissionais em período de teste ativo
- **Trial Expirado**: Profissionais com trial vencido
- **Assinaturas Ativas**: Total de assinaturas pagas ativas
- **Assinaturas Suspensas**: Assinaturas temporariamente suspensas
- **Assinaturas Canceladas**: Total de cancelamentos
- **Receita Mensal**: Soma de todos os planos ativos
- **Vencendo em 7 dias**: Assinaturas próximas ao vencimento

#### Filtros
- **Busca**: Por nome, WhatsApp ou profissão
- **Status**: Filtrar por status da assinatura
  - Todos
  - Em Trial
  - Trial Expirado
  - Ativo
  - Suspenso
  - Cancelado

#### Lista de Profissionais

Visualize todos os profissionais com:
- Nome e profissão
- Contato (WhatsApp e slug)
- Status atual da assinatura
- Valor do plano mensal
- Data de vencimento
- Data de cadastro
- Link para detalhes

### Página de Detalhes do Profissional

Ao clicar em "Detalhes" de um profissional, você verá:

#### Estatísticas do Profissional
- Total de agendamentos
- Agendamentos do mês atual
- Próximos agendamentos

#### Informações de Assinatura
- Status atual
- Valor mensal
- Próximo vencimento
- IDs do Asaas (customer e subscription)
- Formulário para alterar status manualmente

#### Informações Profissionais
- CPF
- Duração padrão dos atendimentos
- Serviços oferecidos
- Dias de trabalho
- Chave de acesso
- Status de verificação do WhatsApp
- Status do onboarding

#### Gerenciamento de Permissões
- Tornar outro profissional administrador
- Remover permissões de administrador

#### Gestão de Trial
- Estender período de trial (7, 15, 30, 60 ou 90 dias)
- Alterar status de assinatura manualmente
- Visualizar datas de vencimento e tempo restante

#### Últimos Agendamentos
- Lista dos 10 últimos agendamentos
- Informações do cliente
- Data, horário e status

## 🔒 Segurança

- O acesso ao dashboard é protegido pelo middleware `admin`
- Apenas profissionais com `is_admin = true` podem acessar
- Usuários não autorizados recebem erro 403

## 🎨 Interface

O dashboard utiliza:
- **TailwindCSS** para estilização
- **Font Awesome** para ícones
- Design responsivo (funciona em mobile e desktop)
- Cores roxas/púrpuras para distinguir da área de profissionais (azul/índigo)

## 📱 Navegação

Profissionais com permissão de admin têm acesso a ambas as áreas:

### Como Administrador

**Login automático:**
- Faça login em `/professional/login`
- Será redirecionado automaticamente para `/admin/dashboard`
- Veja estatísticas de todos os profissionais e assinaturas

**Navegação:**
- Link "Admin" sempre visível no menu (desktop e mobile)
- Link "Meu Painel" no dashboard admin leva ao painel profissional
- Pode alternar livremente entre as duas áreas

### Como Profissional

Administradores também podem acessar o painel profissional:
- Clique em "Meu Painel" no menu do dashboard admin
- Ou acesse diretamente `/professional/dashboard`
- Veja seus próprios agendamentos, configurações, etc.

### Estrutura de URLs

- `/admin` ou `/admin/dashboard` → Dashboard administrativo (requer `is_admin = true`)
- `/professional/dashboard` → Painel do profissional
- `/professional/login` → Login (redireciona admin para `/admin/dashboard`)
- `/professional/logout` → Sair (funciona para ambas as áreas)

## ⚙️ Ações Disponíveis

### Alterar Status de Assinatura
Você pode alterar manualmente o status de qualquer assinatura para:
- Trial
- Ativo
- Suspenso
- Cancelado

### Estender Período de Trial
Na página de detalhes do profissional, você pode estender o período de trial:

**Opções disponíveis:**
- 7 dias
- 15 dias (padrão)
- 30 dias
- 60 dias
- 90 dias

**Como funciona:**
- Se o trial ainda estiver ativo, o período é adicionado ao vencimento atual
- Se o trial já expirou, um novo período começa a partir de hoje
- A funcionalidade só aparece para profissionais em trial ou sem assinatura
- O status é automaticamente alterado para "trial" ao estender

**Casos de uso:**
- Dar mais tempo para profissionais que estão avaliando o sistema
- Recompensar profissionais por indicações
- Resolver problemas técnicos que impediram o uso adequado
- Permitir testes de novas funcionalidades

**Exemplo de uso:**
```
Trial atual: Vence em 22/04/2026 (daqui 2 dias)
Ação: Estender por 30 dias
Resultado: Novo vencimento será 22/05/2026 (daqui 32 dias)
```

**Importante:** 
- A extensão não afeta assinaturas pagas existentes
- O profissional recebe automaticamente o status "trial" ao estender
- Não há limite de quantas vezes você pode estender um trial

### Gerenciar Administradores
- Promover profissionais a administrador
- Remover permissões administrativas

## 🔍 Monitoramento

Use o dashboard para:
1. **Identificar problemas**: Trials expirados, assinaturas suspensas
2. **Acompanhar receita**: Valor total de receita mensal
3. **Prever vencimentos**: Ver quais assinaturas vencem em breve
4. **Suporte**: Acessar informações detalhadas de cada profissional
5. **Gestão de trials**: Estender períodos de teste conforme necessário

### Fluxo: Estender Trial de um Profissional

1. Acesse o dashboard admin (`/admin/dashboard`)
2. Localize o profissional (use filtros ou busca se necessário)
3. Clique em "Detalhes" 
4. Na seção "Informações de Assinatura", localize "Estender Trial"
5. Selecione o período desejado (7, 15, 30, 60 ou 90 dias)
6. Clique em "Estender"
7. ✅ Pronto! Você verá uma mensagem de confirmação com a nova data de vencimento

## 🚨 Troubleshooting

### Não consigo acessar o dashboard
- Verifique se você está autenticado (faça login em `/professional/login`)
- Confirme se seu usuário tem `is_admin = true` no banco de dados
- Limpe o cache: `php artisan cache:clear`
- Faça logout e login novamente para atualizar a sessão

### Erro 403 - Acesso Negado
- Seu usuário não tem permissão de admin (`is_admin = false` ou `NULL`)
- Execute o comando tinker novamente para setar `is_admin = true`
- Verifique se a migration foi executada corretamente

### Não sou redirecionado automaticamente para /admin após login
- Faça logout: acesse `/professional/logout` (ou clique em "Sair")
- Faça login novamente em `/professional/login`
- Se o problema persistir, limpe a sessão: `php artisan session:flush`

### Estatísticas não aparecem
- Verifique se há dados no banco
- Confirme que as migrations foram executadas
- Verifique os logs: `storage/logs/laravel.log`

## 📝 Notas

- É possível ter múltiplos administradores
- Administradores continuam tendo acesso ao painel de profissional normal
- As alterações de status são imediatas
- Não há log de auditoria implementado (considere adicionar se necessário)

## 🔄 Próximas Melhorias Sugeridas

- [ ] Log de auditoria das ações administrativas (registrar quem estendeu trials, quando, etc.)
- [ ] Exportação de dados (Excel/PDF)
- [ ] Gráficos de evolução temporal
- [ ] Notificações automáticas para vencimentos
- [ ] Dashboard de relatórios financeiros
- [ ] Sistema de tickets de suporte
- [ ] Extensão personalizada de trial (campo livre para dias)
- [ ] Histórico de extensões de trial por profissional
- [ ] Envio automático de email/WhatsApp ao estender trial
- [ ] Motivo/nota ao estender trial (campo opcional)
