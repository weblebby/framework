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
        Schema::create('metafields', function (Blueprint $table) {
            $table->id();
            $table->morphs('metafieldable');
            $table->string('key')->index();
            $table->text('original_value')->nullable();
            $table->timestamps();

            $table->unique(['metafieldable_id', 'metafieldable_type', 'key'], 'metafieldable_key_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metafields');
    }
};
