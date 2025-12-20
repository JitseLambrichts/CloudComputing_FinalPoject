<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Matches;
use App\Models\Team;


class MatchesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $path = storage_path('app/private/matches.csv');
        $file = fopen($path, 'r');
        $headers = fgetcsv($file);

        while (($data = fgetcsv($file)) !== FALSE) {
            $row = array_combine($headers, $data);

            // Zoek beide teams op
            $homeTeam = Team::where('common_name', $row['home_team_name'])->first();
            $awayTeam = Team::where('common_name', $row['away_team_name'])->first();

            if ($homeTeam && $awayTeam) {
                Matches::create([
                    'date_gmt'               => \Carbon\Carbon::createFromTimestamp($row['timestamp']),
                    'home_team_id'           => $homeTeam->id,
                    'away_team_id'           => $awayTeam->id,
                    'referee'                => $row['referee'],
                    'attendance'             => (int)$row['attendance'],
                    'stadium_name'           => $row['stadium_name'],
                    'home_team_goal_count'   => (int)$row['home_team_goal_count'],
                    'away_team_goal_count'   => (int)$row['away_team_goal_count'],
                ]);
            }
        }
        fclose($file);
    }
}
