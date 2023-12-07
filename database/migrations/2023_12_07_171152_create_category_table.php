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
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id');
            $table->primary('id');

            $table->string('name');
            $table->string('slug');
            $table->string('url');
            $table->string('last_crawl_hash');
            $table->timestamp('last_crawl_at', 3);

            $table->timestamps(3);
            $table->softDeletes('deleted_at', 3);

            $table->unique(['slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
