import makeWASocket, { useMultiFileAuthState, fetchLatestBaileysVersion } from '@whiskeysockets/baileys'
import QRCode from 'qrcode'
import fs from 'fs'
import path from 'path'

const AUTH_DIR = '/var/www/pagby/scripts/whatsapp-bot/auth_info_baileys'
const OUTPUT_DIR = '/var/www/zapby/public'
const QR_IMAGE = path.join(OUTPUT_DIR, 'whatsapp-qrcode.png')
const QR_HTML = path.join(OUTPUT_DIR, 'whatsapp-qrcode.html')

async function generateQRCode() {
    console.log('🔄 Gerando QR Code para WhatsApp...')
    
    const { state, saveCreds } = await useMultiFileAuthState(AUTH_DIR)
    const { version } = await fetchLatestBaileysVersion()
    
    const sock = makeWASocket({
        version,
        auth: state,
        printQRInTerminal: false
    })

    sock.ev.on('creds.update', saveCreds)
    
    sock.ev.on('connection.update', async (update) => {
        const { connection, qr } = update
        
        if (qr) {
            console.log('📱 QR Code recebido!')
            
            // Gerar imagem PNG
            await QRCode.toFile(QR_IMAGE, qr, {
                width: 400,
                margin: 2,
                color: {
                    dark: '#000000',
                    light: '#FFFFFF'
                }
            })
            console.log(`✅ QR Code salvo em: ${QR_IMAGE}`)
            
            // Gerar página HTML
            const html = `<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WhatsApp QR Code - Zapby</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }
        .qr-container {
            background: #f5f5f5;
            border-radius: 15px;
            padding: 20px;
            margin: 30px 0;
            display: inline-block;
        }
        img {
            max-width: 100%;
            height: auto;
            display: block;
        }
        .instructions {
            text-align: left;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
        }
        .instructions h2 {
            color: #25D366;
            font-size: 18px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .instructions ol {
            color: #555;
            line-height: 1.8;
            padding-left: 20px;
        }
        .instructions li {
            margin: 8px 0;
        }
        .refresh-notice {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 14px;
        }
        .refresh-btn {
            background: #25D366;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .refresh-btn:hover {
            background: #20BA5A;
        }
        .timestamp {
            color: #999;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔗 Conectar WhatsApp</h1>
        <p class="subtitle">Zapby Bot</p>
        
        <div class="qr-container">
            <img src="whatsapp-qrcode.png?t=${Date.now()}" alt="QR Code WhatsApp">
        </div>
        
        <div class="instructions">
            <h2>📱 Como conectar</h2>
            <ol>
                <li>Abra o <strong>WhatsApp</strong> no seu celular</li>
                <li>Toque em <strong>Mais opções (⋮)</strong> ou <strong>Configurações</strong></li>
                <li>Toque em <strong>Aparelhos conectados</strong></li>
                <li>Toque em <strong>Conectar um aparelho</strong></li>
                <li>Aponte a câmera para este código QR</li>
            </ol>
        </div>
        
        <div class="refresh-notice">
            ⏱️ Este QR Code expira em alguns minutos. Se não funcionar, atualize a página.
        </div>
        
        <button class="refresh-btn" onclick="location.reload()">🔄 Atualizar QR Code</button>
        
        <p class="timestamp">Gerado em: ${new Date().toLocaleString('pt-BR')}</p>
    </div>
</body>
</html>`
            
            fs.writeFileSync(QR_HTML, html)
            console.log(`✅ Página HTML salva em: ${QR_HTML}`)
            console.log(`\n🌐 Acesse: https://zapby.com.br/whatsapp-qrcode.html\n`)
            
            // Auto-atualizar a cada 30 segundos
            setTimeout(() => {
                console.log('🔄 Aguardando nova conexão ou QR Code...')
            }, 30000)
        }
        
        if (connection === 'open') {
            console.log('✅ WhatsApp conectado com sucesso!')
            console.log('🗑️ Removendo arquivos de QR Code...')
            
            // Remover arquivos após conectar
            try {
                if (fs.existsSync(QR_IMAGE)) fs.unlinkSync(QR_IMAGE)
                if (fs.existsSync(QR_HTML)) fs.unlinkSync(QR_HTML)
            } catch (err) {
                console.error('Erro ao remover arquivos:', err)
            }
            
            process.exit(0)
        }
        
        if (connection === 'close') {
            console.log('❌ Conexão fechada')
            process.exit(1)
        }
    })
}

generateQRCode().catch(console.error)
