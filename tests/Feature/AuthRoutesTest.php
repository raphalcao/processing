<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Aws\MockHandler;
use Aws\Result;
use Aws\Sdk;

class AuthRoutesTest extends TestCase
{
    use RefreshDatabase;


    public function test_user_registration_with_aws_cognito()
    {
        $mock = new MockHandler();
        $mock->append(new Result([
            'UserSub' => 'mock-user-id',
        ]));

        $sdk = new Sdk([
            'region' => 'us-east-1',
            'version' => 'latest',
            'handler' => $mock,
        ]);

        $cognitoClient = $sdk->createCognitoIdentityProvider();
        $this->app->instance('aws.cognito', $cognitoClient);

        $response = $this->postJson('/api/register', [
            'email' => 'usuario6@exemplo.com',
            'password' => 'Senha123!',
            'name' => 'Test User',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'User registered successfully'
        ]);
    }

    public function test_user_login_with_aws_cognito()
    {
        User::create([
            'name' => 'Usuário Teste',
            'email' => 'usuario6@exemplo.com',
            'password' => Hash::make('Senha123!'),
            'cognito_user_id' => 'mock-user-id',
        ]);

        $mock = new MockHandler();
        $mock->append(new Result([
            'AuthenticationResult' => [
                'AccessToken' => 'mock-access-token',
                'ExpiresIn' => 3600,
                'IdToken' => 'mock-id-token',
                'RefreshToken' => 'mock-refresh-token',
            ],
        ]));
        $this->app->instance('aws.cognito', $mock);

        $response = $this->postJson('/api/login', [
            'email' => 'usuario6@exemplo.com',
            'password' => 'Senha123!',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'AccessToken',
        ]);
    }
}
