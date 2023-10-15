<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;
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
Route::post('players', [UserController::class, 'register']); //crea jugador
Route::post('login', [UserController::class, 'login']); 

Route::middleware('auth:api')->group(function() {
   
    Route::put('/players/{id}',[UserController::class, 'update']); //modifica nom jugador

    Route::post('/players/{id}/games',[GameController::class, 'new_game'])->middleware('role:player'); //jugador realitza una jugada 
    Route::delete('/players/{id}/games',[GameController::class, 'delete_games'])->middleware('role:player'); //elimina totes tirades jugador X
    Route::get('players',[UserController::class, 'players_list'])->middleware('role:admin'); //retorna tots jugadors
    Route::get('/players/{id}/games', [GameController::class, 'games_list'])->middleware('role:player'); // llistat jugades jugador X
    Route::get('/players/ranking',[UserController::class, 'ranking'])->middleware('role:admin'); // % mitjà d'exits
    Route::get('/players/ranking/loser',[UserController::class, 'loser'])->middleware('role:admin'); // jugador amb % exit mes baix
    Route::get('/players/ranking/winner',[UserController::class, 'winner'])->middleware('role:admin'); //jugador amb % exit mes alt



    Route::post('logout',[UserController::class, 'logout']);
});


Route::get('/', function () {
    
    return response()->json(['Log in again, please.'], 401);
    
})->name('login');
