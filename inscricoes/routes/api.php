<?php

use App\Http\Controllers\EventoController;
use App\Http\Controllers\InscricaoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/ping', function() {
    return [
        'mensagem' => 'API está no ar!',
        'hora' => now()->format('H:i:s')
    ];
});

Route::apiResource('eventos', EventoController::class);
// Route::get('/eventos', [EventoController::class, 'index'])->name('eventos.index');
// Route::get('/eventos/{evento}', [EventoController::class, 'show'])->name('eventos.show');

Route::get('/eventos/{evento}/inscricoes', [InscricaoController::class, 'index']);
Route::post('/eventos/{evento}/inscricoes', [InscricaoController::class, 'store']);
Route::get('/inscricoes/{inscricao}', [InscricaoController::class, 'show']);
