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
        if (Schema::hasTable('reviews')) {
            // Change review_image to review_images (JSON) for multiple images
            if (Schema::hasColumn('reviews', 'review_image')) {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->dropColumn('review_image');
                });
            }

            // Add new columns
            if (!Schema::hasColumn('reviews', 'review_images')) {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->json('review_images')->nullable()->after('review_text');
                });
            }

            if (!Schema::hasColumn('reviews', 'admin_reply')) {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->text('admin_reply')->nullable()->after('review_images');
                });
            }

            if (!Schema::hasColumn('reviews', 'admin_reply_date')) {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->timestamp('admin_reply_date')->nullable()->after('admin_reply');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('reviews')) {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropColumn(['review_images', 'admin_reply', 'admin_reply_date']);
                $table->string('review_image')->nullable()->after('review_text');
            });
        }
    }
};
