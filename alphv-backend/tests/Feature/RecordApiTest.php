<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Record;
use App\Models\User;
use Laravel\Sanctum\Sanctum; // Import Sanctum to fake the VIP Token

class RecordApiTest extends TestCase
{
    use RefreshDatabase;

    // === TESTS FOR PUBLIC ROUTES ===
    public function test_unauthenticated_user_cannot_modify_records()
    {
        $payload = ['name' => 'Hacker', 'shape' => 'square', 'color' => 'black'];
        $response = $this->postJson('/api/records', $payload);
        $response->assertStatus(401);
    }

    // === TESTS FOR PROTECTED ROUTES ===
    public function test_api_can_create_a_record()
    {
        // Create a fake admin and give them a VIP token
        $admin = User::factory()->create();
        Sanctum::actingAs($admin);

        $payload = [
            'name' => 'Automated Test Triangle',
            'shape' => 'triangle',
            'color' => 'red'
        ];

        // Simulate the POST request
        $response = $this->postJson('/api/records', $payload);

        // Assert that the response is successful and the record is in the database
        $response->assertStatus(201);
        $this->assertDatabaseHas('records', ['name' => 'Automated Test Triangle']);
    }

    public function test_api_can_update_a_record()
    {
        // 1. Log in, and create a fake shape in the DB
        $admin = User::factory()->create();
        Sanctum::actingAs($admin);

        $record = Record::create([
            'name' => 'Original Square',
            'shape' => 'square',
            'color' => 'blue'
        ]);

        $updatePayload = ['name' => 'Updated Circle', 'shape' => 'circle', 'color' => 'green'];

        // Send the PUT request
        $response = $this->putJson('/api/records/' . $record->id, $updatePayload);

        // Assert the response and check the database for the updated record
        $response->assertStatus(200);
        $this->assertDatabaseHas('records', ['name' => 'Updated Circle', 'color' => 'green']);
    }

    public function test_api_can_delete_a_record()
    {
        // 1. Log in, and create a doomed shape
        $admin = User::factory()->create();
        Sanctum::actingAs($admin);

        $record = Record::create([
            'name' => 'Doomed Triangle',
            'shape' => 'triangle',
            'color' => 'red'
        ]);

        // 2. Send the DELETE request
        $response = $this->deleteJson('/api/records/' . $record->id);

        // 3. Assert the response and check the database to ensure it's gone
        $response->assertStatus(200);
        $this->assertDatabaseMissing('records', ['id' => $record->id]);
    }
}