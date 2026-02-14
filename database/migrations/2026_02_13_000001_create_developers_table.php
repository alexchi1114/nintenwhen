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
        Schema::create('developers', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 1000);
            $table->string('primary_theme_color_hex', 6);
            $table->string('img_path', 1000)->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('predict_multiplier', 4, 2)->default(1.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developers');
    }
};
