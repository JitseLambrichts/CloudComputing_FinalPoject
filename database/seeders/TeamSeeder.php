<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class TeamSeeder extends Seeder
{
    public function run()
    {
        // Pad naar het bestand
        $path = storage_path('app/private/teams.csv');
        $file = fopen($path, 'r');

        // Sla de eerste regel (headers) over
        $headers = fgetcsv($file);

        while (($data = fgetcsv($file)) !== FALSE) {
            // Combineer headers met data voor een associatieve array
            $row = array_combine($headers, $data);

            Team::create([
                'common_name'    => $row['common_name'],
                'matches_played' => (int)$row['matches_played'],
                'wins'           => (int)$row['wins'],
                'wins_home'           => (int)$row['wins_home'],
                'wins_away'           => (int)$row['wins_away'],
                'losses'         => (int)$row['losses'],
                'losses_home'         => (int)$row['losses_home'],
                'losses_away'         => (int)$row['losses_away'],
                'draws'          => (int)$row['draws'],
                'points_per_game'          => (int)$row['points_per_game'],
                'league_position'          => (int)$row['league_position'],
                'goals_scored'   => (int)$row['goals_scored'],
                'goals_conceded' => (int)$row['goals_conceded'],
            ]);
        }

        fclose($file);
    }
}