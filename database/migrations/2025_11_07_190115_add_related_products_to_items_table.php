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
        if (!Schema::hasColumn('items', 'related_products')) {
            Schema::table('items', function (Blueprint $table) {
                $table->text('related_products')->nullable()->after('bulk_pricing_data');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('items', 'related_products')) {
            Schema::table('items', function (Blueprint $table) {
                $table->dropColumn('related_products');
            });
        }
    }
};
