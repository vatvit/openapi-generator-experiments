<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test API health check endpoint
     */
    public function test_health_check_endpoint(): void
    {
        $response = $this->get('/api/v1/health');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'timestamp',
                     'version'
                 ])
                 ->assertJson([
                     'status' => 'healthy',
                     'version' => '1.0.0'
                 ]);
    }

    /**
     * Test API documentation endpoint
     */
    public function test_api_documentation_endpoint(): void
    {
        $response = $this->get('/api/docs');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'openapi_spec',
                     'version',
                     'endpoints'
                 ]);
    }

    /**
     * Test user creation
     */
    public function test_user_creation(): void
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'role' => 'user'
        ];

        $response = $this->postJson('/api/v1/users', $userData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'id',
                     'name',
                     'email',
                     'role',
                     'is_active',
                     'created_at',
                     'updated_at'
                 ])
                 ->assertJson([
                     'name' => $userData['name'],
                     'email' => $userData['email'],
                     'role' => $userData['role'],
                     'is_active' => true
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'name' => $userData['name']
        ]);
    }

    /**
     * Test user validation
     */
    public function test_user_creation_validation(): void
    {
        $response = $this->postJson('/api/v1/users', []);

        $response->assertStatus(422)
                 ->assertJsonStructure([
                     'code',
                     'message',
                     'errors' => [
                         '*' => [
                             'field',
                             'message'
                         ]
                     ]
                 ]);
    }

    /**
     * Test user listing with pagination
     */
    public function test_user_listing(): void
    {
        // Create test users
        User::factory(5)->create();

        $response = $this->get('/api/v1/users?limit=3&offset=0');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'users' => [
                         '*' => [
                             'id',
                             'name',
                             'email',
                             'created_at'
                         ]
                     ],
                     'total',
                     'limit',
                     'offset'
                 ]);

        $data = $response->json();
        $this->assertCount(3, $data['users']);
        $this->assertEquals(3, $data['limit']);
        $this->assertEquals(0, $data['offset']);
        $this->assertGreaterThanOrEqual(5, $data['total']);
    }

    /**
     * Test single user retrieval
     */
    public function test_user_retrieval(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_active' => true
        ]);

        $response = $this->get("/api/v1/users/{$user->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $user->id,
                     'name' => $user->name,
                     'email' => $user->email,
                     'role' => 'admin',
                     'is_active' => true
                 ]);
    }

    /**
     * Test user not found
     */
    public function test_user_not_found(): void
    {
        $response = $this->get('/api/v1/users/999999');

        $response->assertStatus(404)
                 ->assertJson([
                     'code' => 404,
                     'message' => 'User not found'
                 ]);
    }

    /**
     * Test user update
     */
    public function test_user_update(): void
    {
        $user = User::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'role' => 'moderator',
            'is_active' => false
        ];

        $response = $this->putJson("/api/v1/users/{$user->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $user->id,
                     'name' => 'Updated Name',
                     'role' => 'moderator',
                     'is_active' => false
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'role' => 'moderator'
        ]);
    }

    /**
     * Test user deletion
     */
    public function test_user_deletion(): void
    {
        $user = User::factory()->create();

        $response = $this->delete("/api/v1/users/{$user->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);
    }

    /**
     * Test role validation
     */
    public function test_user_role_validation(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'invalid_role'
        ];

        $response = $this->postJson('/api/v1/users', $userData);

        $response->assertStatus(422);
    }
}