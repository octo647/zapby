import baileys from '@whiskeysockets/baileys'
const { makeWASocket, useMultiFileAuthState, fetchLatestBaileysVersion } = baileys
import qrcode from 'qrcode-terminal'
import fs from 'fs'
import path from 'path'
import { fileURLToPath } from 'url'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)

// Caminhos de arquivos
const REMINDERS_FILE = "/var/www/agendazap/storage/app/appointment_reminders.json"
const CONTACTS_FILE = "/var/www/agendazap/storage/app/whatsapp_contacts.json"

// Salvar contato que enviou mensagem
function saveContact(phone, name) {
    try {
        let contacts = []
        if (fs.existsSync(CONTACTS_FILE)) {
            contacts = JSON.parse(fs.readFileSync(CONTACTS_FILE, 'utf8'))
        }
        
        // Verificar se contato já existe
        const exists = contacts.find(c => c.phone === phone)
        if (!exists) {
            contacts.push({
                phone: phone,
                name: name || 'Desconhecido',
                first_contact: new Date().toISOString(),
                last_contact: new Date().toISOString()
            })
            console.log(`💾 Novo contato salvo: ${phone} - ${name}`)
        } else {
            // Atualizar último contato
            exists.last_contact = new Date().toISOString()
            if (name && name !== 'Desconhecido') {
                exists.name = name
            }
        }
        
        fs.writeFileSync(CONTACTS_FILE, JSON.stringify(contacts, null, 2))
    } catch (error) {
        console.error('❌ Erro ao salvar contato:', error)
    }
}

async function main() {
    console.log('🚀 Iniciando AgendaZap WhatsApp Bot...')
    
    const { state, saveCreds } = await useMultiFileAuthState('./auth_info_baileys')
    const { version } = await fetchLatestBaileysVersion()
    
    const sock = makeWASocket({
        version,
        auth: state,
        printQRInTerminal: true
    })

    sock.ev.on('creds.update', saveCreds)
    
    sock.ev.on('connection.update', (update) => {
        const { connection, lastDisconnect, qr } = update
        
        if (qr) {
            console.log('📱 QR Code gerado! Escaneie com seu WhatsApp')
        }
        
        if (connection === 'close') {
            console.log('❌ Conexão fechada')
            main()
        } else if (connection === 'open') {
            console.log('✅ Conectado ao WhatsApp!')
        }
    })

    // Escutar mensagens recebidas
    sock.ev.on('messages.upsert', async ({ messages, type }) => {
        try {
            for (const msg of messages) {
                // Ignorar mensagens próprias e de grupos
                if (msg.key.fromMe || msg.key.remoteJid.includes('@g.us')) continue
                
                const phone = msg.key.remoteJid.replace('@s.whatsapp.net', '')
                const name = msg.pushName || 'Desconhecido'
                const messageText = msg.message?.conversation || 
                                   msg.message?.extendedTextMessage?.text || 
                                   ''
                
                console.log(`📨 Mensagem recebida de ${name} (${phone}): ${messageText}`)
                
                // Salvar contato
                saveContact(phone, name)
                
                // Responder com mensagem de boas-vindas
                const welcomeMessage = `Olá *${name}*! 👋

Bem-vindo ao *AgendaZap*! 📅

Seu contato foi registrado e você receberá lembretes automáticos sobre seus agendamentos.

Sistema ativo e funcionando! ✅`

                await sock.sendMessage(msg.key.remoteJid, { text: welcomeMessage })
                console.log(`✅ Resposta enviada para ${phone}`)
            }
        } catch (error) {
            console.error('❌ Erro ao processar mensagem:', error)
        }
    })

    // Verificar lembretes periodicamente
    setInterval(async () => {
        if (!fs.existsSync(REMINDERS_FILE)) return
        
        try {
            const content = fs.readFileSync(REMINDERS_FILE, 'utf8')
            if (!content.trim()) return
            
            const reminders = JSON.parse(content)
            
            for (const reminder of reminders) {
                const phoneNumber = reminder.phone.replace(/\D/g, '')
                const jid = phoneNumber + '@s.whatsapp.net'
                
                console.log(`📤 Enviando lembrete para ${phoneNumber}`)
                await sock.sendMessage(jid, { text: reminder.message })
                console.log(`✅ Lembrete enviado para ${phoneNumber}`)
            }
            
            // Limpar arquivo após enviar
            fs.writeFileSync(REMINDERS_FILE, '', 'utf8')
        } catch (error) {
            console.error('❌ Erro ao processar lembretes:', error)
        }
    }, 30000) // Verificar a cada 30 segundos
}

main().catch(console.error)
