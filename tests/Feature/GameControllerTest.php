<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\Models\User;
use App\Models\Game; 

class GameControllerTest extends TestCase
{
    use DatabaseTransactions;
    public function testNewGameAuthorized()
    {
        $player = User::factory()->create();
        $player->assignRole('player');
        Passport::actingAs($player);
    
        $response = $this->post('/api/players/' . $player->id . '/games');
    
        $response->assertStatus(200);
    
        $response->assertJsonStructure([
            'message',
            'first_dice',
            'second_dice',
            'result',
        ]);
    }
     

  public function testGamesListAuthorizedWithGame()
{
    $user = User::factory()->create();
    $user->assignRole('player');

    Passport::actingAs($user);

    $game = new Game();
    $game->user_id = $user->id;
    $game->first_dice = 3;
    $game->second_dice = 4;
    $game->game_won = false;
    $game->save();

    $response = $this->get('/api/players/'.$user->id.'/games');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'game_list',
        'wins_rate',
    ]);
}


    public function testGamesListNoGames(){

        $user = User::factory()->create();
        $user->assignRole('player');

        Passport::actingAs($user);

        $response = $this->get("/api/players/{$user->id}/games");

        $response->assertStatus(202);
        $response->assertJson(['message' => 'Play a game to register.']);
    }

    public function testGamesListUnauthorized(){
        
        $user = User::factory()->create();
        $user->assignRole('player');

        $otherUser = User::factory()->create();
        $otherUser->assignRole('player');

        Passport::actingAs($user);

        $response = $this->get("/api/players/{$otherUser->id}/games");

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Not authorized.']);
    }

    public function testDeleteGamesWithGames(){
            
        $user = User::factory()->create();
        $user->assignRole('player');

        $game = new Game();
        $game->user_id = $user->id;
        $game->first_dice = 3;
        $game->second_dice = 4;
        $game->game_won = false;
        $game->save();
        Passport::actingAs($user);

        $response = $this->delete('/api/players/'.$user->id.'/games');

        $response->assertStatus(200);
        $response->assertJson(['message' => 'The games have been deleted']);
    }

    public function testDeleteGamesWithNoGames(){

        $user = User::factory()->create();
        $user->assignRole('player');

        Passport::actingAs($user);

        $response = $this->delete('/api/players/'.$user->id.'/games');

        $response->assertStatus(202);
        $response->assertJson(['message' => 'You do not have any games to delete.']);
    }

    public function testDeleteGamesUnauthorized(){

        $user = User::factory()->create();
        $user->assignRole('player');
        $anotherUser = User::factory()->create();
        $anotherUser->assignRole('player');

        Passport::actingAs($user);

        $response = $this->delete('/api/players/'.$anotherUser->id.'/games');

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Not authorized']);
    }

 }
