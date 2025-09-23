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
            $table->boolean('is_gtm')->default(false)->after('is_facebook_messenger');
            $table->text('gtm_head_code')->nullable()->after('is_gtm');
            $table->text('gtm_body_code')->nullable()->after('gtm_head_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'gtm_body_code')) {
                $table->dropColumn('gtm_body_code');
            }
            if (Schema::hasColumn('settings', 'gtm_head_code')) {
                $table->dropColumn('gtm_head_code');
            }
            if (Schema::hasColumn('settings', 'is_gtm')) {
                $table->dropColumn('is_gtm');
            }
        });
    }
};
