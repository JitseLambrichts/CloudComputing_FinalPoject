<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GrpcController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-grpc', [GrpcController::class, 'grpc']);