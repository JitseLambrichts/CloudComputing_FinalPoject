<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('common_name');
            $table->integer('matches_played');
            $table->integer('wins');
            $table->integer('wins_home');
            $table->integer('wins_away');
            $table->integer('losses');
            $table->integer('losses_home');
            $table->integer('losses_away');
            $table->integer('draws');
            $table->integer('points_per_game');
            $table->integer('league_position');
            $table->integer('goals_scored');
            $table->integer('goals_conceded');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
