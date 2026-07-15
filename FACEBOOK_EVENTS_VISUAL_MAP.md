# 🗺️ Mapa Visual dos Eventos Facebook Pixel - ZapBy

## 📍 Fluxo do Usuário e Eventos

```
┌─────────────────────────────────────────────────────────────────┐
│                   1. LANDING PAGE (/)                           │
│                   welcome.blade.php                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  [EVENTO] PageView ✅ (automático)                              │
│                                                                 │
│  ┌─────────────────────────────────────┐                       │
│  │  "Começar Grátis - 7 Dias"          │                       │
│  │  [EVENTO] Lead ✅                    │  ← Hero Section       │
│  └─────────────────────────────────────┘                       │
│                                                                 │
│  [Seção de Recursos]                                           │
│  [Seção Como Funciona]                                         │
│  [Seção de Preços]                                             │
│                                                                 │
│  ┌─────────────────────────────────────┐                       │
│  │  "Começar Teste Grátis"             │                       │
│  │  [EVENTO] Lead ✅                    │  ← Pricing Section    │
│  └─────────────────────────────────────┘                       │
│                                                                 │
│  ┌─────────────────────────────────────┐                       │
│  │  "Começar Agora Grátis"             │                       │
│  │  [EVENTO] Lead ✅                    │  ← Footer CTA         │
│  └─────────────────────────────────────┘                       │
│                                                                 │
└────────────────────────┬────────────────────────────────────────┘
                         │ Clique em qualquer CTA
                         ↓
┌─────────────────────────────────────────────────────────────────┐
│                2. PÁGINA DE REGISTRO                            │
│                /professional/register                           │
│                professional-register.blade.php                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  [EVENTO] PageView ✅ (automático)                              │
│  [EVENTO] Lead ✅ (chegou na página de registro)               │
│                                                                 │
│  ┌─────────────────────────────────────┐                       │
│  │  📝 Formulário de Cadastro          │                       │
│  │                                     │                       │
│  │  • Nome Completo                    │                       │
│  │  • CPF                              │                       │
│  │  • WhatsApp                         │                       │
│  │  • Profissão                        │                       │
│  │  • Senha                            │                       │
│  │  • Confirmar Senha                  │                       │
│  └─────────────────────────────────────┘                       │
│                                                                 │
│  ┌─────────────────────────────────────┐                       │
│  │  "Continuar"                        │                       │
│  │  [EVENTO] CompleteRegistration ✅    │  ← Form Submit        │
│  └─────────────────────────────────────┘                       │
│                                                                 │
└────────────────────────┬────────────────────────────────────────┘
                         │ Registro bem-sucedido
                         ↓
┌─────────────────────────────────────────────────────────────────┐
│                3. PAINEL PROFISSIONAL                           │
│                /professional/dashboard                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  [EVENTO] PageView ✅ (automático)                              │
│                                                                 │
│  🎉 Período de teste ativo (7 dias)                            │
│                                                                 │
│  [Menu: Painel | Agendamentos | Bloqueios |                   │
│         Configurações | Assinatura]                            │
│                                                                 │
└────────────────────────┬────────────────────────────────────────┘
                         │ Clique em "Assinatura"
                         ↓
┌─────────────────────────────────────────────────────────────────┐
│                4. PÁGINA DE ASSINATURA                          │
│                /professional/subscription                       │
│                subscription.blade.php                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  [EVENTO] PageView ✅ (automático)                              │
│  [EVENTO] ViewContent ✅ (visualizou assinatura)               │
│                                                                 │
│  ┌─────────────────────────────────────┐                       │
│  │  📊 Status da Assinatura            │                       │
│  │                                     │                       │
│  │  🎉 Período de Teste Ativo          │                       │
│  │  Válido até: DD/MM/YYYY             │                       │
│  │                                     │                       │
│  │  Após o teste:                      │                       │
│  │  💰 R$ 19,90/mês                    │                       │
│  └─────────────────────────────────────┘                       │
│                                                                 │
│  ┌─────────────────────────────────────┐                       │
│  │  "💳 Ativar Assinatura"             │                       │
│  │  [EVENTO] InitiateCheckout ✅        │  ← Clique para ativar │
│  └─────────────────────────────────────┘                       │
│                                                                 │
└────────────────────────┬────────────────────────────────────────┘
                         │ Pagamento iniciado
                         ↓
┌─────────────────────────────────────────────────────────────────┐
│                5. GATEWAY DE PAGAMENTO (ASAAS)                  │
│                Página externa                                   │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  🔄 Processando pagamento...                                   │
│                                                                 │
│  [Escolha: PIX, Boleto ou Cartão]                             │
│                                                                 │
└────────────────────────┬────────────────────────────────────────┘
                         │ Pagamento confirmado
                         ↓
┌─────────────────────────────────────────────────────────────────┐
│                6. WEBHOOK (BACKEND)                             │
│                AsaasWebhookController.php                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ⚡ Asaas envia notificação de pagamento confirmado            │
│                                                                 │
│  [EVENTO] Subscribe ✅ (via Conversions API*)                  │
│      ↑                                                          │
│      └─ Server-side, mais confiável!                           │
│                                                                 │
│  * Requer implementação do FacebookConversionsApiService       │
│                                                                 │
└────────────────────────┬────────────────────────────────────────┘
                         │ Assinatura ativada
                         ↓
┌─────────────────────────────────────────────────────────────────┐
│                7. CONFIRMAÇÃO                                   │
│                /professional/subscription                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ✅ Assinatura Ativa                                           │
│                                                                 │
│  💰 Valor Mensal: R$ 19,90                                     │
│  📅 Próximo Vencimento: DD/MM/YYYY                             │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 📊 Resumo de Eventos por Página

| Página | Arquivo | Eventos | Quando Disparam |
|--------|---------|---------|-----------------|
| **Landing** | `welcome.blade.php` | `PageView`<br>`Lead` (3x) | Carrega página<br>Clique em CTAs |
| **Registro** | `professional-register.blade.php` | `PageView`<br>`Lead`<br>`CompleteRegistration` | Carrega página<br>Carrega página<br>Submit formulário |
| **Dashboard** | `dashboard.blade.php` | `PageView` | Carrega página |
| **Assinatura** | `subscription.blade.php` | `PageView`<br>`ViewContent`<br>`InitiateCheckout` | Carrega página<br>Carrega página<br>Clique em ativar |
| **Webhook** | `AsaasWebhookController.php` | `Subscribe`* | Pagamento confirmado |

\* Requer implementação do `FacebookConversionsApiService`

---

## 🎯 Eventos por Objetivo de Campanha

### Para Campanhas de Tráfego
```
🎯 Objetivo: Awareness (Reconhecimento)
📊 Métrica: PageView
💡 Uso: Entender volume de visitantes
```

### Para Campanhas de Engajamento
```
🎯 Objetivo: Consideration (Consideração)
📊 Métrica: Lead
💡 Uso: Pessoas que clicaram em "Começar Agora"
```

### Para Campanhas de Conversão - Cadastro
```
🎯 Objetivo: Conversion (Conversão)
📊 Métrica: CompleteRegistration
💡 Uso: Otimizar para pessoas que completam cadastro
💰 KPI: Custo por Registro
```

### Para Campanhas de Conversão - Assinatura
```
🎯 Objetivo: Conversion (Conversão)
📊 Métrica: Subscribe
💡 Uso: Otimizar para pessoas que assinam (objetivo final)
💰 KPI: Custo por Assinante, ROAS
```

### Para Remarketing
```
🎯 Objetivo: Retargeting
📊 Públicos:
   - ViewContent (visualizou assinatura)
   - InitiateCheckout (quase assinou)
   - CompleteRegistration + NÃO Subscribe
💡 Uso: Recuperar pessoas que abandonaram o funil
```

---

## 📈 Taxa de Conversão Esperada (Benchmarks)

```
┌─────────────────────────────────────────────────────────┐
│  100% → PageView (Landing Page)                         │
│   ↓                                                      │
│  10-20% → Lead (Clique em CTA)                         │
│   ↓                                                      │
│  30-50% → CompleteRegistration                          │
│   ↓                                                      │
│  100% → ViewContent (trial ativo)                       │
│   ↓                                                      │
│  40-60% → InitiateCheckout                              │
│   ↓                                                      │
│  20-30% → Subscribe                                     │
│                                                          │
│  Conversão Final: 1-3% visitantes → assinantes         │
└─────────────────────────────────────────────────────────┘
```

**Exemplo Prático:**
```
1.000 Visitantes
   ↓ 15% clicam em CTA
150 Leads
   ↓ 40% completam cadastro
60 Registros
   ↓ 50% iniciam checkout (trial acaba)
30 Checkouts
   ↓ 25% confirmam assinatura
7-8 Assinantes

Taxa de Conversão: 0,7-0,8%
```

---

## 🔍 Como Identificar Gargalos

### Gargalo 1: PageView → Lead (baixo)
**Sintoma**: Pouco clique nos CTAs
**Soluções**:
- Melhorar copy dos botões
- Posicionar CTAs mais visíveis
- Adicionar urgência/escassez
- Testar cores diferentes

### Gargalo 2: Lead → CompleteRegistration (baixo)
**Sintoma**: Muitos cliques mas poucos registros
**Soluções**:
- Simplificar formulário
- Remover campos desnecessários
- Adicionar trust signals
- Melhorar UX mobile

### Gargalo 3: ViewContent → InitiateCheckout (baixo)
**Sintoma**: Trial acaba mas não ativa assinatura
**Soluções**:
- Enviar emails de nurturing durante trial
- Adicionar benefícios exclusivos
- Oferecer desconto no primeiro mês
- Remarketing agressivo

### Gargalo 4: InitiateCheckout → Subscribe (baixo)
**Sintoma**: Inicia mas não completa pagamento
**Soluções**:
- Facilitar checkout
- Oferecer mais métodos de pagamento
- Adicionar garantia de reembolso
- Follow-up via WhatsApp

---

## 🎨 Cores dos Eventos no Pixel Helper

Quando você usar o Facebook Pixel Helper, verá:

- 🟢 **Verde**: Evento disparou corretamente
- 🟡 **Amarelo**: Aviso (não bloqueia, mas revise)
- 🔴 **Vermelho**: Erro (evento não foi enviado)
- 🔵 **Azul**: Informações adicionais

---

## 📞 Quick Reference

**Ver eventos em tempo real:**
https://business.facebook.com/events_manager2/ → Test Events

**Testar localmente:**
http://localhost/test-facebook-events.html

**Documentação completa:**
- Setup: `FACEBOOK_PIXEL_SETUP.md`
- Eventos: `FACEBOOK_EVENTS_TRACKING.md`
- Resumo: `FACEBOOK_PIXEL_IMPLEMENTATION_SUMMARY.md`

---

**Última atualização:** 24 de abril de 2026
