# 📱 AgendaPro - Guia de Uso via WhatsApp

## 🎯 Conceito

Sistema de agendamento 100% funcional via WhatsApp. Profissionais e clientes fazem TUDO pelo WhatsApp, sem necessidade de acessar site ou fazer login.

## 👨‍💼 Para Profissionais

### 1. Cadastro Inicial

Envie uma mensagem para o WhatsApp do AgendaPro:
```
Oi
```

O bot responderá:
```
Olá! 👋
Bem-vindo ao AgendaPro!

Sou um assistente para agendamentos.

O que você gostaria de fazer?

1️⃣ Criar minha agenda (profissionais)
2️⃣ Agendar um serviço (clientes)

Digite 1 ou 2
```

**Digite:** `1`

O bot iniciará o cadastro:
```
Vamos criar sua agenda! 🚀

Qual seu nome completo?
```

**Responda:** `João Silva`

```
Ótimo, João Silva! 👍

Qual sua profissão?

1️⃣ Cabeleireiro(a)
2️⃣ Manicure
3️⃣ Barbeiro(a)
4️⃣ Esteticista
5️⃣ Outro

Digite o número
```

**Digite:** `1`

```
Perfeito! ✨

Quanto tempo dura um atendimento em média?

1️⃣ 30 minutos
2️⃣ 1 hora
3️⃣ 1h30
4️⃣ 2 horas

Digite o número
```

**Digite:** `2`

```
🎉 Pronto! Sua agenda está no ar!

📝 Nome: João Silva
💼 Profissão: Cabeleireiro(a)
⏱️ Duração: 60 min

🔗 Seu link:
https://agendapro.com/joao-silva

Compartilhe este link com seus clientes!

✨ 14 dias grátis! Depois R$ 14,90/mês

Digite menu para acessar seus agendamentos
```

### 2. Menu Principal

**Digite:** `menu`

```
🏠 Menu Principal

Olá João Silva!

Escolha uma opção:

1️⃣ Ver todos agendamentos
2️⃣ Agendamentos de hoje
3️⃣ Meu link de agendamento
4️⃣ Meus horários
5️⃣ Ajuda

Digite o número da opção
```

### 3. Ver Agendamentos

**Digite:** `1` ou `agendamentos`

```
📅 Próximos Agendamentos

✅ #123
📆 10/02/2026 às 14:00
👤 Maria Silva
📱 11987654321

⏳ #124
📆 11/02/2026 às 10:00
👤 Carlos Santos
📱 11976543210
▸ Para confirmar: confirmar 124
▸ Para cancelar: cancelar 124

Digite menu para voltar
```

### 4. Agendamentos de Hoje

**Digite:** `2` ou `hoje`

```
📅 Hoje - 07/02/2026

✅ 09:00 - Ana Paula
📱 11999888777

⏳ 14:00 - Roberto Lima
📱 11988777666

Digite menu para voltar
```

### 5. Confirmar/Cancelar Agendamento

**Para confirmar:**
```
confirmar 124
```

Resposta:
```
✅ Agendamento #124 confirmado!

Cliente notificado via WhatsApp.

Digite menu para voltar
```

**Para cancelar:**
```
cancelar 124
```

Resposta:
```
❌ Agendamento #124 cancelado.

Cliente notificado via WhatsApp.

Digite menu para voltar
```

### 6. Obter Link de Agendamento

**Digite:** `3` ou `link`

```
🔗 Seu Link de Agendamento

https://agendapro.com/joao-silva

Compartilhe este link para seus clientes agendarem online!

Digite menu para voltar
```

### 7. Ver Horários de Trabalho

**Digite:** `4` ou `horários`

```
⏰ Meus Horários

📅 Dias: Seg, Ter, Qua, Qui, Sex, Sáb
🕐 Horário: 08:00 às 19:00
⏱️ Duração: 60 minutos

Para alterar horários, acesse:
https://agendapro.com/dashboard/horarios

Digite menu para voltar
```

### 8. Comandos Rápidos

Você pode digitar a qualquer momento:

- `menu` - Voltar ao menu principal
- `agendamentos` - Ver todos agendamentos
- `hoje` - Ver agendamentos de hoje
- `link` - Obter seu link
- `horários` - Ver seus horários
- `ajuda` - Ver lista de comandos
- `confirmar ID` - Confirmar agendamento
- `cancelar ID` - Cancelar agendamento

## 👥 Para Clientes

### Opção 1: Via Link (Recomendado)

1. Cliente recebe o link: `https://agendapro.com/joao-silva`
2. Abre no navegador
3. Vê horários disponíveis
4. Escolhe data e hora
5. Preenche nome e WhatsApp
6. Recebe confirmação automática no WhatsApp

### Opção 2: Via WhatsApp Bot (Futuro)

Cliente pode iniciar conversa com o bot e:
1. Buscar profissional por nome
2. Ver horários disponíveis
3. Agendar direto pela conversa

## 🔔 Notificações Automáticas

### Quando cliente agenda:
**Profissional recebe:**
```
🎉 Novo agendamento!

👤 Cliente: Maria Silva
📅 Data: 10/02/2026
⏰ Horário: 14:00
📱 WhatsApp: 11987654321
```

**Cliente recebe:**
```
✅ Agendamento confirmado!

📅 Data: 10/02/2026
⏰ Horário: 14:00
💈 Profissional: João Silva

Obrigado! 😊
```

### 24h antes do agendamento:
**Cliente recebe:**
```
👋 Olá Maria Silva!

Lembrete do seu agendamento:
📅 Amanhã às 14:00
💈 Profissional: João Silva

Nos vemos lá! 😊
```

### Quando profissional cancela:
**Cliente recebe:**
```
❌ Agendamento cancelado

📅 Data: 10/02/2026
⏰ Horário: 14:00
```

## 🛠️ Configuração Técnica

### 1. WhatsApp API

Use Evolution API ou similar:
```env
WHATSAPP_API_URL=http://localhost:8080
WHATSAPP_API_KEY=sua_api_key
```

### 2. Webhook

Configure o webhook na Evolution API para:
```
POST https://seusite.com/webhook/whatsapp
```

### 3. Formato das Mensagens

O webhook deve enviar:
```json
{
  "from": "5511999999999",
  "body": "texto da mensagem",
  "pushname": "Nome do Contato"
}
```

## 💡 Dicas de Uso

### Para Profissionais:

1. **Compartilhe seu link** em todas as redes sociais
2. **Configure notificações** para não perder agendamentos
3. **Confirme rapidamente** para dar confiança aos clientes
4. **Use comandos rápidos** para agilizar seu dia

### Boas Práticas:

- Responda `menu` sempre que quiser ver opções
- Confirme agendamentos assim que receber
- Verifique `hoje` toda manhã
- Compartilhe seu `link` regularmente

## 🎓 Exemplos de Conversas Reais

### Exemplo 1: Profissional checando agenda
```
Profissional: hoje
Bot: Você tem 3 agendamentos hoje...
Profissional: confirmar 125
Bot: ✅ Confirmado!
Profissional: menu
```

### Exemplo 2: Profissional compartilhando link
```
Profissional: link
Bot: 🔗 Seu link: https://...
[Profissional copia e compartilha nas redes sociais]
```

### Exemplo 3: Gerenciando múltiplos agendamentos
```
Profissional: agendamentos
Bot: [Lista 10 agendamentos]
Profissional: confirmar 126
Bot: ✅ Confirmado!
Profissional: confirmar 127
Bot: ✅ Confirmado!
Profissional: menu
```

## 📊 Vantagens do Sistema

### ✅ Para o Profissional:
- Sem necessidade de aprender sistema complexo
- Tudo no WhatsApp que já usa diariamente
- Comandos simples e intuitivos
- Notificações automáticas
- Link para compartilhar facilmente

### ✅ Para o Cliente:
- Agenda online 24/7
- Confirmação instantânea
- Lembrete automático
- Fácil de usar

### ✅ Para o Negócio:
- Reduz no-shows (com lembretes)
- Profissional mais organizado
- Cliente satisfeito
- Mais produtividade

## 🚀 Próximos Passos

1. Configure sua Evolution API
2. Conecte o webhook
3. Teste o cadastro
4. Compartilhe seu link
5. Comece a receber agendamentos!

---

**AgendaPro** - Sua agenda no seu WhatsApp! 💚
