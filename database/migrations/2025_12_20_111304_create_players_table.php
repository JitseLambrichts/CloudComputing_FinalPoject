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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->integer('age');
            $table->string('birthday_GMT');
            $table->string('position');
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('cascade');
            $table->integer('minutes_played_overall');
            $table->string('nationality');
            $table->integer('goals_overall');
            $table->integer('assists_overall');
            $table->integer('yellow_cards_overall');
            $table->integer('red_cards_overall');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
