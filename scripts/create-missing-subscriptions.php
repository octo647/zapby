<?php

// Script para criar subscriptions para profissionais que não têm

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Professional;
use App\Models\Subscription;

echo "Verificando profissionais sem subscription...\n\n";

$professionals = Professional::doesntHave('subscription')->get();

if ($professionals->isEmpty()) {
    echo "✅ Todos os profissionais já têm subscription!\n";
    exit(0);
}

echo "Encontrados {$professionals->count()} profissional(is) sem subscription:\n\n";

foreach ($professionals as $professional) {
    // Se o professional não tem trial_ends_at, definir para 7 dias
    if (!$professional->trial_ends_at) {
        $professional->trial_ends_at = now()->addDays(7);
        $professional->save();
    }
    
    $subscription = Subscription::create([
        'professional_id' => $professional->id,
        'status' => 'trial',
        'amount' => 19.90,
    ]);
    
    echo "✓ Subscription criada para: {$professional->name} ({$professional->email})\n";
    echo "  - ID: {$subscription->id}\n";
    echo "  - Status: {$subscription->status}\n";
    echo "  - Trial até: " . \Carbon\Carbon::parse($professional->trial_ends_at)->format('d/m/Y H:i') . "\n\n";
}

echo "✅ Processo concluído!\n";
