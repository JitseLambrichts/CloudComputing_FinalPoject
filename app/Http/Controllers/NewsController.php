<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

// Voor de cache -> bronvermelding Copilot

class NewsController extends Controller
{
    public function index() {
        $articles = Cache::remember('sports_news', 3600, function() {
            $response = Http::get('https://newsapi.org/v2/top-headlines', [
                'category' => 'sports',
                // 'q' => 'Premier League',
                'apiKey' => env('NEWS_API_KEY')
            ]);
            if ($response->successful() && isset($response->json()['articles'])) {
                return $response->json()['articles'];
            }
            return [];
        });

        return view('news', ['articles' => $articles]);
    }
}
