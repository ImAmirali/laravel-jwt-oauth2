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
        Schema::create('products', function (Blueprint $table) {
            # column
            $table->id();
            $table->string('title', 255);
            $table->text('description');
            $table->integer('price');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->ondelete('cascade');
            $table->foreignId('brand_id')->constrained('brands')->onDelete('cascade');
            $table->integer('discount_percentage')->default(0);
            $table->decimal('rating', 2, 1)->default(0);
            $table->integer('stock')->default(0);
            $table->string('sku', 50)->unique();
            $table->integer('weight')->nullable();
            $table->string('warranty_information', 255)->nullable();
            $table->string('shipping_information', 255)->nullable();
            $table->string('availability_status', 50)->nullable();
            $table->string('return_policy', 255)->nullable();
            $table->integer('minimum_order_quantity')->default(1);
            $table->timestamps(); # created_at, updated_at
            $table->softDeletes(); # deleted_at
            $table->string('barcode', 50)->nullable();
            $table->text('qr_code')->nullable();
            $table->string('thumbnail', 500)->nullable();
            # index
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
