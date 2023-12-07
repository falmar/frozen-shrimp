<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');
            $table->uuid('category_id');

            $table->string('name');
            $table->string('url');
            $table->string('image_url');
            $table->string('price');

            $table->timestamps(3);
            $table->softDeletes('deleted_at', 3);

            $table->foreign('category_id')->references('id')->on('categories');
            $table->index(['category_id']);
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
