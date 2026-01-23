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
            // Use JSON column to store all hero section settings (saves space)
            if (!Schema::hasColumn('settings', 'hero_section_settings')) {
                $table->json('hero_section_settings')->nullable()->after('gtm_body_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'hero_section_settings')) {
                $table->dropColumn('hero_section_settings');
            }
        });
    }
};
