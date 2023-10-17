<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function register(Request $request){
        
        $validation_rules = [
            'name' => 'nullable|unique:users, name',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ];

        $messages = [
            'name.unique' => 'The name already exists.',
            'email.required' => 'The email field is required.',
            'email.email' => 'The email field is not valid.',
            'email.unique' => 'The email is already registered.',
            'password.min' => 'The password must contain a minimum of 8 characters.',
            'password.required' => 'The password field is required.',

        ];
        $validator = Validator::make($request->all(), $validation_rules, $messages);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()], 422);
        }

        $name = $request->filled('name') ? $request->name : 'Anonymous';

        $user = User::create([
            'name' => $name,
            'email' => $request-> email,
            'password' => Hash::make($request->password),
        ])->assignRole('player');

        return response()->json(['message' => 'Register completed'], 201); 
    }
    
    public function login (Request $request){
        
        $validation_rules = [
            'email' => 'required|email:users',
            'password' => 'required|min:8',
        ];

        $messages = [
            
            'email.required' => 'The email field is required.',
            'email.email' => 'The email field is not valid.',
            'password.required' => 'The password field is required.',
        ];
        $validator = Validator::make($request->all(), $validation_rules, $messages);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()], 422);
        }; 

        $login_data =[
            'email' => $request-> email,
            'password' => $request-> password,
        ];

        if(Auth::attempt($login_data)){
            
            $user = Auth::user();
             /** @var \App\Models\User $user **/ //decimos que $user es una instancia de clase User
            $accessToken = $user-> createToken('authToken')->accessToken;

                return response()->json([
                    
                    'message' => 'You have logged in successfully!',
                    'user' => $user,
                    'accessToken' => $accessToken,
                ], 200);
            } else {
                // Las credenciales de inicio de sesión son incorrectas
                return response()->json([
                    'message' => 'Incorrect credentials',
                ], 401);
            }

    }
    
    public function update(Request $request, $id){
     
        $user = User::find($id); 

        if ($user->id !== Auth::user()->id) {
            return response()->json([
                'message' => 'You dont have the permission to update the name.'], 401);
        } 
        

        
        $updated_name = $request-> input('name');

        if(empty($updated_name)){
            return response()->json([
                'error' => 'The field is required.'], 422);
        }

        if ($updated_name !== $user->name){
            
            $validation_rules = [
                'name' => 'unique:users',
            ];

    
            $validator = Validator::make($request->only('name'), $validation_rules);
            
            if ($validator->fails()) {
                return response()->json([
                    'message' => $validator->errors()], 422);
            }

            $user->name = $updated_name;
            
            $user->update();

            return response()->json([
                'message' => 'The name has been updated.',
            ], 200);
        } else{

            return response()->json([
                'message' => 'Try a new name, please.',
            ], 422);
        }
    }

    public function logout(){
      
        /** @var \App\Models\User $user **/
        $user = Auth::user();

        $token = $user->token();
        $token->revoke();

        return response()->json([
            'message' => 'Log out done!'
        ], 200);

    }
    
    public function players_list(){

    $users = User::orderBy('wins_rate', 'desc')->get();

    return response()->json(['users' => $users], 200);
    }

    public function ranking(){
    
        $totalGames = Game::count(); 
        $gamesWon = Game::where('game_won', true)->count(); 

        $rate= $totalGames > 0 ? ($gamesWon / $totalGames) * 100 : 0;

        return response()->json([
            'Total games' => $totalGames, 'All players games rate' => round($rate,2)
        ], 200);
    }

    public function winner(){
        
        $id = Auth::id();
        $user = User::find($id); 

        if ($user->hasRole('admin')){
            
            $winner = User::whereHas('roles', function ($query) {
                $query->where('name', 'player');
            })->orderBy('wins_rate', 'desc')->first();

            if ($winner) {

                $game_won = $winner->games()->where('game_won', true)->count();
                $wins_rate = round($winner->wins_rate,2);
                return response()->json([
                    'name' => $winner->name,'Wins rate' => $wins_rate,  'Wins' => $game_won, 'Total Games' => $winner->games()->count()], 200);
            } else {
                return response()->json([
                    'message' => 'There is no winner'], 404);
            }
        }
    }

    public function loser(){

        $id = Auth::id();
        $user = User::find($id); 

        if ($user->hasRole('admin')){
            
            $loser = User::whereHas('roles', function ($query) {
                $query->where('name', 'player');
            })->orderBy('wins_rate', 'asc')->first();

            if ($loser) {

                $game_won = $loser->games()->where('game_won', true)->count();
                $wins_rate = round($loser->wins_rate,2);

                return response()->json([
                    'Name' => $loser->name,'Wins rate' => $wins_rate,  'Wins' => $game_won, 'Total games' => $loser->games()->count()], 200);
            } else {
                return response()->json([
                    'message' => 'There is no winner'], 404);
            }
        }
    }
}
