<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\Models\User;

class UserControllerTest extends TestCase
{
    use DatabaseTransactions;

    // register method

    public function testEmptyDatesValidation()
    {
        $response = $this->json('POST', '/api/players', []);

        $response->assertStatus(422)
        ->assertJson([
            "message" => [
                "email" => ["The email field is required."],
                "password" => ["The password field is required."]
            ]
        ]);
    }
    
    public function testValidRegistration()
    {
        $user_info = [
            'name' => 'Melina',
            'email' => 'melina@gmail.com',
            'password' => '123456789',
        ];

        $response = $this->json('POST', '/api/players', $user_info);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Register completed']);
    }

    public function testDuplicateNameValidation()
    {
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

    public function testDuplicateEmailValidation()
    {
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

    public function testInvalidEmailValidation()
    {
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

    public function testShortPasswordValidation()
    {
        $user_data = [
            'name' => 'User',
            'email' => 'user@example.com',
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

    public function testValidPasswordValidation()
    {
        $user_data = [
            'name' => 'Sara',
            'email' => 'sara@gmail.com',
            'password' => '123456789',
        ];

        $response = $this->json('POST', '/api/players', $user_data);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Register completed']);
    }

    public function testRoleAssignment()
    {
        $user_data = [
            'name' => 'Melina',
            'email' => 'melina@gmail.com',
            'password' => '123456789',
        ];

        $response = $this->json('POST', '/api/players', $user_data);

        $user = User::where('email', 'melina@gmail.com')->first();

        $this->assertTrue($user->hasRole('player'));
    }

    // update method
    protected function registerUser($name, $email, $password)
{
    $response = $this->json('POST', '/api/players', [
        'name' => $name,
        'email' => $email,
        'password' => $password,
    ]);

    $response->assertStatus(201); // Asegura que el registro sea exitoso

    // Puedes devolver cualquier información relevante que quieras de la respuesta, como el usuario creado.
    // Por ejemplo, si la respuesta devuelve un JSON con los datos del usuario, podrías hacer:
    return $response->json();
}
protected function loginUser($email, $password)
{
    $response = $this->json('POST', '/api/login', [
        'email' => $email,
        'password' => $password,
    ]);

    $response->assertStatus(200); // Asegura que el inicio de sesión sea exitoso

    // Puedes devolver cualquier información relevante que quieras de la respuesta, como el token de acceso.
    // Por ejemplo, si la respuesta devuelve un JSON con el token, podrías hacer:
    return $response->json('access_token');
}


    public function testSuccessfulUpdate()
{
    $user = $this->registerUser('Usuario1', 'usuario1@example.com', 'securepassword');

    $token = $this->loginUser('usuario1@example.com', 'securepassword');

    $response = $this->updateUser($user->id, 'NuevoNombre', $token);

    $response->assertStatus(200)
        ->assertJson(['message' => 'The name has been updated.']);
}

}
