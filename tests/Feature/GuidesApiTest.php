<?php

namespace Tests\Feature;

use App\Models\Guide;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuidesApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_only_active_guides_and_filter_by_min_experience(): void
    {
        Guide::factory()->count(2)->create(['experience_years' => 2, 'is_active' => true]);
        Guide::factory()->create(['experience_years' => 5, 'is_active' => true]);
        Guide::factory()->inactive()->create(['experience_years' => 10]);

        $response = $this->getJson('/api/guides?min_experience=3');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.experience_years', 5);
    }
}


