# Correção do PWA para Android Moderno

## Problema Identificado

Ao tentar instalar o PWA do ZapBy no Android, ocorria um erro indicando que o aplicativo foi feito para uma versão anterior do Android e não cumpre mais os requisitos de segurança.

## Causa Raiz

Os navegadores modernos no Android (especialmente Android 12+) têm requisitos de segurança mais rigorosos para PWAs:

1. **HTTPS obrigatório** - Não funciona em HTTP
2. **Manifest.json completo** - Deve incluir todos os campos necessários
3. **Service Worker moderno** - Deve seguir as melhores práticas atuais
4. **Headers de segurança** - CSP, X-Frame-Options, etc.
5. **MIME types corretos** - manifest.json e sw.js com Content-Type apropriado

## Correções Aplicadas

### 1. Manifest.json Atualizado

**Antes:**
```json
{
  "orientation": "portrait",
  "icons": [
    {
      "purpose": "any maskable"  // ❌ Formato incorreto
    }
  ]
}
```

**Depois:**
```json
{
  "scope": "/",
  "orientation": "portrait-primary",
  "categories": ["business", "productivity"],
  "lang": "pt-BR",
  "dir": "ltr",
  "prefer_related_applications": false,
  "icons": [
    {
      "purpose": "any"  // ✅ Separado corretamente
    },
    {
      "purpose": "maskable"
    }
  ]
}
```

**Campos adicionados:**
- `scope`: Define o escopo do PWA
- `categories`: Categoriza o app nas lojas
- `lang` e `dir`: Internacionalização
- `prefer_related_applications`: Desabilita sugestão de apps nativos

### 2. Service Worker Modernizado

**Melhorias implementadas:**
- ✅ Versionamento adequado do cache
- ✅ Estratégia "Network First" com fallback
- ✅ `skipWaiting()` e `claim()` para atualizações imediatas
- ✅ Tratamento de erros melhorado
- ✅ Cache apenas de requisições GET
- ✅ Logging para depuração

### 3. Headers de Segurança (Apache)

Adicionados ao `zapby.conf`:

```apache
# Segurança
Header always set X-Content-Type-Options "nosniff"
Header always set X-Frame-Options "SAMEORIGIN"
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"

# Content Security Policy
Header always set Content-Security-Policy "default-src 'self' https: ..."

# MIME types corretos
<FilesMatch "manifest\.json$">
    Header set Content-Type "application/manifest+json"
</FilesMatch>
```

### 4. .htaccess Atualizado

```apache
# MIME types para PWA
<FilesMatch "manifest\.json$">
    Header set Content-Type "application/manifest+json"
</FilesMatch>

<FilesMatch "sw\.js$">
    Header set Content-Type "application/javascript"
    Header set Service-Worker-Allowed "/"
</FilesMatch>
```

### 5. JavaScript com Verificação HTTPS

```javascript
const isSecure = window.location.protocol === 'https:' || 
                 window.location.hostname === 'localhost';

if ('serviceWorker' in navigator && isSecure) {
    // Registrar service worker apenas em contexto seguro
}
```

## Como Aplicar as Correções

### Método 1: Script Automático

```bash
cd /home/helder/projetos/zapby
./scripts/fix-pwa-android.sh
```

### Método 2: Manual

1. **Sincronizar arquivos:**
```bash
rsync -avz public/ helder@zapby.com.br:/var/www/zapby/public/
rsync -avz resources/views/ helder@zapby.com.br:/var/www/zapby/resources/views/
```

2. **Aplicar configuração do Apache:**
```bash
scp scripts/zapby.conf helder@zapby.com.br:/tmp/
ssh helder@zapby.com.br "sudo mv /tmp/zapby.conf /etc/httpd/conf.d/zapby.conf && sudo systemctl reload httpd"
```

3. **Limpar cache no servidor:**
```bash
ssh helder@zapby.com.br "cd /var/www/zapby && php artisan cache:clear && php artisan config:clear && php artisan view:clear"
```

## Testes e Validação

### 1. Teste no Android

1. Acesse `https://zapby.com.br` no Chrome Android
2. Aguarde o prompt de instalação
3. Instale o PWA
4. Verifique se não há mais erros

### 2. Validação com Lighthouse

```bash
# No Chrome Desktop
1. Abra DevTools (F12)
2. Vá para aba "Lighthouse"
3. Selecione "Progressive Web App"
4. Execute a auditoria
5. Score deve ser 100% ou próximo
```

### 3. Verificação do Manifest

```
chrome://inspect#devices
```

1. Conecte seu Android via USB
2. Ative depuração USB no Android
3. Inspecione a página zapby.com.br
4. Vá para Application > Manifest
5. Verifique se todos os campos estão corretos

### 4. Service Worker

No DevTools (Application > Service Workers):
- ✅ Status: Activated and running
- ✅ Scope: https://zapby.com.br/
- ✅ Update on reload: Habilitado

## Requisitos do Android Moderno

### Android 12+ (API 31+)

- ✅ HTTPS obrigatório
- ✅ Manifest completo com `scope`
- ✅ Service Worker funcional
- ✅ Ícones 192x192 e 512x512
- ✅ Headers de segurança (CSP)
- ✅ MIME types corretos

### Android 13+ (API 33+)

Adiciona requisitos:
- ✅ `theme-color` no manifest
- ✅ `display: standalone`
- ✅ Estratégia de cache adequada

## Troubleshooting

### Erro: "App feito para versão anterior"

**Causa:** Manifest incompleto ou headers de segurança faltando

**Solução:**
1. Verifique se o manifest tem todos os campos
2. Confirme que os headers estão sendo enviados:
```bash
curl -I https://zapby.com.br/manifest.json
```

### PWA não aparece para instalação

**Causa:** Service Worker não registrado ou HTTPS não funcionando

**Solução:**
1. Abra DevTools Console
2. Procure por erros de Service Worker
3. Verifique se está em HTTPS:
```javascript
console.log(window.location.protocol) // deve ser 'https:'
```

### Service Worker não atualiza

**Causa:** Cache agressivo

**Solução:**
```javascript
// No DevTools > Application > Service Workers
// Clique em "Unregister" e recarregue a página
```

Ou pelo código:
```javascript
navigator.serviceWorker.getRegistrations().then(registrations => {
    registrations.forEach(reg => reg.unregister())
})
```

## Recursos Adicionais

- [MDN: Progressive Web Apps](https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps)
- [Google: PWA Checklist](https://web.dev/pwa-checklist/)
- [Chrome DevTools: Debug PWAs](https://developer.chrome.com/docs/devtools/progressive-web-apps/)

## Monitoramento

Para verificar se o PWA está funcionando corretamente:

```bash
# Logs do Apache
ssh helder@zapby.com.br "sudo tail -f /var/log/httpd/zapby_access.log | grep -E 'manifest|sw\.js'"

# Verificar headers
curl -I https://zapby.com.br/manifest.json
curl -I https://zapby.com.br/sw.js
```

## Changelog

### 2026-04-21
- ✅ Manifest.json atualizado com campos obrigatórios
- ✅ Service Worker modernizado (v2)
- ✅ Headers de segurança adicionados ao Apache
- ✅ .htaccess configurado com MIME types corretos
- ✅ Verificação HTTPS no JavaScript
- ✅ Script de deploy automatizado criado
