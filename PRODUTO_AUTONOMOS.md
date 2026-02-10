# Produto para Profissionais Autônomos de Beleza

## Conceito

Sistema de agendamento ultra-simplificado para **profissionais de beleza autônomos** que alugam cadeira/espaço em salões. Foco em simplicidade, acessibilidade via WhatsApp e usabilidade para público com baixa familiaridade tecnológica.

## Público-Alvo

### Persona Principal: Profissional Autônomo
- **Quem é**: Cabeleireiro, manicure, barbeiro, esteticista que aluga espaço
- **Necessidades**: Sistema simples para organizar agenda e receber agendamentos
- **Características**: 
  - Pode não ter email ativo
  - Usa WhatsApp como principal canal de comunicação
  - Pouca familiaridade com sistemas complexos
  - Precisa de resultado imediato sem curva de aprendizado

### Persona Secundária: Dono do Espaço (Opcional)
- **Quem é**: Proprietário que aluga cadeiras para múltiplos profissionais
- **Necessidades**: Visão básica de ocupação, controle simples de inquilinos
- **Diferencial**: Não precisa de relatórios financeiros detalhados

## Nome Sugerido

**"AgendaSimples"** ou **"MeuHorário"** ou **"AgendaPro"**

## Diferenças Fundamentais vs Pagby

| Aspecto                 | Pagby (Atual)                 | Produto Novo                |
|-------------------------|-------------------------------|-----------------------------|
| **Foco**                | Gestão completa de salão      | Agenda individual           |
| **Usuário principal**   | Proprietário                  | Profissional autônomo       |
| **Complexidade**        | Média/Alta                    | Mínima                      |
| **Cadastro**            | Email obrigatório             | WhatsApp (sem email)        |
| **Pagamentos**          | Sistema de comissões          | Opcional/Simples            |
| **Relatórios**          | Completos                     | Básicos                     |
| **Preço**               | R$ 29,90-59,90/funcionário    | R$ 14,90-19,90/profissional |
| **Multi-tenant**        | Por salão                     | Por profissional            |

## Arquitetura Técnica

### Opção 1: Fork Simplificado do Pagby
```
Nova aplicação Laravel baseada em Pagby mas removendo:
- Sistema de comissões
- Múltiplas filiais
- Relatórios financeiros complexos
- Gestão de estoque
- Sistema de caixa
```

### Opção 2: Micro-SaaS do Zero
```
Nova aplicação minimalista:
- Laravel 11 (slim)
- Livewire para interatividade mínima
- WhatsApp API como canal principal
- Banco de dados simples
- Sem multi-tenancy complexo (1 DB, users separados)
```

**Recomendação**: Opção 2 - Começar do zero permite criar algo realmente simples sem carregar complexidade desnecessária.

## Features Essenciais (MVP)

### ✅ Core Features
1. **Cadastro via WhatsApp**
   - Link único: `wa.me/5511999999999?text=Quero%20criar%20minha%20agenda`
   - Bot coleta: Nome, WhatsApp, Serviços que oferece
   - Gera link de agenda: `agendasimples.com.br/joao`

2. **Agenda Pública Simples**
   - Cliente acessa: `agendasimples.com.br/joao`
   - Vê horários disponíveis da semana
   - Clica no horário → Preenche nome e WhatsApp
   - Confirmação automática via WhatsApp

3. **Painel do Profissional (Mobile-First)**
   - Lista de agendamentos do dia/semana
   - Botão "Confirmar" / "Cancelar"
   - Histórico últimos 30 dias
   - Notificações WhatsApp

4. **Gestão de Horários**
   - Definir horário de trabalho (segunda a sábado)
   - Bloquear horários específicos
   - Duração padrão dos serviços

5. **Notificações WhatsApp**
   - Novo agendamento → notifica profissional
   - 24h antes → lembra cliente
   - Confirmação/cancelamento → notifica ambos

### ⚠️ Features Opcionais (Fase 2)
- Múltiplos serviços com preços
- Pagamento online (PIX via Asaas)
- Fotos do trabalho (portfolio)
- Avaliações de clientes
- Lista de espera

### ❌ Features Removidas (vs Pagby)
- Sistema de comissões
- Múltiplas filiais
- Relatórios financeiros detalhados
- Gestão de funcionários
- Sistema de caixa
- Controle de estoque
- Níveis de acesso complexos

## Modelo de Negócio

### Plano Único Simplificado
```
R$ 14,90/mês por profissional
- 14 dias grátis (sem cartão)
- Agendamentos ilimitados
- Notificações WhatsApp incluídas
- Link personalizado
- Sem taxa de setup
```

### Plano Espaço (Opcional)
```
R$ 49,90/mês para donos de espaço
- Até 5 profissionais
- R$ 8,90 por profissional adicional
- Painel de ocupação do espaço
- Relatório simples de uso
```

## Fluxo de Onboarding

### 1. Descoberta (WhatsApp)
```
Cliente: [Clica em anúncio/indicação]
Bot: "Oi! Sou o AgendaSimples 👋
     Crie sua agenda profissional em 2 minutos.
     Qual seu nome?"

Cliente: "João Silva"

Bot: "Legal, João! Você é:
     1️⃣ Cabeleireiro
     2️⃣ Manicure
     3️⃣ Barbeiro
     4️⃣ Esteticista
     5️⃣ Outro"

Cliente: "1"

Bot: "Perfeito! Quanto tempo dura um atendimento em média?
     1️⃣ 30 minutos
     2️⃣ 1 hora
     3️⃣ 1h30
     4️⃣ 2 horas"

Cliente: "2"

Bot: "Pronto! Sua agenda está no ar 🎉
     Link: agendasimples.com.br/joaosilva
     
     Clique aqui para configurar seus horários:
     [Link para painel]
     
     Sem cartão, grátis por 14 dias!"
```

### 2. Configuração Inicial (Web Mobile)
```
Tela 1: "Quando você trabalha?"
[Checkboxes: Seg, Ter, Qua, Qui, Sex, Sab, Dom]
[Horário: 08:00 às 19:00]

Tela 2: "Pronto! Compartilhe seu link"
[Botão: Copiar Link]
[Botão: Compartilhar no WhatsApp]

[Preview da agenda pública]
```

### 3. Primeiro Agendamento
```
Cliente agenda → WhatsApp do profissional recebe:
"🎉 Novo agendamento!
Maria - Corte feminino
Amanhã às 14:00
WhatsApp: (11) 98765-4321

[Confirmar] [Cancelar]"
```

## Stack Tecnológica Recomendada

### Backend Minimalista
```
- Laravel 11 (sem multi-tenancy complexo)
- MySQL (1 database compartilhado)
- Jobs para notificações WhatsApp
- API REST simples
```

### Frontend Ultra-Leve
```
- Blade templates (sem Livewire para simplificar)
- Alpine.js para interatividade mínima
- Tailwind CSS
- Mobile-first design
```

### Integrações Essenciais
```
- WhatsApp Business API (Evolution API ou Baileys)
- Asaas (pagamento das assinaturas + opcional PIX para clientes)
- Sem complexidade adicional
```

### Hospedagem Econômica
```
- Servidor compartilhado (Hostinger, Locaweb)
- Ou VPS básico (R$ 30/mês)
- Sem necessidade de infraestrutura complexa
```

## Estrutura de Banco de Dados Simplificada

```sql
-- Tabela principal: profissionais (substitui tenants)
professionals
- id
- name
- slug (joaosilva)
- whatsapp
- profession
- default_duration (minutos)
- working_days (JSON: [1,2,3,4,5,6])
- working_hours (JSON: {start: "08:00", end: "19:00"})
- subscription_status
- trial_ends_at
- created_at

-- Agendamentos simplificados
appointments
- id
- professional_id
- client_name
- client_whatsapp
- date
- time
- duration
- status (pending, confirmed, cancelled, completed)
- notes
- created_at

-- Bloqueios de horário
time_blocks
- id
- professional_id
- date
- start_time
- end_time
- reason

-- Pagamentos (assinatura)
subscriptions
- id
- professional_id
- asaas_customer_id
- asaas_subscription_id
- status
- next_due_date
```

## Roadmap de Desenvolvimento

### Fase 1: MVP (4-6 semanas)
- [ ] Setup Laravel básico
- [ ] Cadastro via WhatsApp (bot simples)
- [ ] Agenda pública (view única)
- [ ] Painel profissional mobile
- [ ] Notificações WhatsApp
- [ ] Sistema de assinatura (Asaas)

### Fase 2: Refinamento (2-3 semanas)
- [ ] Múltiplos serviços
- [ ] Upload de fotos
- [ ] Melhorias UX
- [ ] Analytics básico
- [ ] Testes com usuários reais

### Fase 3: Crescimento (ongoing)
- [ ] Pagamento PIX para clientes
- [ ] Sistema de avaliações
- [ ] Integrações (Instagram, Google)
- [ ] Plano para espaços
- [ ] Programa de afiliados

## Estratégia de Lançamento

### Pré-Lançamento
1. Landing page simples
2. Grupo WhatsApp com 20-30 profissionais beta
3. Coleta de feedback intenso
4. Ajustes rápidos

### Lançamento Soft
1. Liberar para primeiros 100 profissionais
2. R$ 9,90/mês (early adopter price)
3. Incentivo para trazer outros profissionais

### Crescimento
1. Programa de indicação (1 mês grátis)
2. Parcerias com distribuidoras de produtos
3. Presença em eventos do setor
4. Marketing boca-a-boca (produto que vende sozinho)

## Projeção Financeira Simplificada

### Custos Mensais (Estimativa)
```
Hospedagem VPS: R$ 50
WhatsApp API: R$ 100 (até 1000 profissionais)
Asaas (gateway): 1,99% + R$ 0,49/transação
Domínio: R$ 3
CDN/Storage: R$ 20
Total: ~R$ 200/mês
```

### Breakeven
```
200 ÷ 14,90 = ~14 profissionais pagantes
Target inicial: 50 profissionais em 3 meses
```

### Projeção 1 Ano
```
Mês 3: 50 profissionais = R$ 745/mês
Mês 6: 200 profissionais = R$ 2.980/mês
Mês 12: 500 profissionais = R$ 7.450/mês
```

## Vantagens Competitivas

1. **Mais simples que qualquer concorrente**
   - Agende.app: muito complexo
   - Booksy: interface confusa
   - Agenda Fácil: caro

2. **Cadastro via WhatsApp**
   - Zero fricção
   - Sem email necessário
   - Familiar ao público

3. **Preço acessível**
   - R$ 14,90 vs R$ 30-50 da concorrência
   - Sem taxa de setup

4. **Mobile-first real**
   - Não é desktop adaptado
   - Pensado para uso no celular

5. **Foco no profissional autônomo**
   - Não tenta ser tudo para todos
   - Resolve 1 problema muito bem

## Riscos e Mitigações

| Risco | Mitigação |
|-------|-----------|
| WhatsApp banir bot | Usar Evolution API (self-hosted) + múltiplos números |
| Baixa adoção inicial | Teste com grupo pequeno antes de investir |
| Profissionais não pagam | Trial sem cartão, mas lembrar 3x antes de expirar |
| Sistema muito simples | Adicionar features conforme demanda real |
| Concorrência copia | Velocidade de execução + foco no nicho |

## Próximos Passos Imediatos

1. **Validação** (Esta semana)
   - [ ] Criar landing page simples
   - [ ] Conversar com 10 profissionais autônomos
   - [ ] Entender dores reais

2. **Prototipagem** (Próximas 2 semanas)
   - [ ] Mockup do fluxo completo (Figma)
   - [ ] Bot WhatsApp funcional (teste)
   - [ ] Agenda pública (HTML estático)

3. **Decisão Go/No-Go**
   - Se 5+ profissionais demonstrarem interesse real
   - Se validar que R$ 14,90 é viável
   - Se confirmar simplicidade como diferencial

## Conclusão

Este produto tem potencial de ser **mais bem-sucedido que o Pagby** para o mercado de massa, porque:

- ✅ Resolve problema específico muito bem
- ✅ Público maior (todo profissional autônomo)
- ✅ Menor fricção de adoção
- ✅ Preço acessível
- ✅ Modelo escalável

**Recomendação**: Iniciar validação imediatamente com grupo pequeno antes de desenvolver.
