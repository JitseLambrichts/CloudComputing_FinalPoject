<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnalyticsServiceClient;
use App\Models\LivePlayerUpdate;
use Grpc\ChannelCredentials;

class GrpcController extends Controller
{
    public function grpc()
    {
        try {
            $host = env('GRPC_HOST', '127.0.0.1');
            $port = env('GRPC_PORT', '50051');

            $client = new AnalyticsServiceClient($host . ':' . $port, [
                'credentials' => ChannelCredentials::createInsecure(),
            ]);

            // 2. Start de bidi-stream
            $call = $client->StreamPlayerAnalytics();

            $players = ["Bryan Heynen", "Matte Smets", "Jarne Steuckers"];
            $responses = [];

            foreach ($players as $player) {
                // 3. Maak een test-update aan
                $update = new LivePlayerUpdate();
                $update->setPlayerName($player);
                $update->setCurrentHeartRate(120 + rand(0, 80));
                $update->setCurrentLactate(2.0 + (rand(0, 100) / 100) * 15.0);
                $update->setTimestamp(time());

                // 4. Verstuur naar de stream
                $call->write($update);
            }

            // 5. Sluit de write-stream
            $call->writesDone();

            // 6. Lees alle responses van de server
            while (($response = $call->read()) !== null) {
                $responses[] = [
                    'speler' => $response->getPlayerName(),
                    'moeheidsniveau' => $response->getFatigueLevel(),
                    'advies' => $response->getRecommendation(),
                ];
            }

            return response()->json($responses);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ], 500);
        }
    }
}