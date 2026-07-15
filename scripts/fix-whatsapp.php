<?php

/*
 * Script para corrigir número de WhatsApp do profissional
 * 
 * Uso: php artisan tinker < scripts/fix-whatsapp.php
 * ou copie e cole no tinker
 */

use App\Models\Professional;

// Buscar o profissional Helder
$professional = Professional::where('name', 'LIKE', '%Helder%')->first();

if ($professional) {
    echo "Profissional encontrado: {$professional->name}\n";
    echo "WhatsApp atual: {$professional->whatsapp}\n\n";
    
    // Corrigir o número
    $professional->whatsapp = '5532987007302';
    $professional->save();
    
    echo "✅ WhatsApp atualizado para: {$professional->whatsapp}\n";
} else {
    echo "❌ Profissional não encontrado\n";
}

// Listar todos os profissionais e seus números
echo "\n--- Todos os profissionais ---\n";
foreach (Professional::all() as $prof) {
    echo "{$prof->id} - {$prof->name}: {$prof->whatsapp}\n";
}
