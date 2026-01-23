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
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('has_separate_delivery')->default(0)->after('related_products');
            $table->decimal('inside_dhaka_delivery_fee', 10, 2)->default(70.00)->after('has_separate_delivery');
            $table->decimal('outside_dhaka_delivery_fee', 10, 2)->default(130.00)->after('inside_dhaka_delivery_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['has_separate_delivery', 'inside_dhaka_delivery_fee', 'outside_dhaka_delivery_fee']);
        });
    }
};
