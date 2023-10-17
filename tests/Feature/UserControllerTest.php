<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\Models\User;
use App\Models\Game; 


class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    // register method

    public function testEmptyDatesValidation(){

        $response = $this->json('POST', '/api/players', []);

        $response->assertStatus(422)
        ->assertJson([
            "message" => [
                "email" => ["The email field is required."],
                "password" => ["The password field is required."]
            ]
        ]);
    }
    
    public function testValidRegistration(){

        $user_info = [
            'name' => 'Melina',
            'email' => 'melina@gmail.com',
            'password' => '123456789',
        ];

        $response = $this->json('POST', '/api/players', $user_info);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Register completed']);
    }

    public function testDuplicateNameValidation(){

        $user = User::factory()->create();

        $user_data = [
            'name' => $user->name,
            'email' => 'newuser@gmail.com',
            'password' => 'password123456',
        ];

        $response = $this->json('POST', '/api/players', $user_data);

        $response->assertStatus(422)
        ->assertJson([
            "message" => [
                "name" => ["The name already exists."]
            ]
        ]);
        
    }

    public function testDuplicateEmailValidation(){

        $user = User::factory()->create();

        $user_data = [
            'name' => 'Melina',
            'email' => $user->email,
            'password' => '123456789',
        ];

        $response = $this->json('POST', '/api/players', $user_data);

        $response->assertStatus(422)
        ->assertJson([
            "message" => [
                "email" => ["The email is already registered."]
            ]
        ]);
    }

    public function testInvalidEmailValidation(){

        $user_data = [
            'name' => 'Invalid User',
            'email' => 'invalid_email',
            'password' => 'securepassword',
        ];

        $response = $this->json('POST', '/api/players', $user_data);

        $response->assertStatus(422)
        ->assertJson([
            "message" => [
                "email" => ["The email field is not valid."]
            ]
        ]);
    }

    public function testShortPasswordValidation(){

        $user_data = [
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password' => 'o',
        ];

        $response = $this->json('POST', '/api/players', $user_data);

        $response->assertStatus(422)
        ->assertJson([
            "message" => [
                "password" => ["The password must contain a minimum of 8 characters."]
            ]
        ]);
    }

    public function testValidPasswordValidation(){

        $user_data = [
            'name' => 'Sara',
            'email' => 'sara@gmail.com',
            'password' => '123456789',
        ];

        $response = $this->json('POST', '/api/players', $user_data);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Register completed']);
    }

    public function testRoleAssignment(){

        $user_data = [
            'name' => 'Melina',
            'email' => 'melina@gmail.com',
            'password' => '123456789',
        ];

        $response = $this->json('POST', '/api/players', $user_data);

        $user = User::where('email', 'melina@gmail.com')->first();

        $this->assertTrue($user->hasRole('player'));
    }

    //login 

    public function testLoginWithValidData(){
        
        $user = User::factory()->create([
            'email' => 'melina@gmail.com',
            'password' => bcrypt('123456789'), 
        ]);

        $response = $this->json('POST', '/api/login', [
            'email' => 'melina@gmail.com',
            'password' => '123456789',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user',
                'accessToken',
            ]);
    }

    public function testLoginWithInvalidData(){

        $response = $this->json('POST', '/api/login', [
            'email' => 'nonexistent@gmail.com', 
            'password' => 'invalidpassword', 
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Incorrect credentials',
            ]);
    }

    //logout
    public function testLogout(){

        $user = new User();
        $user->name = 'Usuario de Prueba';
        $user->email = 'prueba@correo.com';
        $user->password = bcrypt('contraseña');
        $user->save();

        $this->actingAs($user);

        $response = $this->post('/api/logout');

        $response->assertStatus(302);

    }

    
    // update method
    public function testUpdateWithValidData(){

        $user = User::factory()->create([
            'name' => 'AntiguoNombre',
        ]);

        Passport::actingAs($user);

        $data = [
            'name' => 'NuevoNombre',
        ];

        $response = $this->json('PUT', '/api/players/' . $user->id, $data);

        $response->assertStatus(200)
            ->assertJson(['message' => 'The name has been updated.']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'NuevoNombre',
        ]);
    }
    
    public function testUpdateWithSameName(){

        $user = User::factory()->create([
            'name' => 'AntiguoNombre',
        ]);
    
        Passport::actingAs($user);
    
        $data = [
            'name' => 'AntiguoNombre', 
        ];
    
        $response = $this->json('PUT', '/api/players/' . $user->id, $data);
    
        $response->assertStatus(422)
            ->assertJson(['message' => 'Try a new name, please.']);
    
    }

    public function testUpdateWithEmptyName(){

        $user = User::factory()->create([
            'name' => 'AntiguoNombre',
        ]);

        Passport::actingAs($user);

        $data = [
            'name' => '', // Campo del nombre vacío
        ];

        $response = $this->json('PUT', '/api/players/' . $user->id, $data);

        $response->assertStatus(422)
            ->assertJson(['error' => 'The field is required.']);
    }

    public function testUnauthorizedUpdate(){

        $user = User::factory()->create([
            'name' => 'AntiguoNombre',
        ]);

        $otherUser = User::factory()->create();

        Passport::actingAs($user);

        $data = [
            'name' => 'NuevoNombre', 
        ];

        $response = $this->json('PUT', '/api/players/' . $otherUser->id, $data);

        $response->assertStatus(401)
            ->assertJson(['message' => 'You dont have the permission to update the name.']);
    }

    // game
    public function testPlayersList(){

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Passport::actingAs($admin);

        User::factory(5)->create();

        $response = $this->get('/api/players');

        $response->assertStatus(200);

        $response->assertJsonStructure(['users']);

        $response->assertJsonCount(8, 'users');
    }

    public function testRankingWithAdminRole(){

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Passport::actingAs($admin);

        $totalGames = Game::count();
        $gamesWon = Game::where('game_won', true)->count();
        $winRate = $totalGames > 0 ? ($gamesWon / $totalGames) * 100 : 0;

        $response = $this->get('/api/players/ranking');

        $response->assertStatus(200);

        $response->assertJson([
            'Total games' => $totalGames,
            'All players games rate' => round($winRate, 2),
        ]);
    }

    public function testRankingWinnerWithAdminRole(){

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Passport::actingAs($admin);

        $response = $this->get('/api/players/ranking/winner');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'name',
            'Wins rate',
            'Wins',
            'Total Games',
        ]);
    }
    public function testRankingLoserWithAdminRole(){

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        Passport::actingAs($admin);

        $response = $this->get('/api/players/ranking/winner');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'name',
            'Wins rate',
            'Wins',
            'Total Games',
        ]);
    }
    
}