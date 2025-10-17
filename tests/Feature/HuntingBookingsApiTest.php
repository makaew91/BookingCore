<?php

namespace Tests\Feature;

use App\Models\Guide;
use App\Models\HuntingBooking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HuntingBookingsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_booking_success(): void
    {
        $guide = Guide::factory()->create();

        $payload = [
            'tour_name' => 'Duck Hunt',
            'hunter_name' => 'John Doe',
            'guide_id' => $guide->id,
            'date' => now()->addDay()->toDateString(),
            'participants_count' => 3,
        ];

        $this->postJson('/api/bookings', $payload)
            ->assertCreated()
            ->assertJsonPath('data.tour_name', 'Duck Hunt');

        $this->assertDatabaseHas('hunting_bookings', [
            'tour_name' => 'Duck Hunt',
            'guide_id' => $guide->id,
        ]);
    }

    public function test_validate_participants_limit(): void
    {
        $guide = Guide::factory()->create();
        $payload = [
            'tour_name' => 'Boar Hunt',
            'hunter_name' => 'Jane Doe',
            'guide_id' => $guide->id,
            'date' => now()->addDays(2)->toDateString(),
            'participants_count' => 11,
        ];

        $this->postJson('/api/bookings', $payload)
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['participants_count']]);
    }

    public function test_validate_guide_availability_on_same_date(): void
    {
        $guide = Guide::factory()->create();
        $date = now()->addDays(3)->toDateString();
        HuntingBooking::factory()->create([
            'guide_id' => $guide->id,
            'date' => $date,
        ]);

        $payload = [
            'tour_name' => 'Boar Hunt',
            'hunter_name' => 'Jane Doe',
            'guide_id' => $guide->id,
            'date' => $date,
            'participants_count' => 3,
        ];

        $this->postJson('/api/bookings', $payload)
            ->assertStatus(422)
            ->assertJsonStructure(['message', 'errors' => ['date']]);
    }
}


