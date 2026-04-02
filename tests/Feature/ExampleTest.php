<?php

namespace Tests\Feature;

use Database\Seeders\DemoEventSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_redirects_to_demo_event(): void
    {
        $this->seed(DemoEventSeeder::class);

        $response = $this->get('/');

        $response->assertRedirect(route('events.show', 'summer-jazz-night-2026'));
    }
}
