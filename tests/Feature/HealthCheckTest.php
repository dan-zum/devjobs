<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_health_endpoint_responde_up(): void
    {
        $this->getJson('/health')
            ->assertOk()
            ->assertJson(['status' => 'UP', 'database' => 'UP']);
    }
}
