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
            $table->string('tagline1_icon', 50)->default('fas fa-truck')->after('show_header_footer_shop_page');
            $table->string('tagline1_text')->default('Free delivery over 500tk')->after('tagline1_icon');
            $table->string('tagline2_icon', 50)->default('fas fa-percent')->after('tagline1_text');
            $table->string('tagline2_text')->default('5% off for website order')->after('tagline2_icon');
            $table->string('tagline3_icon', 50)->default('fas fa-gift')->after('tagline2_text');
            $table->string('tagline3_text')->default('2nd time? get your 15% voucher')->after('tagline3_icon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'tagline1_icon',
                'tagline1_text',
                'tagline2_icon',
                'tagline2_text',
                'tagline3_icon',
                'tagline3_text'
            ]);
        });
    }
};
