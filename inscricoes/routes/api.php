<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\InscricaoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function() {
    return [
        'mensagem' => 'API está no ar!',
        'hora' => now()->format('H:i:s')
    ];
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn (Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('eventos', EventoController::class);

    Route::get('/eventos/{evento}/inscricoes', [InscricaoController::class, 'index']);
    Route::post('/eventos/{evento}/inscricoes', [InscricaoController::class, 'store']);
    Route::get('/inscricoes/{inscricao}', [InscricaoController::class, 'show']);
});
