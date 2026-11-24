<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_map_tool_pages_use_the_full_screen_map_layout(): void
    {
        foreach ([
            '/pencarian',
            '/klaster',
            '/peta-panas',
            '/wilayah',
            '/rute',
            '/cek-alamat',
        ] as $uri) {
            $this->get($uri)
                ->assertOk()
                ->assertSee('class="map-tool"', false)
                ->assertSee('id="peta"', false);
        }
    }

    public function test_health_check_endpoint(): void
    {
        $this->get('/health')
            ->assertOk()
            ->assertJsonPath('status', 'healthy')
            ->assertJsonPath('database.status', 'connected');
    }
}
