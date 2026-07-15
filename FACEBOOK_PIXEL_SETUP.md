# 🎯 Guia Rápido: Como Configurar o Facebook Pixel

## 📋 Pré-requisitos
- Conta no Facebook Business Manager
- Acesso administrativo ao pixel do Facebook
- Acesso ao código-fonte do projeto ZapBy

## 🚀 Passo a Passo

### 1. Obter o ID do Pixel no Facebook

1. Acesse [Facebook Business Manager](https://business.facebook.com/)
2. Vá em **Configurações de Negócios** (ícone de engrenagem)
3. No menu lateral, clique em **Fontes de Dados** → **Pixels**
4. Selecione seu pixel ou crie um novo
5. Copie o **ID do Pixel** (número de 15-16 dígitos)

### 2. Configurar no Projeto ZapBy

Edite o arquivo `.env` na raiz do projeto:

```bash
# Adicione ou edite esta linha:
FACEBOOK_PIXEL_ID=seu_id_do_pixel_aqui
```

**Exemplo**:
```bash
FACEBOOK_PIXEL_ID=2150463045748997
```

### 3. Reiniciar o Servidor (se necessário)

```bash
# Laravel cache clear
php artisan config:clear
php artisan cache:clear

# Se estiver usando queue
php artisan queue:restart
```

### 4. Verificar se Está Funcionando

#### Método 1: Facebook Pixel Helper (Recomendado)
1. Instale a extensão [Facebook Pixel Helper](https://chrome.google.com/webstore/detail/facebook-pixel-helper/fdgfkebogiimcoedlicjlajpkdmockpc) no Chrome
2. Acesse seu site (landing page)
3. Clique no ícone da extensão
4. Você deve ver:
   - ✅ **PageView** ao carregar a página
   - ✅ **Lead** ao clicar em "Começar Agora"
5. Acesse a página de registro
6. Você deve ver:
   - ✅ **Lead** ao carregar
   - ✅ **CompleteRegistration** ao submeter o formulário

#### Método 2: Console do Navegador (F12)
```javascript
// Digite no console:
window.fbq

// Se retornar uma função, o pixel está carregado corretamente
```

#### Método 3: Events Manager do Facebook
1. Acesse [Events Manager](https://business.facebook.com/events_manager2/)
2. Selecione seu pixel
3. Clique em **Test Events** no menu lateral
4. Navegue pelo site em outra aba
5. Você verá os eventos chegando em tempo real 🎉

### 5. Configurar Domínio Verificado (Importante para iOS 14+)

Para evitar limitações de rastreamento:

1. No **Facebook Business Manager**, vá em:
   - **Configurações de Negócios** → **Segurança da Marca** → **Domínios**
2. Clique em **Adicionar** e insira seu domínio (ex: `zapby.com.br`)
3. Siga as instruções para verificação:
   - **Opção 1**: Adicionar meta tag HTML no `<head>`
   - **Opção 2**: Upload de arquivo HTML
   - **Opção 3**: Verificação DNS (TXT record)
4. Após verificar, vá em **Gerenciador de Eventos**
5. Configure **Configurações de Agregação de Eventos**:
   - Prioridade 1: **Subscribe** (conversão principal)
   - Prioridade 2: **InitiateCheckout**
   - Prioridade 3: **CompleteRegistration**
   - Prioridade 4: **Lead**

## 🎯 Criar Campanhas de Conversão

### Configurar Evento de Conversão

1. No **Gerenciador de Anúncios**, crie uma nova campanha
2. Objetivo: **Conversões**
3. Em **Evento de Conversão**, selecione:
   - **Subscribe** (para campanhas de aquisição de assinantes)
   - **CompleteRegistration** (para campanhas de cadastro)
   - **Lead** (para gerar interesse)

### Criar Públicos Personalizados

1. Acesse **Gerenciador de Públicos**
2. Crie **Público Personalizado** → **Tráfego do Site**
3. Exemplos de públicos úteis:

#### Público: Visitantes da Landing Page
- **Condição**: PageView → URL contém `/welcome` ou `/`
- **Período**: Últimos 30 dias
- **Uso**: Remarketing geral

#### Público: Pessoas que Clicaram em CTA (Lead)
- **Condição**: Lead
- **Período**: Últimos 7 dias
- **Uso**: Remarketing agressivo (pessoas interessadas recentemente)

#### Público: Cadastrados mas Não Assinantes
- **Condição**: CompleteRegistration E NÃO Subscribe
- **Período**: Últimos 90 dias
- **Uso**: Oferecer benefícios/descontos para converter

#### Público: Iniciaram Checkout mas Não Assinaram
- **Condição**: InitiateCheckout E NÃO Subscribe
- **Período**: Últimos 7 dias
- **Uso**: Remarketing urgente (carrinho abandonado)

#### Público: Assinantes (Exclusão)
- **Condição**: Subscribe
- **Período**: Últimos 180 dias
- **Uso**: EXCLUIR de todas as campanhas de aquisição

### Criar Públicos Semelhantes (Lookalike)

1. Base: Público de **Assinantes** (Subscribe)
2. Porcentagem: 1% (mais preciso) a 5% (maior alcance)
3. País: Brasil
4. Use este público em campanhas de prospecção

## 📊 Métricas Importantes

### No Facebook Ads Manager

Acompanhe estas métricas:
- **Custo por Lead**: Quanto custa cada clique em "Começar Agora"
- **Custo por Registro** (CompleteRegistration): Quanto custa cada cadastro
- **Custo por Assinatura** (Subscribe): Quanto custa cada assinante
- **Taxa de Conversão**: % de leads que viram assinantes
- **LTV (Lifetime Value)**: Valor médio por assinante x tempo de retenção

### Exemplo de Análise
```
Lead → R$ 5,00
CompleteRegistration → R$ 15,00 (33% dos leads)
Subscribe → R$ 50,00 (33% dos cadastros)

Se LTV = R$ 100,00
ROI = (100 - 50) / 50 = 100% 🎉
```

## 🔧 Troubleshooting

### Problema: Eventos não aparecem no Events Manager

**Soluções**:
1. Verifique se `FACEBOOK_PIXEL_ID` está correto no `.env`
2. Limpe o cache: `php artisan config:clear`
3. Verifique se o domínio está em HTTPS
4. Desabilite ad blockers
5. Verifique no Console (F12) se há erros de JavaScript

### Problema: Pixel Helper mostra erro

**Soluções**:
1. Verifique se o ID do pixel está correto
2. Certifique-se de que o código está no `<head>` da página
3. Verifique se não há múltiplos pixels carregando

### Problema: Eventos duplicados

**Causa**: Código do pixel incluído duas vezes
**Solução**: Use apenas o componente `@include('partials.facebook-pixel')` uma vez por página

### Problema: Baixa taxa de correspondência (Match Rate)

**Causa**: Falta de dados do usuário enviados com os eventos
**Solução**: Implementar Facebook Conversions API (server-side tracking) com dados hasheados do usuário

## 🔐 LGPD e Privacidade

### Banner de Cookies (Recomendado)

Adicione um banner de consentimento:

```html
<!-- Exemplo simples -->
<div id="cookie-banner" class="fixed bottom-0 left-0 right-0 bg-gray-900 text-white p-4">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <p class="text-sm">
            Usamos cookies e pixels de rastreamento para melhorar sua experiência e personalizar anúncios.
        </p>
        <div class="flex gap-4">
            <button onclick="acceptCookies()" class="bg-green-600 px-4 py-2 rounded">
                Aceitar
            </button>
            <button onclick="rejectCookies()" class="text-gray-300">
                Recusar
            </button>
        </div>
    </div>
</div>

<script>
function acceptCookies() {
    // Carregar pixel apenas após consentimento
    localStorage.setItem('cookiesAccepted', 'true');
    document.getElementById('cookie-banner').remove();
}

function rejectCookies() {
    localStorage.setItem('cookiesAccepted', 'false');
    document.getElementById('cookie-banner').remove();
    // Desabilitar pixel
    fbq('consent', 'revoke');
}
</script>
```

## 📚 Recursos Úteis

- [Documentação Oficial Facebook Pixel](https://developers.facebook.com/docs/facebook-pixel)
- [Facebook Pixel Helper](https://chrome.google.com/webstore/detail/facebook-pixel-helper/fdgfkebogiimcoedlicjlajpkdmockpc)
- [Guia de Eventos Padrão](https://developers.facebook.com/docs/facebook-pixel/implementation/conversion-tracking)
- [Conversions API](https://developers.facebook.com/docs/marketing-api/conversions-api)
- [LGPD e Facebook Pixel](https://www.facebook.com/business/gdpr)

## 📞 Suporte

Se precisar de ajuda:
1. Verifique a documentação interna: `FACEBOOK_EVENTS_TRACKING.md`
2. Teste com Facebook Pixel Helper
3. Consulte o Events Manager para erros específicos

---

**Configuração criada em**: 24 de abril de 2026
**Versão do ZapBy**: 1.0
