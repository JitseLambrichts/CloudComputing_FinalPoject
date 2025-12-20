<?php

namespace Database\Seeders;

use App\Models\Player;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pad naar het bestand
        $path = storage_path('app/private/players.csv');
        $file = fopen($path, 'r');

        // Sla de eerste regel (headers) over
        $headers = fgetcsv($file);

        while (($data = fgetcsv($file)) !== FALSE) {
            // Combineer headers met data voor een associatieve array
            $row = array_combine($headers, $data);

            $team = \App\Models\Team::where('common_name', $row['Current Club'])->first();

            Player::create([
                'full_name'                 => $row['full_name'],
                'age'                       => (int) $row['age'],
                'birthday_GMT'              => $row['birthday_GMT'],
                'position'                  => $row['position'],
                'team_id'                   => $team ? $team->id : null,
                'minutes_played_overall'    => (int) $row['minutes_played_overall'],
                'nationality'               => $row['nationality'],
                'goals_overall'             => (int) $row['goals_overall'],
                'assists_overall'           => (int) $row['assists_overall'],
                'yellow_cards_overall'      => (int) $row['yellow_cards_overall'],
                'red_cards_overall'         => (int) $row['red_cards_overall'],
            ]);
        }
        fclose($file);
    }
}
