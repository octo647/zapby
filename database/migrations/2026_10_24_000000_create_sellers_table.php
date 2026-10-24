<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('whatsapp')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::table('professionals', function (Blueprint $table) {
            $table->foreignId('seller_id')->nullable()->after('is_admin')->constrained('sellers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('professionals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('seller_id');
        });
        Schema::dropIfExists('sellers');
    }
};
