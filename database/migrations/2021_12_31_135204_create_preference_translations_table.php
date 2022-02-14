<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preference_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preference_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index();
            $table->text('value');
            $table->timestamps();

            $table->unique(['preference_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preference_translations');
    }
};
