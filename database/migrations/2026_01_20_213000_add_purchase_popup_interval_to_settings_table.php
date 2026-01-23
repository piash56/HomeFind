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
            if (!Schema::hasColumn('settings', 'purchase_popup_interval')) {
                $table->integer('purchase_popup_interval')->default(2000)->after('hero_section_settings')->comment('Purchase popup interval in milliseconds');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'purchase_popup_interval')) {
                $table->dropColumn('purchase_popup_interval');
            }
        });
    }
};
