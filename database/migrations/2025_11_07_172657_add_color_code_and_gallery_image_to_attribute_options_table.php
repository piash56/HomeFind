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
        if (!Schema::hasColumn('attribute_options', 'color_code')) {
            Schema::table('attribute_options', function (Blueprint $table) {
                $table->string('color_code')->nullable()->after('image');
            });
        }
        
        if (!Schema::hasColumn('attribute_options', 'gallery_image_id')) {
            Schema::table('attribute_options', function (Blueprint $table) {
                $table->unsignedBigInteger('gallery_image_id')->nullable()->after('color_code');
                $table->foreign('gallery_image_id')->references('id')->on('galleries')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('attribute_options', 'gallery_image_id')) {
            Schema::table('attribute_options', function (Blueprint $table) {
                $table->dropForeign(['gallery_image_id']);
                $table->dropColumn('gallery_image_id');
            });
        }
        
        if (Schema::hasColumn('attribute_options', 'color_code')) {
            Schema::table('attribute_options', function (Blueprint $table) {
                $table->dropColumn('color_code');
            });
        }
    }
};
