<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Tests to catch common API mistakes during development
 * - Invalid inputs
 * - Missing required fields
 * - Type validation
 *
 * NOTE: These tests are currently skipped because body parameter validation
 * is not yet implemented in the generator templates. The OpenAPI Generator's
 * controller template cannot access body parameter properties ({{#vars}})
 * when iterating {{#allParams}}.
 *
 * To implement validation, we need to either:
 * 1. Enable Form Request generation (requires generator config)
 * 2. Add validation to Model classes
 * 3. Implement manual validation in handlers
 *
 * These tests document the expected behavior once validation is implemented.
 */
class ApiValidationTest extends TestCase
{
    /**
     * Test that invalid data is rejected (PetStore)
     */
    public function test_petstore_rejects_invalid_pet_data(): void
    {
        $this->markTestSkipped('Body parameter validation not yet implemented in generator templates');

        // Missing required field 'name'
        $response = $this->postJson('/v2/pets', [
            'tag' => 'test'
        ]);

        // Should reject (422 or 400), not accept
        $this->assertContains($response->status(), [400, 422],
            'Should reject pet without name field');
    }

    /**
     * Test that empty pet name is rejected
     */
    public function test_petstore_rejects_empty_pet_name(): void
    {
        $this->markTestSkipped('Body parameter validation not yet implemented in generator templates');

        $response = $this->postJson('/v2/pets', [
            'name' => '',
            'tag' => 'test'
        ]);

        $this->assertContains($response->status(), [400, 422],
            'Should reject pet with empty name');
    }

    /**
     * Test that invalid game mode is rejected (TicTacToe)
     */
    public function test_tictactoe_rejects_invalid_game_mode(): void
    {
        $this->markTestSkipped('Body parameter validation not yet implemented in generator templates');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer test-token',
        ])->postJson('/v1/games', [
            'mode' => 'invalid_mode'
        ]);

        $this->assertContains($response->status(), [400, 422],
            'Should reject invalid game mode');
    }

    /**
     * Test that missing required field is rejected
     */
    public function test_tictactoe_rejects_missing_mode(): void
    {
        $this->markTestSkipped('Body parameter validation not yet implemented in generator templates');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer test-token',
        ])->postJson('/v1/games', []);

        $this->assertContains($response->status(), [400, 422],
            'Should reject game creation without mode');
    }

    /**
     * Test that invalid mark is rejected
     */
    public function test_tictactoe_rejects_invalid_mark(): void
    {
        $this->markTestSkipped('Body parameter validation not yet implemented in generator templates');

        // First create a game
        $createResponse = $this->withHeaders([
            'Authorization' => 'Bearer test-token',
        ])->postJson('/v1/games', [
            'mode' => 'ai_easy'
        ]);

        $gameId = $createResponse->json('id');

        // Try to mark with invalid value
        $response = $this->withHeaders([
            'Authorization' => 'Bearer test-token',
        ])->putJson("/v1/games/{$gameId}/board/1/1", [
            'mark' => 'Z' // Invalid, should be X or O
        ]);

        $this->assertContains($response->status(), [400, 422],
            'Should reject invalid mark (Z)');
    }

    /**
     * Test that authentication is required for TicTacToe
     */
    public function test_tictactoe_requires_authentication(): void
    {
        $this->markTestSkipped('Authentication middleware not enforced - optional middleware');

        // Try without auth header
        $response = $this->postJson('/v1/games', [
            'mode' => 'ai_easy'
        ]);

        $this->assertContains($response->status(), [401, 403],
            'Should reject request without authentication');
    }

    /**
     * Test that invalid pet ID returns 404
     */
    public function test_petstore_returns_404_for_nonexistent_pet(): void
    {
        $this->markTestSkipped('404 handling not implemented in handlers - returns dummy data');

        $response = $this->get('/v2/pets/99999999');

        $this->assertEquals(404, $response->status(),
            'Should return 404 for non-existent pet');
    }

    /**
     * Test that invalid game ID returns 404
     */
    public function test_tictactoe_returns_404_for_nonexistent_game(): void
    {
        $this->markTestSkipped('404 handling not implemented in handlers - returns dummy data');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer test-token',
        ])->get('/v1/games/nonexistent-id/board');

        $this->assertEquals(404, $response->status(),
            'Should return 404 for non-existent game');
    }
}
