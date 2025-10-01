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
        Schema::table('settings', function (Blueprint $table) {
            $table->boolean('cta_enabled')->default(true)->after('is_decimal');
            $table->string('cta_phone')->nullable()->after('cta_enabled');
            $table->string('cta_whatsapp')->nullable()->after('cta_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['cta_enabled', 'cta_phone', 'cta_whatsapp']);
        });
    }
};
