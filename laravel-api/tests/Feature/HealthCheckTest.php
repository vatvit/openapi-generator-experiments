<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /**
     * Test API health endpoint (API-only application).
     */
    public function test_health_endpoint_returns_healthy_status(): void
    {
        $response = $this->get('/api/health');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'healthy'
                 ]);
    }
}
