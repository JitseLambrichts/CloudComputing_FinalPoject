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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_gmt');
            $table->foreignId('home_team_id')->constrained('teams');
            $table->foreignId('away_team_id')->constrained('teams');
            $table->string('referee')->nullable();
            $table->integer('attendance')->nullable();
            $table->string('stadium_name')->nullable();
            $table->integer('home_team_goal_count')->nullable();
            $table->integer('away_team_goal_count')->nullable();
            $table->integer('home_team_corner_count')->nullable();
            $table->integer('away_team_corner_count')->nullable();
            $table->integer('home_team_yellow_cards')->nullable();
            $table->integer('home_team_red_cards')->nullable();
            $table->integer('away_team_yellow_cards')->nullable();
            $table->integer('away_team_red_cards')->nullable();
            $table->integer('home_team_shots')->nullable();
            $table->integer('away_team_shots')->nullable();
            $table->integer('home_team_shots_on_target')->nullable();
            $table->integer('away_team_shots_on_target')->nullable();
            $table->integer('home_team_shots_off_target')->nullable();
            $table->integer('away_team_shots_off_target')->nullable();
            $table->integer('home_team_fouls')->nullable();
            $table->integer('away_team_fouls')->nullable();
            $table->integer('home_team_possession')->nullable();
            $table->integer('away_team_possession')->nullable();
            $table->decimal('team_a_xg', 5, 2)->nullable();             //5 staat voor het totaal aantal cijfers, en 2 staat voor het aantal decimalen
            $table->decimal('team_b_xg', 5, 2)->nullable();
            $table->decimal('odds_ft_draw', 5, 2)->nullable();
            $table->decimal('odds_ft_away_team_win', 5, 2)->nullable();
            $table->decimal('odds_ft_over15', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
