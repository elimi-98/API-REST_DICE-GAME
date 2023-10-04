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
   
    Route::put('/players/{id}',[UserController::class, 'update']) ->middleware('role:player'); //modifica nom jugador
    Route::post('/players/{id}/games',[UserController::class, 'play'])->middleware('role:player'); //jugador realitza una jugada 
    Route::delete('/players/{id}/games',[UserController::class, 'destroy'])->middleware('role:player'); //elimina totes tirades jugador X
    Route::get('players',[UserController::class, 'index'])->middleware('role:admin'); //retorna tots jugadors
    Route::get('/players/{id}/games', [UserController::class, 'index'])->middleware('role:player'); // llistat jugades jugador X
    Route::get('/plaers/ranking',[UserController::class, 'ranking'])->middleware('role:admin'); // % mitjà d'exits
    Route::get('/plaers/ranking/loser',[UserController::class, 'ranking.loser'])->middleware('role:admin'); // jugador amb % exit mes baix
    Route::get('/plaers/ranking/winner',[UserController::class, 'winner'])->middleware('role:admin'); //jugador amb % exit mes alt



    Route::post('logout',[UserController::class, 'logout'])->middleware('role:admin|player');
});

