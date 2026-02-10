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
        Schema::table('professionals', function (Blueprint $table) {
            $table->boolean('whatsapp_verified')->default(false)->after('whatsapp');
            $table->string('verification_code', 6)->nullable()->after('whatsapp_verified');
            $table->timestamp('verification_code_expires_at')->nullable()->after('verification_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_verified', 'verification_code', 'verification_code_expires_at']);
        });
    }
};
