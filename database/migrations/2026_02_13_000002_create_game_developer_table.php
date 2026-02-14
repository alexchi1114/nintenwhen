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
        Schema::create('game_developer', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('game_id');
            $table->unsignedSmallInteger('developer_id');
            $table->string('type', 50);
            $table->timestamps();

            $table->unique(['game_id', 'developer_id']);

            $table->foreign('game_id')
                ->references('id')
                ->on('games')
                ->cascadeOnDelete();

            $table->foreign('developer_id')
                ->references('id')
                ->on('developers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_developer');
    }
};
