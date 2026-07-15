# Facebook Pixel - Eventos Rastreáveis Configurados

## 📊 Visão Geral

Este documento descreve todos os eventos do Facebook Pixel configurados no sistema ZapBy para rastreamento de conversões e comportamento do usuário.

## 🔑 Configuração

O ID do Facebook Pixel está configurado na variável de ambiente:
```
FACEBOOK_PIXEL_ID=2150463045748997
```

Para alterar o Pixel ID, edite o arquivo `.env`:
```bash
FACEBOOK_PIXEL_ID=seu_pixel_id_aqui
```

## 📍 Eventos Configurados

### 1. **PageView** (Padrão)
**Localização**: Todas as páginas (automático)
**Descrição**: Rastreia todas as visualizações de página.

---

### 2. **Lead** - Demonstração de Interesse
**Localizações**:
- Página inicial (`welcome.blade.php`):
  - Botão "Começar Grátis - 7 Dias" (Hero Section)
  - Botão "Começar Teste Grátis" (Seção de Preços)
  - Botão "Começar Agora Grátis" (CTA Final)
- Página de registro (`professional-register.blade.php`):
  - Ao carregar a página

**Código**:
```javascript
fbq('track', 'Lead', {
    content_name: 'Hero CTA - Começar Grátis'
});
```

**Objetivo**: Rastrear quando um usuário demonstra interesse em testar o produto.

---

### 3. **CompleteRegistration** - Cadastro Concluído
**Localização**: Página de registro (`professional-register.blade.php`)
**Trigger**: Ao submeter o formulário de cadastro

**Código**:
```javascript
fbq('track', 'CompleteRegistration', {
    content_name: 'Professional Registration'
});
```

**Objetivo**: Rastrear quando um usuário completa o registro no sistema.

---

### 4. **ViewContent** - Visualização de Conteúdo
**Localização**: Página de assinatura (`subscription.blade.php`)
**Trigger**: Ao carregar a página

**Código**:
```javascript
fbq('track', 'ViewContent', {
    content_name: 'Subscription Page',
    content_category: 'Subscription'
});
```

**Objetivo**: Rastrear quando um usuário acessa a página de gerenciamento de assinatura.

---

### 5. **InitiateCheckout** - Início do Checkout
**Localização**: Página de assinatura (`subscription.blade.php`)
**Trigger**: Ao clicar no botão "Ativar Assinatura"

**Código**:
```javascript
fbq('track', 'InitiateCheckout', {
    content_name: 'Subscription Activation',
    value: 19.90,
    currency: 'BRL'
});
```

**Objetivo**: Rastrear quando um usuário inicia o processo de assinatura paga.

---

### 6. **Subscribe** - Assinatura Confirmada (Backend)
**Localização**: Webhook do Asaas (`AsaasWebhookController.php`)
**Trigger**: Quando o pagamento da assinatura é confirmado

**Código** (a ser implementado no controller):
```php
// Disparar evento Facebook Conversions API quando assinatura for confirmada
// Requer instalação do SDK do Facebook
```

**Objetivo**: Rastrear conversões de assinatura confirmadas via servidor (mais confiável que client-side).

---

## 📈 Funil de Conversão

```
1. PageView (Landing Page)
   ↓
2. Lead (Clique em CTA)
   ↓
3. Lead (Página de Registro)
   ↓
4. CompleteRegistration (Cadastro Concluído)
   ↓
5. ViewContent (Visualiza Assinatura)
   ↓
6. InitiateCheckout (Clica em Ativar)
   ↓
7. Subscribe (Assinatura Confirmada)
```

## 🔍 Como Verificar os Eventos

### No Facebook Pixel Helper (Extensão Chrome)
1. Instale o [Facebook Pixel Helper](https://chrome.google.com/webstore/detail/facebook-pixel-helper/fdgfkebogiimcoedlicjlajpkdmockpc)
2. Navegue pelas páginas do site
3. Clique no ícone da extensão para ver os eventos disparados

### No Gerenciador de Eventos do Facebook
1. Acesse [Facebook Events Manager](https://business.facebook.com/events_manager2/)
2. Selecione seu Pixel
3. Vá em "Test Events" para ver eventos em tempo real
4. Vá em "Overview" para ver estatísticas agregadas

### Testando Localmente
```bash
# Abra o console do navegador (F12)
# Os eventos são disparados via JavaScript
# Você verá confirmações no console se o Pixel estiver instalado corretamente
```

## 🎯 Otimização de Campanhas

### Eventos Recomendados para Públicos Personalizados:
- **Lead**: Criar público de pessoas interessadas
- **CompleteRegistration**: Criar público de cadastrados (excluir de campanhas de aquisição)
- **InitiateCheckout**: Criar público para remarketing (pessoas que quase assinaram)
- **Subscribe**: Criar público de assinantes (excluir de todas as campanhas)

### Eventos Recomendados para Conversões:
- **Conversão Principal**: Subscribe
- **Conversões Secundárias**: CompleteRegistration, InitiateCheckout
- **Micro-conversões**: Lead

## 🔐 Privacidade e LGPD

Os eventos do Facebook Pixel seguem as diretrizes de privacidade:
- Não coletamos dados sensíveis
- Não enviamos PII (Personally Identifiable Information) nos parâmetros dos eventos
- Usuários podem optar por não participar usando extensões de bloqueio de rastreamento

## 📝 Manutenção

### Adicionar Novo Evento:
1. Identifique a ação a ser rastreada
2. Adicione o código do evento na página apropriada:
```javascript
fbq('track', 'NomeDoEvento', {
    parametro1: 'valor1',
    parametro2: 'valor2'
});
```
3. Teste usando o Facebook Pixel Helper
4. Documente neste arquivo

### Eventos Padrão do Facebook:
- `ViewContent` - Visualização de conteúdo
- `Search` - Busca
- `AddToCart` - Adicionar ao carrinho
- `AddToWishlist` - Adicionar aos favoritos
- `InitiateCheckout` - Iniciar checkout
- `AddPaymentInfo` - Adicionar informação de pagamento
- `Purchase` - Compra
- `Lead` - Lead/Interesse
- `CompleteRegistration` - Registro completo
- `Subscribe` - Assinatura

Para eventos customizados, use:
```javascript
fbq('trackCustom', 'MeuEventoCustomizado', {
    parametros: 'aqui'
});
```

## 🚀 Melhorias Futuras

### Conversions API (Server-Side)
Para rastreamento mais confiável (não bloqueado por ad blockers):
1. Instalar o SDK do Facebook para PHP
2. Configurar credenciais de acesso
3. Enviar eventos do servidor nos momentos críticos (assinatura confirmada, pagamento recebido)

### Exemplo de implementação:
```php
use FacebookAds\Api;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\UserData;

// Enviar evento Subscribe via servidor
$event = (new Event())
    ->setEventName('Subscribe')
    ->setEventTime(time())
    ->setUserData($userData)
    ->setCustomData($customData);

$request = (new EventRequest(env('FACEBOOK_PIXEL_ID')))
    ->setEvents([$event]);

$response = $request->execute();
```

## 📞 Suporte

Para dúvidas sobre eventos do Facebook Pixel:
- [Documentação Oficial do Facebook Pixel](https://developers.facebook.com/docs/facebook-pixel)
- [Facebook Business Help Center](https://www.facebook.com/business/help)

---

**Última atualização**: 24 de abril de 2026
**Versão**: 1.0
