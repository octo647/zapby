import makeWASocket, { useMultiFileAuthState, fetchLatestBaileysVersion } from 'baileys'
import QRCode from 'qrcode'
import fs from 'fs'
import path from 'path'

const AUTH_DIR = './auth_info_baileys'
const OUTPUT_IMAGE = '/var/www/zapby/public/qrcode-whatsapp.png'

async function generateQRCode() {
    console.log('🔄 Gerando QR Code para WhatsApp...')
    
    const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR)
    const { version } = await fetchLatestBaileysVersion()
    
    const sock = makeWASocket({
        version,
        auth: state
    })

    sock.ev.on('creds.update', saveCreds)
    
    sock.ev.on('connection.update', async (update) => {
        const { connection, qr } = update
        
        if (qr) {
            console.log('📱 QR Code recebido!')
            
            // Gerar imagem PNG
            await QRCode.toFile(OUTPUT_IMAGE, qr, {
                width: 500,
                margin: 2,
                color: {
                    dark: '#000000',
                    light: '#FFFFFF'
                }
            })
            
            console.log('✅ QR Code gerado com sucesso!')
            console.log(`📂 Arquivo: ${OUTPUT_IMAGE}`)
            console.log('🌐 Acesse: https://zapby.pagby.com.br/qrcode-whatsapp.png')
            console.log('')
            console.log('Aguardando conexão...')
        }
        
        if (connection === 'open') {
            console.log('✅ Conectado ao WhatsApp!')
            console.log('Você pode fechar este script agora.')
            process.exit(0)
        }
        
        if (connection === 'close') {
            console.log('❌ Conexão fechada')
            process.exit(1)
        }
    })
}

generateQRCode().catch(err => {
    console.error('Erro:', err)
    process.exit(1)
})
