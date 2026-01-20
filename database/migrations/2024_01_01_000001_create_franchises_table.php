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
        Schema::create('franchises', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name');
            $table->string('primary_theme_color_hex', 6);
            $table->string('secondary_theme_color_hex', 6);
            $table->smallInteger('parent_franchise_id')->nullable();
            $table->decimal('predict_multiplier', 4, 2)->default(1.00);
            $table->boolean('show')->default(true);
            $table->timestamps();

            $table->foreign('parent_franchise_id')
                ->references('id')
                ->on('franchises')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franchises');
    }
};
