<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_login()
    {
        Role::factory()->create([
            'title' => 'admin',
            'description' => 'Administrator role with full access.'
        ]);
        
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson(route('login'), [
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ])->json();
        
        $this->assertEquals($response['code'], 200);
        $this->assertEquals($response['message'], 'Success');
        $this->assertTrue($response['success']);
        $this->assertEquals($response['data']['email'], $user->email);
        $this->assertNotEmpty($response['data']['token']);
       
    }

    public function test_invalid_login_credentials()
    {
        Role::factory()->create([
            'title' => 'admin',
            'description' => 'Administrator role with full access.'
        ]);
        
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson(route('login'), [
            'email' => 'testuser@example.com',
            'password' => 'wrongpassword',
        ])->json();

        $this->assertEquals($response['code'], 401);
        $this->assertEquals($response['message'], 'Invalid credentials');
        $this->assertFalse($response['success']);
        $this->assertEmpty($response['data']);
    }

    public function test_login_missing_fields()
    {
        $response = $this->postJson(route('login'), [])->json();
        $this->assertEquals($response['code'], 500);
        $this->assertEquals($response['message'], 'The email field is required. (and 1 more error)');
        $this->assertFalse($response['success']);
        $this->assertEquals($response['data'],
            [
                'email' =>[
                    0 => 'The email field is required.',   
                ],
                'password' =>[
                    0 => 'The password field is required.',
                ],
            ],
        );
    }

    public function test_internal_server_error_during_login()
    {
        $mockService = $this->createMock(UserService::class);
        $mockService->method('login')->willThrowException(new \Exception('Something went wrong'));
        $this->app->instance(UserService::class, $mockService);
        $response = $this->postJson(route('login'), [
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ])->json();
        
        $this->assertEquals($response['code'], 500);
        $this->assertEquals($response['message'], 'Something went wrong');
        $this->assertFalse($response['success']);
        $this->assertEmpty($response['data']);
    }

    public function test_logout_success()
    {
        Role::factory()->create([
            'title' => 'admin',
            'description' => 'Administrator role with full access.'
        ]);
        
        $user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make('password123'),
        ]);

        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson(route('logout'))->json();

        $this->assertEquals($response['code'], 200);
        $this->assertEquals($response['message'], 'Successfully logged out');
        $this->assertTrue($response['success']);
        $this->assertCount(0, $user->tokens);
    }

    public function test_logout_unauthenticated()
    {
        $response = $this->postJson(route('logout'))->json();

        $this->assertEquals($response['code'], 500);
        $this->assertEquals($response['message'],'Unauthenticated.');
        $this->assertFalse($response['success']);
        $this->assertEmpty($response['data']);

    }

    public function test_internal_server_error_during_logout()
    {
        $mockService = $this->createMock(AuthController::class);
        $mockService->method('logout')->willThrowException(new \Exception('Logout failed'));
        $this->app->instance(AuthController::class, $mockService);

        Role::factory()->create([
            'title' => 'admin',
            'description' => 'Administrator role with full access.'
        ]);
        
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->postJson(route('logout'))->json();

        $this->assertEquals($response['code'], 500);
        $this->assertEquals($response['message'], 'Logout failed');
        $this->assertFalse($response['success']);
        $this->assertEmpty($response['data']);
    }
}
