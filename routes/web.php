<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

use App\Http\Controllers\NewsController;

Route::get('/', function () {
    return view('welcome');
});


Route::view('/matches', 'teams')->name('matches');
// Dit is nodig omdat de browser denkt dat dit een tracker is als de GraphQL wordt opgeroepen, en de trackers worden geblokkeerd -> bronvermelding Copilot
Route::get('/api/proxy/graphql-matches', function (Request $request) {
    $response = Http::get('http://graphql:5001/api/matches', $request->all());
    return $response->json();
});

Route::view('/player', 'player')->name('player');
Route::get('/api/proxy/graphql-player', function (Request $request) {
    $response = Http::get('http://graphql:5001/api/player', $request->all());
    return $response->json();
});

// Middleware bronvermelding Copilot
Route::get('/news', [NewsController::class, 'index'])
    ->name('news')
    ->middleware('throttle:10.1');