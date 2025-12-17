<?php

namespace Tests\Feature;

use Tests\TestCase;

class PetStoreApiTest extends TestCase
{
    /**
     * Test GET /v2/pets endpoint
     */
    public function test_find_pets_endpoint_returns_array(): void
    {
        $response = $this->get('/v2/pets');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'name']
                 ]);
    }

    /**
     * Test GET /v2/pets with limit parameter
     */
    public function test_find_pets_with_limit_parameter(): void
    {
        $response = $this->get('/v2/pets?limit=3');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'name']
                 ]);

        $data = $response->json();
        $this->assertLessThanOrEqual(3, count($data), 'Should return at most 3 pets');
    }

    /**
     * Test GET /v2/pets with tags parameter
     */
    public function test_find_pets_with_tags_parameter(): void
    {
        $response = $this->get('/v2/pets?tags[]=demo');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     '*' => ['id', 'name']
                 ]);
    }

    /**
     * Test GET /v2/pets/{id} endpoint
     */
    public function test_find_pet_by_id_endpoint(): void
    {
        $response = $this->get('/v2/pets/1');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id',
                     'name'
                 ]);
    }

    /**
     * Test POST /v2/pets endpoint (add pet)
     */
    public function test_add_pet_endpoint(): void
    {
        $newPet = [
            'name' => 'Test Pet',
            'tag' => 'test'
        ];

        $response = $this->postJson('/v2/pets', $newPet);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id',
                     'name',
                     'tag'
                 ]);

        $data = $response->json();
        $this->assertEquals('Test Pet', $data['name']);
    }

    /**
     * Test DELETE /v2/pets/{id} endpoint
     */
    public function test_delete_pet_endpoint(): void
    {
        $response = $this->delete('/v2/pets/999');

        $response->assertStatus(204);
    }
}
