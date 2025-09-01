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
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->ondelete('cascade');
            $table->foreignId('user_id')->constrained('users')->ondelete('set null');
            $table->tinyInteger('rating')->unsigned();
            $table->text('comment');
            $table->timestamp('date')->useCurrent();
            // index
            $table->index(['product_id']);
            $table->index(['rating']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_reviews');
    }
};
