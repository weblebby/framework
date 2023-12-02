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
        Schema::create('taxonomies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('term_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('taxonomies')->nullOnDelete();
            $table->string('taxonomy');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['term_id', 'taxonomy']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxonomies');
    }
};
