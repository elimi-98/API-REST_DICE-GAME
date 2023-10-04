<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('players', [UsserController::class, 'register']); //crea jugador
Route::post('login', [UsserController::class, 'login']); 

Route::middleware('auth:api')->group(function() {
   
    Route::put('/players/{id}',[UserController::class, 'update']); //modifica nom jugador
    Route::post('/players/{id}/games',[UserController::class, 'play']); //jugador realitza una jugada 
    Route::delete('/players/{id}/games',[UserController::class, 'destroy']); //elimina totes tirades jugador X
    Route::get('players',[UserController::class, 'index']); //retorna tots jugadors
    Route::get('/players/{id}/games', [UserController::class, 'index']); // llistat jugades jugador X
    Route::get('/plaers/ranking',[UserController::class, 'ranking']); // % mitjà d'exits
    Route::get('/plaers/ranking/loser',[UserController::class, 'ranking.loser']); // jugador amb % exit mes baix
    Route::get('/plaers/ranking/winner',[UserController::class, 'winner']); //jugador amb % exit mes alt



    Route::post('logout',[UserController::class, 'logout']);
});

