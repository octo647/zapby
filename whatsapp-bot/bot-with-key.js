import baileys from '@whiskeysockets/baileys'
const { makeWASocket, useMultiFileAuthState, fetchLatestBaileysVersion } = baileys
import qrcode from 'qrcode-terminal'
import fs from 'fs'
import mysql from 'mysql2/promise'

// Configurações
const REMINDERS_FILE = "/var/www/agendazap/storage/app/appointment_reminders.json"
const DB_CONFIG = {
    host: 'localhost',
    user: 'helder',
    password: 'jamapara',
    database: 'agendazap'
}

// Estados de conversa dos usuários
const userStates = new Map()

// Extrair número limpo do JID
function extractPhoneNumber(jid) {
    return jid.replace(/@s\.whatsapp\.net|@lid/g, '')
}

// Criar conexão com banco
async function getDbConnection() {
    return await mysql.createConnection(DB_CONFIG)
}

// Buscar profissional pela chave de acesso
async function getProfessionalByKey(accessKey) {
    const conn = await getDbConnection()
    const [rows] = await conn.execute(
        'SELECT * FROM professionals WHERE access_key = ? AND subscription_status IN (?, ?)',
        [accessKey.toUpperCase(), 'active', 'trial']
    )
    await conn.end()
    return rows[0] || null
}

// Buscar profissional por ID
async function getProfessionalById(id) {
    const conn = await getDbConnection()
    const [rows] = await conn.execute(
        'SELECT * FROM professionals WHERE id = ?',
        [id]
    )
    await conn.end()
    return rows[0] || null
}

// Obter horários disponíveis
async function getAvailableSlots(professionalId, date) {
    const conn = await getDbConnection()
   
    const [profRows] = await conn.execute(
        'SELECT working_days, working_hours, default_duration FROM professionals WHERE id = ?',
        [professionalId]
    )
    
    if (profRows.length === 0) {
        await conn.end()
        return []
    }
    
    const prof = profRows[0]
    const workingDays = JSON.parse(prof.working_days)
    const workingHours = JSON.parse(prof.working_hours)
    const duration = prof.default_duration
    
    const dayOfWeek = new Date(date + 'T12:00:00').getDay()
    if (!workingDays.includes(dayOfWeek)) {
        await conn.end()
        return []
    }
    
    const [appointments] = await conn.execute(
        `SELECT time, duration FROM appointments 
         WHERE professional_id = ? AND date = ? AND status IN ('scheduled', 'confirmed')`,
        [professionalId, date]
    )
    
    await conn.end()
    
    const slots = []
    const [startHour, startMin] = workingHours.start.split(':').map(Number)
    const [endHour, endMin] = workingHours.end.split(':').map(Number)
    
    let currentMinutes = startHour * 60 + startMin
    const endMinutes = endHour * 60 + endMin
    
    while (currentMinutes + duration <= endMinutes) {
        const hour = Math.floor(currentMinutes / 60)
        const min = currentMinutes % 60
        const timeStr = `${String(hour).padStart(2, '0')}:${String(min).padStart(2, '0')}`
        
        const isOccupied = appointments.some(apt => {
            const [aptHour, aptMin] = apt.time.split(':').map(Number)
            const aptStart = aptHour * 60 + aptMin
            const aptEnd = aptStart + apt.duration
            const slotEnd = currentMinutes + duration
            
            return (currentMinutes < aptEnd && slotEnd > aptStart)
        })
        
        if (!isOccupied) {
            slots.push(timeStr)
        }
        
        currentMinutes += duration
    }
    
    return slots
}

// Criar agendamento
async function createAppointment(data) {
    const conn = await getDbConnection()
    const [result] = await conn.execute(
        `INSERT INTO appointments 
        (professional_id, client_name, client_whatsapp, date, time, duration, status, created_at, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, 'scheduled', NOW(), NOW())`,
        [data.professional_id, data.client_name, data.client_whatsapp, data.date, data.time, data.duration]
    )
    await conn.end()
    return result.insertId
}

// Processar mensagem
async function processMessage(sock, from, name, text) {
    const phone = extractPhoneNumber(from)
    let state = userStates.get(phone) || { step: 'start' }
    
    console.log(`📨 ${name} (${phone}): ${text} [Step: ${state.step}]`)
    
    switch (state.step) {
        case 'start':
            // Cliente envia a chave de acesso
            const accessKey = text.trim().toUpperCase()
            
            if (accessKey.length < 4 || accessKey.length > 20) {
                await sock.sendMessage(from, {
                    text: `Olá *${name}*! 👋\n\nBem-vindo ao *AgendaZap*! 📅\n\nPara agendar, você precisa da *chave de acesso* do profissional.\n\nDigite a chave que você recebeu (ex: ABC12345)`
                })
                return
            }
            
            console.log(`🔍 Buscando profissional com chave: ${accessKey}`)
            const professional = await getProfessionalByKey(accessKey)
            
            if (!professional) {
                await sock.sendMessage(from, {
                    text: `❌ Chave de acesso inválida: *${accessKey}*\n\nVerifique a chave com o profissional e tente novamente.`
                })
                return
            }
            
            console.log(`✅ Profissional encontrado: ${professional.name}`)
            
            state.professional = professional
            state.step = 'choose_date'
            userStates.set(phone, state)
            
            // Mostrar próximos dias disponíveis
            const today = new Date()
            const dates = []
            
            for (let i = 0; i < 14; i++) {
                const date = new Date(today)
                date.setDate(date.getDate() + i)
                const dateStr = date.toISOString().split('T')[0]
                
                // Verificar se tem slots disponíveis
                const slots = await getAvailableSlots(professional.id, dateStr)
                if (slots.length > 0) {
                    dates.push({
                        date: dateStr,
                        display: date.toLocaleDateString('pt-BR', { weekday: 'short', day: '2-digit', month: '2-digit' })
                    })
                }
                
                if (dates.length >= 7) break
            }
            
            if (dates.length === 0) {
                await sock.sendMessage(from, {
                    text: `😔 Desculpe, não há horários disponíveis com *${professional.name}* nas próximas semanas.\n\nEntre em contato diretamente para mais informações.`
                })
                userStates.delete(phone)
                return
            }
            
            let dateMsg = `✅ Profissional: *${professional.name}*\n`
            dateMsg += `💼 Serviço: ${professional.profession}\n`
            dateMsg += `⏱️ Duração: ${professional.default_duration} minutos\n\n`
            dateMsg += `📅 *Escolha a data:*\n\n`
            
            dates.forEach((d, idx) => {
                dateMsg += `${idx + 1}️⃣ ${d.display}\n`
            })
            dateMsg += `\nDigite o número da data desejada:`
            
            state.availableDates = dates
            userStates.set(phone, state)
            
            await sock.sendMessage(from, { text: dateMsg })
            break
            
        case 'choose_date':
            const dateChoice = parseInt(text)
            
            if (isNaN(dateChoice) || dateChoice < 1 || dateChoice > state.availableDates.length) {
                await sock.sendMessage(from, {
                    text: `⚠️ Opção inválida. Digite um número de 1 a ${state.availableDates.length}.`
                })
                return
            }
            
            state.selectedDate = state.availableDates[dateChoice - 1].date
            
            const slots = await getAvailableSlots(state.professional.id, state.selectedDate)
            
            if (slots.length === 0) {
                await sock.sendMessage(from, {
                    text: `😔 Horários esgotados para esta data. Digite outro número para escolher outra data.`
                })
                return
            }
            
            let slotsMsg = `🕐 *Horários disponíveis em ${state.availableDates[dateChoice - 1].display}:*\n\n`
            
            slots.forEach((slot, idx) => {
                slotsMsg += `${idx + 1}️⃣ ${slot}\n`
            })
            slotsMsg += `\nDigite o número do horário desejado:`
            
            state.availableSlots = slots
            state.step = 'choose_time'
            userStates.set(phone, state)
            
            await sock.sendMessage(from, { text: slotsMsg })
            break
            
        case 'choose_time':
            const timeChoice = parseInt(text)
            
            if (isNaN(timeChoice) || timeChoice < 1 || timeChoice > state.availableSlots.length) {
                await sock.sendMessage(from, {
                    text: `⚠️ Opção inválida. Digite um número de 1 a ${state.availableSlots.length}.`
                })
                return
            }
            
            state.selectedTime = state.availableSlots[timeChoice - 1]
            state.step = 'ask_name'
            userStates.set(phone, state)
            
            await sock.sendMessage(from, {
                text: `📝 *Qual é o seu nome completo?*`
            })
            break
            
        case 'ask_name':
            state.clientName = text.trim()
            
            // Criar agendamento
            const appointmentId = await createAppointment({
                professional_id: state.professional.id,
                client_name: state.clientName,
                client_whatsapp: phone,
                date: state.selectedDate,
                time: state.selectedTime,
                duration: state.professional.default_duration
            })
            
            const dateFormatted = new Date(state.selectedDate + 'T12:00:00').toLocaleDateString('pt-BR', { 
                weekday: 'long', 
                day: '2-digit', 
                month: 'long',
                year: 'numeric'
            })
            
            // Confirmar para cliente
            await sock.sendMessage(from, {
                text: `✅ *Agendamento confirmado!*\n\n📋 *Detalhes do agendamento:*\n\n👤 Nome: ${state.clientName}\n💼 Profissional: ${state.professional.name}\n🔧 Serviço: ${state.professional.profession}\n📅 Data: ${dateFormatted}\n🕐 Horário: ${state.selectedTime}\n⏱️ Duração: ${state.professional.default_duration} minutos\n\nVocê receberá um lembrete 24h antes do seu agendamento.\n\nPara *cancelar* ou *remarcar*, entre em contato com o profissional.\n\nObrigado por usar o AgendaZap! 😊`
            })
            
            // Notificar profissional
            if (state.professional.whatsapp) {
                const profJid = state.professional.whatsapp.includes('@') 
                    ? state.professional.whatsapp 
                    : state.professional.whatsapp + '@s.whatsapp.net'
                
                try {
                    await sock.sendMessage(profJid, {
                        text: `🎉 *Novo agendamento via AgendaZap!*\n\n👤 Cliente: *${state.clientName}*\n📱 WhatsApp: ${phone}\n📅 Data: ${dateFormatted}\n🕐 Horário: ${state.selectedTime}\n⏱️ Duração: ${state.professional.default_duration} minutos\n\nAcesse o painel para mais detalhes:\nhttps://agendazap.pagby.com.br/dashboard`
                    })
                } catch (error) {
                    console.error('❌ Erro ao notificar profissional:', error)
                }
            }
            
            // Limpar estado
            userStates.delete(phone)
            break
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
        const { connection, qr } = update
        if (qr) console.log('📱 QR Code gerado!')
        if (connection === 'close') {
            console.log('❌ Conexão fechada - reconectando...')
            setTimeout(() => main(), 5000)
        } else if (connection === 'open') {
            console.log('✅ Conectado ao WhatsApp!')
        }
    })

    sock.ev.on('messages.upsert', async ({ messages }) => {
        for (const msg of messages) {
            if (msg.key.fromMe || msg.key.remoteJid.includes('@g.us')) continue
            
            const text = msg.message?.conversation || 
                        msg.message?.extendedTextMessage?.text || ''
            const name = msg.pushName || 'Usuário'
            
            try {
                await processMessage(sock, msg.key.remoteJid, name, text)
            } catch (error) {
                console.error('❌ Erro:', error)
                await sock.sendMessage(msg.key.remoteJid, {
                    text: '❌ Ocorreu um erro. Por favor, tente novamente ou entre em contato com o suporte.'
                })
            }
        }
    })

    // Processar lembretes
    setInterval(async () => {
        if (!fs.existsSync(REMINDERS_FILE)) return
        
        try {
            const content = fs.readFileSync(REMINDERS_FILE, 'utf8')
            if (!content.trim()) return
            
            const reminders = JSON.parse(content)
            
            for (const reminder of reminders) {
                const phone = reminder.phone.replace(/\D/g, '')
                const jid = phone + '@s.whatsapp.net'
                
                console.log(`📤 Lembrete → ${phone}`)
                await sock.sendMessage(jid, { text: reminder.message })
                console.log(`✅ Enviado → ${phone}`)
            }
            
            fs.writeFileSync(REMINDERS_FILE, '', 'utf8')
        } catch (error) {
            console.error('❌ Erro ao processar lembretes:', error)
        }
    }, 30000)
}

main().catch(console.error)
