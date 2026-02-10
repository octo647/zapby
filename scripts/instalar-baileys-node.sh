#!/bin/bash
# Instala servidor WhatsApp com Baileys (solução definitiva)

set -e

VPS="helder@69.6.222.77"
PORT="22022"

echo "🚀 Criando servidor WhatsApp com Baileys..."

ssh -p $PORT $VPS << 'ENDSSH'

# Criar diretório
mkdir -p ~/whatsapp-server
cd ~/whatsapp-server

# package.json
cat > package.json << 'EOF'
{
  "name": "agendazap-whatsapp",
  "version": "1.0.0",
  "type": "module",
  "dependencies": {
    "@whiskeysockets/baileys": "^6.7.5",
    "qrcode-terminal": "^0.12.0",
    "qrcode": "^1.5.3",
    "express": "^4.18.2",
    "pino": "^8.16.2"
  }
}
EOF

# server.js
cat > server.js << 'EOF'
import makeWASocket, { DisconnectReason, useMultiFileAuthState } from '@whiskeysockets/baileys';
import express from 'express';
import QRCode from 'qrcode';
import fs from 'fs';

const app = express();
app.use(express.json());

let sock;
let qrCodeData = null;
let isConnected = false;

// Função para iniciar WhatsApp
async function connectToWhatsApp() {
    const { state, saveCreds } = await useMultiFileAuthState('./auth_info');
    
    sock = makeWASocket({
        auth: state,
        printQRInTerminal: true,
    });

    sock.ev.on('connection.update', async (update) => {
        const { connection, lastDisconnect, qr } = update;
        
        if (qr) {
            console.log('📱 QR Code gerado!');
            qrCodeData = await QRCode.toDataURL(qr);
        }
        
        if (connection === 'close') {
            const shouldReconnect = lastDisconnect?.error?.output?.statusCode !== DisconnectReason.loggedOut;
            console.log('Conexão fechada. Reconectando:', shouldReconnect);
            isConnected = false;
            
            if (shouldReconnect) {
                connectToWhatsApp();
            }
        } else if (connection === 'open') {
            console.log('✅ WhatsApp conectado!');
            isConnected = true;
            qrCodeData = null;
        }
    });

    sock.ev.on('creds.update', saveCreds);
}

// API Endpoints
app.get('/status',  (req, res) => {
    res.json({ connected: isConnected });
});

app.get('/qrcode', (req, res) => {
    if (qrCodeData) {
        const base64 = qrCodeData.replace('data:image/png;base64,', '');
        res.json({ 
            qrcode: base64,
            dataUrl: qrCodeData
        });
    } else if (isConnected) {
        res.json({ message: 'Já conectado' });
    } else {
        res.status(503).json({ error: 'QR Code não disponível ainda' });
    }
});

app.post('/send', async (req, res) => {
    const { phone, message } = req.body;
    
    if (!isConnected) {
        return res.status(503).json({ error: 'WhatsApp não conectado' });
    }
    
    try {
        const jid = phone.includes('@') ? phone : `${phone}@s.whatsapp.net`;
        await sock.sendMessage(jid, { text: message });
        res.json({ success: true });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

app.post('/logout', async (req, res) => {
    if (sock) {
        await sock.logout();
        isConnected = false;
        qrCodeData = null;
        res.json({ success: true });
    } else {
        res.json({ error: 'Não conectado' });
    }
});

// Iniciar
const PORT = 3000;
app.listen(PORT, async () => {
    console.log(`🚀 Servidor rodando na porta ${PORT}`);
    await connectToWhatsApp();
});
EOF

# Instalar dependências
echo "📦 Instalando dependências..."
npm install

# Criar systemd service
sudo tee /etc/systemd/system/whatsapp-server.service > /dev/null << 'EOF'
[Unit]
Description=AgendaZap WhatsApp Server
After=network.target

[Service]
Type=simple
User=helder
WorkingDirectory=/home/helder/whatsapp-server
ExecStart=/usr/bin/node server.js
Restart=on-failure

[Install]
WantedBy=multi-user.target
EOF

# Iniciar serviço
echo "🔄 Iniciando serviço..."
sudo systemctl daemon-reload
sudo systemctl enable whatsapp-server
sudo systemctl start whatsapp-server

echo ""
echo "✅ Servidor WhatsApp instalado!"
echo "📋 Ver logs: sudo journalctl -u whatsapp-server -f"
echo "📱 QR Code: http://69.6.222.77:3000/qrcode"

ENDSSH

echo ""
echo "✅ Instalação concluída!"
echo ""
echo "🌐 URLs:"
echo "   QR Code: http://agendazap.pagby.com.br:3000/qrcode"
echo "   Status: http://agendazap.pagby.com.br:3000/status"
echo ""
echo "Aguarde 10 segundos e abra o QR Code no navegador!"
