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
        Schema::create('direct_direct_tag', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('direct_id');
            $table->unsignedSmallInteger('direct_tag_id');
            $table->timestamps();

            $table->unique(['direct_id', 'direct_tag_id']);

            $table->foreign('direct_id')
                ->references('id')
                ->on('directs')
                ->cascadeOnDelete();

            $table->foreign('direct_tag_id')
                ->references('id')
                ->on('direct_tags');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('direct_direct_tag');
    }
};
