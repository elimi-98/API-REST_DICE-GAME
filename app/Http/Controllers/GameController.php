<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Game;


class GameController extends Controller{

    public function new_game($id){
        
        $id = Auth::id();
        $user = User::find($id);

        if ($user->hasRole('player')) {
            $first_dice = rand(1, 6);
            $second_dice = rand(1, 6);
    
            $game_won = ($first_dice + $second_dice) === 7 ? "Winner" : "Loser";
            
            $rate = $user->getWinsRateAttribute(); 
            $user->wins_rate = $rate;
            $user->save();

            $game = new Game();
            $game->user_id = $user->id;
            $game->first_dice = $first_dice;
            $game->second_dice = $second_dice;
            $game->game_won = ($first_dice + $second_dice) === 7;
    
            $game->save();
    
            return response()->json([
                'message' => 'Tirada exitosa',
                'first_dice' => $first_dice,
                'second_dice' => $second_dice,
                'result' => $game_won,
            ], 200);
        } else {
            return response()->json(['message' => 'Not authorized'], 401);
        }
    }
    public function games_list($id){
        
        $authUser = Auth::user();

        if ($authUser->id == $id) {

            $games = $authUser->games;

            if ($games->count() > 0) {
                $game_list = [];
                foreach ($games as $game) {
                   
                    $result = $game->game_won ? 'Winner' : 'Loser';

                    $game_details = [
                        'first dice' => $game->first_dice,
                        'second dice' => $game->second_dice,
                        'result' => $result,
                    ];

                    $game_list[] = $game_details;
                }

                return response()->json([
                    'game_list' => $game_list,
                    'wins_rate'=> $authUser->wins_rate,
                
                ], 200);
            } else {
                return response()->json(['message' => 'Play a game to register.'], 202);
            }
        } else {
            return response()->json(['message' => 'Not authorized.'], 401);
        }
    }

    public function delete_games($id) {
        $authUser = Auth::user();
    
        if ($authUser->id == $id) {
            $games = $authUser->games;
    
            if ($games->count() > 0) {
                foreach ($games as $game) {
                    $game->delete();
                }
    
                return response()->json([
                    'message' => 'The games have been deleted'
                ], 200);
            } else {
                return response()->json([
                    'message' => 'You do not have any games to delete.'
                ], 202);
            }
        }
        return response()->json(['message' => 'Not authorized'], 401);
    }
        
}