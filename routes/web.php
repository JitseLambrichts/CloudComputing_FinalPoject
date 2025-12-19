<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GrpcController;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-grpc', [GrpcController::class, 'grpc']);

Route::view('/matches', 'teams');
// Dit is nodig omdat de browser denkt dat dit een tracker is als de GraphQL wordt opgeroepen, en de trackers worden geblokkeerd -> bronvermelding Copilot
Route::get('/api/proxy/graphql-matches', function (Request $request) {
    $response = Http::get('http://graphql:5001/api/matches', $request->all());
    return $response->json();
});