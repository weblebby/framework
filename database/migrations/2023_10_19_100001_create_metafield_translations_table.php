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
        Schema::create('metafield_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('metafield_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index();
            $table->text('value');
            $table->timestamps();

            $table->unique(['metafield_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metafield_translations');
    }
};
