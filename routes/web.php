<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GrpcController;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/grpc', [GrpcController::class, 'grpc'])->name('grpc');

Route::view('/matches', 'teams')->name('matches');
// Dit is nodig omdat de browser denkt dat dit een tracker is als de GraphQL wordt opgeroepen, en de trackers worden geblokkeerd -> bronvermelding Copilot
Route::get('/api/proxy/graphql-matches', function (Request $request) {
    $response = Http::get('http://graphql:5001/api/matches', $request->all());
    return $response->json();
});

Route::view('/data', 'data')->name('data');

Route::view('/live-data', 'liveData')->name('live-data');
Route::get('/api/proxy/graphql-player', function (Request $request) {
    $response = Http::get('http://graphql:5001/api/player', $request->all());
    return $response->json();
});