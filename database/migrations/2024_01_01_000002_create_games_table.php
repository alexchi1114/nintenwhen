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
        Schema::create('games', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 1000);
            $table->date('release_date')->nullable();
            $table->smallInteger('franchise_id')->nullable();
            $table->string('img_path', 1000)->default('');
            $table->boolean('is_upcoming')->default(false);
            $table->string('preorder_link', 1000)->nullable();
            $table->string('release_date_tentative', 255)->nullable();
            $table->integer('display_order')->default(0);
            $table->boolean('show')->default(true);
            $table->timestamps();

            $table->foreign('franchise_id')
                ->references('id')
                ->on('franchises');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};
