<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professional_id')->constrained()->onDelete('cascade');
            $table->string('asaas_customer_id')->nullable();
            $table->string('asaas_subscription_id')->nullable();
            $table->string('status')->default('trial'); // trial, active, cancelled, suspended
            $table->date('next_due_date')->nullable();
            $table->decimal('amount', 10, 2)->default(14.90);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
