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
        Schema::create('game_system', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('game_id');
            $table->smallInteger('system_id');
            $table->timestamps();

            $table->unique(['game_id', 'system_id']);

            $table->foreign('game_id')
                ->references('id')
                ->on('games')
                ->cascadeOnDelete();

            $table->foreign('system_id')
                ->references('id')
                ->on('systems');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_system');
    }
};
