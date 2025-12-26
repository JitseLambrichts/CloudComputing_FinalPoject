<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matches extends Model
{
    public function homeTeam() { return $this->belongsTo(Team::class, 'home_team_id'); }
    public function awayTeam() { return $this->belongsTo(Team::class, 'away_team_id'); }

    public function getScore(): array
    {
        return [
            'home'      => $this->home_team_goal_count,
            'away'      => $this->away_team_goal_count,
            'full-time' => "{$this->home_team_goal_count} - {$this->away_team_goal_count}",
        ];
    }

    public function getWinner(): ? Team
    {
        if ($this->home_team_goal_count > $this->away_team_goal_count) {
            return $this->homeTeam;
        }
        
        if ($this->away_team_goal_count > $this->home_team_goal_count) {
            return $this->awayTeam;
        }

        // Bij gelijkspel retourneren we null
        return null;
    }
}
