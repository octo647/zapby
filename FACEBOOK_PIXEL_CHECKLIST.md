# ✅ Checklist de Ativação - Facebook Pixel

## 📋 Lista de Verificação Passo a Passo

### ✅ 1. Arquivos Criados/Modificados

#### Componentes e Views
- [x] `resources/views/partials/facebook-pixel.blade.php` - Componente do pixel
- [x] `resources/views/welcome.blade.php` - Landing com eventos Lead
- [x] `resources/views/auth/professional-register.blade.php` - Registro com eventos
- [x] `resources/views/professional/subscription.blade.php` - Assinatura com eventos
- [x] `resources/views/layouts/professional.blade.php` - Layout com pixel

#### Configuração
- [x] `.env.example` - Variável FACEBOOK_PIXEL_ID adicionada

#### Documentação
- [x] `FACEBOOK_PIXEL_SETUP.md` - Guia de configuração
- [x] `FACEBOOK_EVENTS_TRACKING.md` - Lista de eventos
- [x] `FACEBOOK_PIXEL_IMPLEMENTATION_SUMMARY.md` - Resumo da implementação
- [x] `FACEBOOK_EVENTS_VISUAL_MAP.md` - Mapa visual do funil
- [x] `FACEBOOK_PIXEL_CHECKLIST.md` - Este arquivo

#### Utilitários
- [x] `public/test-facebook-events.html` - Página de testes
- [x] `app/Services/FacebookConversionsApiService.php.example` - Exemplo server-side

---

## 🚀 Próximos Passos (Você Precisa Fazer)

### PASSO 1: Configurar o .env ⚠️ OBRIGATÓRIO
```bash
# Edite o arquivo .env
nano .env

# Adicione esta linha (ou edite se já existir):
FACEBOOK_PIXEL_ID=2150463045748997

# Salve (Ctrl+O) e saia (Ctrl+X)
```

### PASSO 2: Limpar Cache do Laravel ⚠️ OBRIGATÓRIO
```bash
cd /home/helder/projetos/zapby
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### PASSO 3: Testar Localmente
```bash
# Acesse no navegador:
# http://localhost/test-facebook-events.html
# ou
# http://seu-dominio.local/test-facebook-events.html

# Teste clicando nos botões e veja o log
```

### PASSO 4: Instalar Facebook Pixel Helper
1. Abra o Chrome
2. Acesse: https://chrome.google.com/webstore/detail/facebook-pixel-helper/fdgfkebogiimcoedlicjlajpkdmockpc
3. Clique em "Usar no Chrome"
4. Aceite as permissões

### PASSO 5: Testar no Site Real
Navegue pelas páginas e verifique os eventos:

#### Landing Page (/)
- [ ] PageView ✅ ao carregar
- [ ] Lead ✅ ao clicar em "Começar Grátis"
- [ ] Lead ✅ ao clicar em "Começar Teste Grátis"
- [ ] Lead ✅ ao clicar em "Começar Agora Grátis"

#### Página de Registro (/professional/register)
- [ ] PageView ✅ ao carregar
- [ ] Lead ✅ ao carregar
- [ ] CompleteRegistration ✅ ao enviar formulário

#### Página de Assinatura (/professional/subscription)
- [ ] PageView ✅ ao carregar
- [ ] ViewContent ✅ ao carregar
- [ ] InitiateCheckout ✅ ao clicar em "Ativar Assinatura"

### PASSO 6: Verificar no Facebook Events Manager
1. Acesse: https://business.facebook.com/events_manager2/
2. Selecione seu pixel
3. Clique em **"Test Events"** (menu lateral)
4. Navegue no seu site em outra aba
5. Veja os eventos chegando em tempo real! 🎉

### PASSO 7: Verificar Domínio (iOS 14+) ⚠️ IMPORTANTE
1. Acesse Facebook Business Manager
2. **Configurações de Negócios** → **Segurança da Marca** → **Domínios**
3. Clique em **Adicionar**
4. Digite seu domínio (ex: `zapby.com.br`)
5. Escolha método de verificação:
   - **Meta tag HTML** (mais fácil)
   - Upload de arquivo HTML
   - Verificação DNS

#### Se escolher Meta Tag:
```html
<!-- Adicione no <head> do welcome.blade.php -->
<meta name="facebook-domain-verification" content="seu_codigo_aqui" />
```

### PASSO 8: Configurar Agregação de Eventos
1. No Facebook Events Manager
2. Selecione seu pixel
3. **Configurações** → **Configurações de Agregação de Eventos**
4. Defina prioridades:
   - **Prioridade 1**: Subscribe
   - **Prioridade 2**: InitiateCheckout
   - **Prioridade 3**: CompleteRegistration
   - **Prioridade 4**: Lead

---

## 🎯 Opcional mas Recomendado

### Implementar Conversions API (Server-Side)
**Benefício**: Rastreamento mais confiável, não bloqueado por ad blockers

```bash
# Instalar SDK
composer require facebook/php-business-sdk

# Configurar .env
FACEBOOK_CONVERSIONS_API_TOKEN=seu_token_aqui

# Seguir instruções em:
# app/Services/FacebookConversionsApiService.php.example
```

### Criar Públicos Personalizados
1. Acesse: https://business.facebook.com/adsmanager/audiences
2. Criar públicos baseados em eventos:
   - Visitantes (PageView)
   - Interessados (Lead)
   - Cadastrados não assinantes (CompleteRegistration + NÃO Subscribe)
   - Iniciaram checkout (InitiateCheckout)
   - Assinantes - EXCLUIR de campanhas (Subscribe)

### Adicionar Banner de Cookies (LGPD)
**Benefício**: Compliance com lei de proteção de dados

Criar arquivo: `resources/views/partials/cookie-banner.blade.php`

```blade
<div id="cookie-banner" class="hidden fixed bottom-0 left-0 right-0 bg-gray-900 text-white p-4 z-50">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <p class="text-sm">
            Usamos cookies e pixels de rastreamento para melhorar sua experiência.
            <a href="/politica-de-privacidade" class="underline">Saiba mais</a>
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
// Mostrar banner se não respondeu ainda
if (!localStorage.getItem('cookiesAnswered')) {
    document.getElementById('cookie-banner').classList.remove('hidden');
}

function acceptCookies() {
    localStorage.setItem('cookiesAnswered', 'true');
    localStorage.setItem('cookiesAccepted', 'true');
    document.getElementById('cookie-banner').classList.add('hidden');
}

function rejectCookies() {
    localStorage.setItem('cookiesAnswered', 'true');
    localStorage.setItem('cookiesAccepted', 'false');
    document.getElementById('cookie-banner').classList.add('hidden');
    if (typeof fbq !== 'undefined') {
        fbq('consent', 'revoke');
    }
}
</script>
```

---

## 🐛 Troubleshooting

### Problema: "Pixel não carrega"
```bash
# Solução 1: Verificar .env
cat .env | grep FACEBOOK_PIXEL_ID

# Solução 2: Limpar cache
php artisan config:clear
php artisan cache:clear

# Solução 3: Verificar se está em HTTPS
# Facebook Pixel funciona melhor em HTTPS
```

### Problema: "Eventos não disparam"
```javascript
// Abra o console do navegador (F12) e digite:
window.fbq

// Se retornar uma função, o pixel está OK
// Se retornar undefined, verifique:
// 1. FACEBOOK_PIXEL_ID está configurado?
// 2. Ad blocker está desativado?
// 3. Arquivo facebook-pixel.blade.php existe?
```

### Problema: "Eventos não aparecem no Events Manager"
- Aguarde até 20 minutos (pode ter delay)
- Use "Test Events" para ver em tempo real
- Verifique se está usando o pixel correto
- Desabilite ad blockers e extensões de privacidade

### Problema: "Match rate baixo"
**Causa**: Poucos dados do usuário enviados
**Solução**: Implementar Conversions API (server-side) com email/telefone hasheados

---

## 📊 Métricas de Sucesso

### Curto Prazo (1-2 semanas)
- [ ] Pixel instalado e funcionando
- [ ] Todos os eventos disparando corretamente
- [ ] Event Match Quality > 6.0
- [ ] Domínio verificado

### Médio Prazo (1 mês)
- [ ] Públicos personalizados criados
- [ ] Primeira campanha de conversão ativa
- [ ] Custo por Lead < R$ 10,00
- [ ] Custo por Cadastro < R$ 30,00

### Longo Prazo (3 meses)
- [ ] Custo por Assinante < R$ 100,00
- [ ] ROAS > 200% (R$ 2 de retorno para cada R$ 1 investido)
- [ ] Conversions API implementada
- [ ] Públicos semelhantes testados

---

## 📞 Recursos e Documentação

### Documentação Interna (no projeto)
- 📖 Setup completo: `FACEBOOK_PIXEL_SETUP.md`
- 📊 Lista de eventos: `FACEBOOK_EVENTS_TRACKING.md`
- 🗺️ Mapa visual: `FACEBOOK_EVENTS_VISUAL_MAP.md`
- 📝 Resumo: `FACEBOOK_PIXEL_IMPLEMENTATION_SUMMARY.md`
- 🧪 Testes: `public/test-facebook-events.html`
- 💻 API exemplo: `app/Services/FacebookConversionsApiService.php.example`

### Links Úteis
- Facebook Pixel Helper: https://chrome.google.com/webstore/detail/facebook-pixel-helper/fdgfkebogiimcoedlicjlajpkdmockpc
- Events Manager: https://business.facebook.com/events_manager2/
- Documentação Oficial: https://developers.facebook.com/docs/facebook-pixel
- Business Manager: https://business.facebook.com/

---

## ✅ Status Final

```
┌──────────────────────────────────────────────────────────┐
│  ✅ IMPLEMENTAÇÃO CLIENT-SIDE: COMPLETA                  │
│  ⚠️  CONFIGURAÇÃO .ENV: PENDENTE (VOCÊ)                  │
│  ⚠️  TESTES: PENDENTE (VOCÊ)                             │
│  📦 CONVERSIONS API: OPCIONAL                            │
│  🍪 BANNER LGPD: OPCIONAL (RECOMENDADO)                  │
└──────────────────────────────────────────────────────────┘
```

**Próximo Passo Imediato**: 
1. Configure `FACEBOOK_PIXEL_ID` no `.env`
2. Rode `php artisan config:clear`
3. Teste com o Pixel Helper

---

**Data**: 24 de abril de 2026
**Versão**: 1.0
**Desenvolvedor**: GitHub Copilot
