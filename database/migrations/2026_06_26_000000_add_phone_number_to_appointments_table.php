<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (!Schema::hasColumn('appointments', 'phone_number')) {
                $table->string('phone_number')->nullable()->after('client_whatsapp');
            }
        });

        DB::table('appointments')
            ->where(function ($query) {
                $query->whereNull('phone_number')
                    ->orWhere('phone_number', '');
            })
            ->orderBy('id')
            ->chunkById(100, function ($appointments) {
                foreach ($appointments as $appointment) {
                    $phoneNumber = preg_replace('/[^0-9]/', '', (string) $appointment->client_whatsapp);

                    if (strlen($phoneNumber) >= 10 && strlen($phoneNumber) <= 13) {
                        DB::table('appointments')
                            ->where('id', $appointment->id)
                            ->update(['phone_number' => $phoneNumber]);
                    }
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            if (Schema::hasColumn('appointments', 'phone_number')) {
                $table->dropColumn('phone_number');
            }
        });
    }
};
