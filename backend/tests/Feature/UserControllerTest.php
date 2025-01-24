<?php

namespace Tests\Feature;

use App\Enums\UserStatus;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_user()
    {
        $adminRole = Role::factory()->create([
            'title' => 'admin',
            'description' => 'Administrator role with full access.'
        ]);
        $user = User::factory()->create([
            'role_id' => $adminRole->id
        ]);
        $token = $user->createToken('TestToken')->plainTextToken;

        Role::factory()->create([
            'id' => 2,
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        

        $requestData = [
            "email" => "adnan@wiki.com",
            "password" => "Adnan=123",
            "name" => "adnan",
            "surname" => "tasdemir",
            "password_confirmation"=> "Adnan=123",
            "username" => "adnan",
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->postJson(route('createUser',$requestData))->json();

        $this->assertEquals($response['code'], 200);
        $this->assertEquals($response['message'], 'Success');
        $this->assertTrue($response['success']);
        $this->assertEquals($response['data']['email'], $requestData['email']);
        $this->assertDatabaseHas('users', [
            'email' => 'adnan@wiki.com',
            'name' => 'adnan',
            'surname' => 'tasdemir',
            'username' => 'adnan',
        ]);
    }


    public function test_create_user_missing_fields()
    {
        $adminRole = Role::factory()->create([
            'title' => 'admin',
            'description' => 'Administrator role with full access.'
        ]);
        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);

        $user = User::factory()->create([
            'role_id' => $adminRole->id
        ]);
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->postJson(route('createUser'),[])->json();

        $this->assertEquals($response['code'], 500);
        $this->assertEquals($response['message'], 'The name field is required. (and 4 more errors)');
        $this->assertFalse($response['success']);
        $this->assertEquals($response['data'],
        [
            "name" => [
                "The name field is required." 
            ], 
            "surname" => [
                "The surname field is required." 
            ], 
            "username" => [
                "The username field is required." 
            ], 
            "email" => [
                "The email field is required." 
            ], 
            "password" => [
                "The password field is required." 
            ] 
         ], 
        );
    }

    public function test_select_user()
    {
        $adminRole = Role::factory()->create([
            'title' => 'admin',
            'description' => 'Administrator role with full access.'
        ]);
        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $admin = User::factory()->create([
            'role_id' => $adminRole->id
        ]);
        $token = $admin->createToken('TestToken')->plainTextToken;

        $user = User::factory()->create([
            'role_id' => $userRole->id
        ]);

        $requestData = [
            'id' => $user->id
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->getJson(route('selectUser',$requestData))->json();

        $this->assertEquals($response['code'], 200);
        $this->assertEquals($response['message'], 'Success');
        $this->assertTrue($response['success']);
        $this->assertEquals($response['data']['email'], $user->email);
    }
    
    public function test_delete_user()
    {
        $adminRole = Role::factory()->create([
            'title' => 'admin',
            'description' => 'Administrator role with full access.'
        ]);
        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $admin = User::factory()->create([
            'role_id' => $adminRole->id
        ]);
        $token = $admin->createToken('TestToken')->plainTextToken;

        $user = User::factory()->create([
            'role_id' => $userRole->id
        ]);

        $requestData = [
            'id' => $user->id
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->deleteJson(route('deleteUser',$requestData))->json();

        $this->assertEquals($response['code'], 200);
        $this->assertEquals($response['message'], 'Success');
        $this->assertTrue($response['success']);
        $this->assertEquals($response['data']['email'], $user->email);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'email' => $user->email
        ]);
    }

    public function test_update_user_missing_fields()
    {
        $adminRole = Role::factory()->create([
            'title' => 'admin',
            'description' => 'Administrator role with full access.'
        ]);
        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $admin = User::factory()->create([
            'role_id' => $adminRole->id
        ]);
        $token = $admin->createToken('TestToken')->plainTextToken;

        $user = User::factory()->create([
            'role_id' => $userRole->id
        ]);

        $requestData = [
            'id' => $user->id
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->putJson(route('updateUser',$requestData))->json();

        $this->assertEquals($response['code'], 500);
        $this->assertEquals($response['message'], 'No updatable data provided.');
        $this->assertFalse($response['success']);
        $this->assertEquals($response['data'], [
            "custom" => [
                "No updatable data provided." 
            ], 
         ], 
        );
    }


    public function test_update_user()
    {
        $adminRole = Role::factory()->create([
            'title' => 'admin',
            'description' => 'Administrator role with full access.'
        ]);
        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $admin = User::factory()->create([
            'role_id' => $adminRole->id
        ]);
        $token = $admin->createToken('TestToken')->plainTextToken;

        $user = User::factory()->create([
            'role_id' => $userRole->id
        ]);

        $requestData = [
            'id' => $user->id,
            'status' => UserStatus::INACTIVE
        ];

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->putJson(route('updateUser',$requestData))->json();

        $this->assertEquals($response['code'], 200);
        $this->assertEquals($response['message'], 'Success');
        $this->assertTrue($response['success']);
        $this->assertEquals($response['data']['email'], $user->email);
        $this->assertEquals($response['data']['status'], UserStatus::INACTIVE->value);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $user->email,
            'status' => UserStatus::INACTIVE
        ]);
    }

    public function test_list_users()
    {
        $adminRole = Role::factory()->create([
            'title' => 'admin',
            'description' => 'Administrator role with full access.'
        ]);
        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $admin = User::factory()->create([
            'role_id' => $adminRole->id
        ]);
        $token = $admin->createToken('TestToken')->plainTextToken;

        User::factory()->create([
            'role_id' => $userRole->id
        ]);
        User::factory()->create([
            'role_id' => $userRole->id
        ]);
        User::factory()->create([
            'role_id' => $userRole->id
        ]);
        User::factory()->create([
            'role_id' => $userRole->id
        ]);
        User::factory()->create([
            'role_id' => $userRole->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->getJson(route('listUsers'))->json();

        $this->assertEquals($response['code'], 200);
        $this->assertEquals($response['message'], 'Success');
        $this->assertTrue($response['success']);
        $this->assertNotEmpty($response['data']);
        $this->assertCount(6,$response['data']);
        $this->assertNotEmpty($response['pagination']);
    }


    public function test_create_user_with_by_user()
    {
        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $user = User::factory()->create([
            'role_id' => $userRole->id
        ]);
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->postJson(route('createUser'),[])->json();
        $this->assertEquals($response['code'], 403);    
        $this->assertEquals($response['message'], 'This action is unauthorized.');
        $this->assertFalse($response['success']);
        $this->assertEmpty($response['data']);
    }

    public function test_select_user_with_by_user()
    {
        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $user = User::factory()->create([
            'role_id' => $userRole->id
        ]);
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->getJson(route('selectUser',['id' => $user->id]))->json();

        $this->assertEquals($response['code'], 403);    
        $this->assertEquals($response['message'], 'This action is unauthorized.');
        $this->assertFalse($response['success']);
        $this->assertEmpty($response['data']);
    }

    public function test_delete_user_with_by_user()
    {
        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $user = User::factory()->create([
            'role_id' => $userRole->id
        ]);
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->deleteJson(route('selectUser',['id' => $user->id]))->json();

        $this->assertEquals($response['code'], 403);    
        $this->assertEquals($response['message'], 'This action is unauthorized.');
        $this->assertFalse($response['success']);
        $this->assertEmpty($response['data']);
    }

    public function test_list_users_with_by_user()
    {
        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $user = User::factory()->create([
            'role_id' => $userRole->id
        ]);
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->getJson(route('listUsers'))->json();

        $this->assertEquals($response['code'], 403);    
        $this->assertEquals($response['message'], 'This action is unauthorized.');
        $this->assertFalse($response['success']);
        $this->assertEmpty($response['data']);
    }

    public function test_update_user_by_user()
    {
        $userRole = Role::factory()->create([
            'title' => 'user',
            'description' => 'Regular user role with limited access.'
        ]);
        $user = User::factory()->create([
            'role_id' => $userRole->id
        ]);
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->getJson(route('updateUser',['id' => $user->id]))->json();

        $this->assertEquals($response['code'], 403);    
        $this->assertEquals($response['message'], 'This action is unauthorized.');
        $this->assertFalse($response['success']);
        $this->assertEmpty($response['data']);
    }


}
