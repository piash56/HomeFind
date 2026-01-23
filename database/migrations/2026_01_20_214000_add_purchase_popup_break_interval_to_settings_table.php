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
            if (!Schema::hasColumn('settings', 'purchase_popup_break_interval')) {
                $table->integer('purchase_popup_break_interval')->default(2000)->after('purchase_popup_interval')->comment('Purchase popup break interval in milliseconds');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'purchase_popup_break_interval')) {
                $table->dropColumn('purchase_popup_break_interval');
            }
        });
    }
};
