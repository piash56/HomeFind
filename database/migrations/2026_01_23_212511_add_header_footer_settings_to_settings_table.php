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
            $table->boolean('show_header_footer_product_page')->default(0)->after('footer_quick_links');
            $table->boolean('show_header_footer_shop_page')->default(0)->after('show_header_footer_product_page');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['show_header_footer_product_page', 'show_header_footer_shop_page']);
        });
    }
};
