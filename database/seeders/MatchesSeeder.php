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
                    'date_gmt'                      => \Carbon\Carbon::createFromTimestamp($row['timestamp']),
                    'home_team_id'                  => $homeTeam->id,
                    'away_team_id'                  => $awayTeam->id,
                    'referee'                       => $row['referee'],
                    'attendance'                    => (int)$row['attendance'],
                    'stadium_name'                  => $row['stadium_name'],
                    'home_team_goal_count'          => (int)$row['home_team_goal_count'],
                    'away_team_goal_count'          => (int)$row['away_team_goal_count'],
                    'home_team_corner_count'        => (int)$row['home_team_corner_count'],
                    'away_team_corner_count'        => (int)$row['away_team_corner_count'],
                    'home_team_yellow_cards'        => (int)$row['home_team_yellow_cards'],
                    'home_team_red_cards'           => (int)$row['home_team_red_cards'],
                    'away_team_yellow_cards'        => (int)$row['away_team_yellow_cards'],
                    'away_team_red_cards'           => (int)$row['away_team_red_cards'],
                    'home_team_shots'               => (int)$row['home_team_shots'],
                    'away_team_shots'               => (int)$row['away_team_shots'],
                    'home_team_shots_on_target'     => (int)$row['home_team_shots_on_target'],
                    'away_team_shots_on_target'     => (int)$row['away_team_shots_on_target'],
                    'home_team_shots_off_target'    => (int)$row['home_team_shots_off_target'],
                    'away_team_shots_off_target'    => (int)$row['away_team_shots_off_target'],    
                    'home_team_fouls'               => (int)$row['home_team_fouls'],
                    'away_team_fouls'               => (int)$row['away_team_fouls'],
                    'home_team_possession'          => (int)$row['home_team_possession'],
                    'away_team_possession'          => (int)$row['away_team_possession'], 
                    'team_a_xg'                     => (float)$row['team_a_xg'],
                    'team_b_xg'                     => (float)$row['team_b_xg'],
                    'odds_ft_home_team_win'         => (float)$row['odds_ft_home_team_win'],
                    'odds_ft_draw'                  => (float)$row['odds_ft_draw'],
                    'odds_ft_away_team_win'         => (float)$row['odds_ft_away_team_win'],
                ]);
            }
        }
        fclose($file);
    }
}
