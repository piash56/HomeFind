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
        // Check if reviews table exists and modify it
        if (Schema::hasTable('reviews')) {
            // Drop existing columns that are not needed for our new structure
            if (Schema::hasColumn('reviews', 'user_id')) {
                try {
                    Schema::table('reviews', function (Blueprint $table) {
                        $table->dropForeign(['user_id']);
                    });
                } catch (Exception $e) {
                    // Foreign key might not exist, continue
                }
                Schema::table('reviews', function (Blueprint $table) {
                    $table->dropColumn('user_id');
                });
            }

            if (Schema::hasColumn('reviews', 'subject')) {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->dropColumn('subject');
                });
            }

            if (Schema::hasColumn('reviews', 'review')) {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->dropColumn('review');
                });
            }

            // Add new columns for our review system
            if (!Schema::hasColumn('reviews', 'order_id')) {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->unsignedBigInteger('order_id')->after('item_id');
                    $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                });
            }

            if (!Schema::hasColumn('reviews', 'customer_name')) {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->string('customer_name')->after('order_id');
                });
            }

            if (!Schema::hasColumn('reviews', 'customer_phone')) {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->string('customer_phone')->after('customer_name');
                });
            }

            if (!Schema::hasColumn('reviews', 'review_text')) {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->text('review_text')->nullable()->after('rating');
                });
            }

            if (!Schema::hasColumn('reviews', 'review_image')) {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->string('review_image')->nullable()->after('review_text');
                });
            }

            // Modify status column to use our enum values
            if (Schema::hasColumn('reviews', 'status')) {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->dropColumn('status');
                });

                Schema::table('reviews', function (Blueprint $table) {
                    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('review_image');
                });
            }

            // Add index for better performance
            if (!Schema::hasIndex('reviews', 'reviews_item_status_index')) {
                Schema::table('reviews', function (Blueprint $table) {
                    $table->index(['item_id', 'status'], 'reviews_item_status_index');
                });
            }
        } else {
            // Create the table if it doesn't exist
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('item_id');
                $table->unsignedBigInteger('order_id');
                $table->string('customer_name');
                $table->string('customer_phone');
                $table->integer('rating'); // 1-5 stars
                $table->text('review_text')->nullable();
                $table->string('review_image')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->timestamps();

                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                $table->index(['item_id', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration modifies an existing table structure
        // We'll keep the original structure intact for rollback safety
        // If needed, specific rollback logic can be added here
    }
};
