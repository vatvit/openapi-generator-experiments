<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test API health endpoint (API-only application).
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/api/v1/health');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'healthy'
                 ]);
    }
}
