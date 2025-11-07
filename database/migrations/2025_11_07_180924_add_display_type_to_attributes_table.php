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
        if (!Schema::hasColumn('attributes', 'display_type')) {
            Schema::table('attributes', function (Blueprint $table) {
                $table->string('display_type')->default('name')->after('keyword');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('attributes', 'display_type')) {
            Schema::table('attributes', function (Blueprint $table) {
                $table->dropColumn('display_type');
            });
        }
    }
};
