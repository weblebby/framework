<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('navigation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('navigation_items')->nullOnDelete();
            $table->integer('position');
            $table->integer('type');
            $table->nullableMorphs('linkable');
            $table->string('link')->nullable();
            $table->string('smart_type')->nullable();
            $table->integer('smart_limit')->nullable();
            $table->json('smart_filters')->nullable();
            $table->json('smart_sort')->nullable();
            $table->boolean('smart_view_all')->nullable();
            $table->boolean('open_in_new_tab')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('navigation_item_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('navigation_item_id')->constrained()->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('title');
            $table->timestamps();

            $table->unique(['navigation_item_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('navigation_item_translations');
        Schema::dropIfExists('navigation_items');
    }
};
