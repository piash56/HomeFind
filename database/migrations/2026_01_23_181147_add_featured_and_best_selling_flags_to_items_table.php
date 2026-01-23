<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('is_featured')->default(0)->after('status');
            $table->boolean('is_best_selling')->default(0)->after('is_featured');
        });
        
        // Migrate existing data: if is_type is 'feature' or 'flash_deal', set is_featured = 1
        // if is_type is 'best', set is_best_selling = 1
        DB::statement("UPDATE items SET is_featured = 1 WHERE is_type IN ('feature', 'flash_deal')");
        DB::statement("UPDATE items SET is_best_selling = 1 WHERE is_type = 'best'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['is_featured', 'is_best_selling']);
        });
    }
};
