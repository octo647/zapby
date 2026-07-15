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
        Schema::create('professionals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('whatsapp')->unique();
            $table->string('profession');
            $table->integer('default_duration')->default(60); // minutos
            $table->json('working_days')->nullable(); // 0=dom, 1=seg, etc
            $table->json('working_hours')->nullable();
            $table->string('subscription_status')->default('trial'); // trial, active, cancelled, suspended
            $table->timestamp('trial_ends_at')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professionals');
    }
};
