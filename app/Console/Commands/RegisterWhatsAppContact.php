<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RegisterWhatsAppContact extends Command
{
    protected $signature = 'whatsapp:register-contact {phone} {name}';
    protected $description = 'Registra um contato manualmente no arquivo de contatos do WhatsApp';

    public function handle()
    {
        $phone = $this->argument('phone');
        $name = $this->argument('name');
        
        // Formatar telefone
        $phone = preg_replace('/\D/', '', $phone);
        if (strlen($phone) === 10 || strlen($phone) === 11) {
            $phone = '55' . $phone;
        }
        
        $contactsFile = storage_path('app/whatsapp_contacts.json');
        
        // Carregar contatos existentes
        $contacts = [];
        if (file_exists($contactsFile)) {
            $contacts = json_decode(file_get_contents($contactsFile), true) ?? [];
        }
        
        // Verificar se já existe
        $exists = false;
        foreach ($contacts as $contact) {
            if (isset($contact['phone']) && preg_replace('/\D/', '', $contact['phone']) === $phone) {
                $exists = true;
                $this->warn("Contato já existe: {$contact['name']} ({$contact['phone']})");
                
                if ($this->confirm('Deseja atualizar para usar @s.whatsapp.net?')) {
                    $contact['remoteJid'] = $phone . '@s.whatsapp.net';
                    $contact['name'] = $name;
                    $contact['last_contact'] = now()->toISOString();
                    
                    // Salvar
                    file_put_contents(
                        $contactsFile,
                        json_encode($contacts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                    );
                    
                    $this->info("✅ Contato atualizado: {$name} ({$phone})");
                    $this->info("   remoteJid: {$phone}@s.whatsapp.net");
                }
                return 0;
            }
        }
        
        // Adicionar novo contato
        $newContact = [
            'phone' => $phone,
            'name' => $name,
            'remoteJid' => $phone . '@s.whatsapp.net',
            'first_contact' => now()->toISOString(),
            'last_contact' => now()->toISOString(),
            'registered_manually' => true,
        ];
        
        $contacts[] = $newContact;
        
        // Salvar
        file_put_contents(
            $contactsFile,
            json_encode($contacts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
        
        $this->info("✅ Contato registrado: {$name} ({$phone})");
        $this->info("   remoteJid: {$phone}@s.whatsapp.net");
        $this->info("");
        $this->info("Agora o bot poderá enviar mensagens para este número.");
        
        return 0;
    }
}
