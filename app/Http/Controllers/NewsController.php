<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{
    public function index() {
        $response = Http::get('https://newsapi.org/v2/top-headlines', [
            'category' => 'sports',
            'q' => 'Premier League',
            'apiKey' => env('NEWS_API_KEY')
        ]);

        $news = $response->json();

        return view('news', ['articles' => $news['articles'] ?? []]);
    }
}
