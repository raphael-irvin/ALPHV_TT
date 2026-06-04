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

    public function test_api_deny_invalid_data_on_create()
    {
        $admin = User::factory()->create();
        Sanctum::actingAs($admin);

        $invalidPayload = ['name' => 'Valid Name', 'shape' => 'hexagon', 'color' => 'invisible'];

        $response = $this->postJson('/api/records', $invalidPayload);

        $response->assertStatus(422); // Unprocessable Entity due to validation errors
    }

    public function test_api_deny_invalid_data_on_update()
    {
        $admin = User::factory()->create();
        Sanctum::actingAs($admin);

        $record = Record::create([
            'name'  => 'Test Shape',
            'shape' => 'square',
            'color' => 'blue'
        ]);

        $invalidUpdatePayload = ['name' => 'Still Valid Name', 'shape' => 'pentagon', 'color' => 'invisible'];

        $response = $this->putJson('/api/records/' . $record->id, $invalidUpdatePayload);

        $response->assertStatus(422); // Unprocessable Entity due to validation errors
    }

    // === TESTS FOR PAGINATION ===

    public function test_records_index_returns_paginated_response_structure()
    {
        // Create 15 records → 2 pages (10 on page 1, 5 on page 2)
        Record::factory()->count(15)->create();

        $response = $this->getJson('/api/records');

        $response->assertStatus(200)
                 // Verify the standard Laravel paginator envelope fields are all present
                 ->assertJsonStructure([
                     'data',
                     'current_page',
                     'last_page',
                     'per_page',
                     'total',
                     'from',
                     'to',
                     'next_page_url',
                     'prev_page_url',
                 ])
                 // Page 1 of 2, 10 records per page, 15 total
                 ->assertJsonPath('current_page', 1)
                 ->assertJsonPath('last_page', 2)
                 ->assertJsonPath('per_page', 10)
                 ->assertJsonPath('total', 15)
                 // Exactly 10 records in the 'data' array on the first page
                 ->assertJsonCount(10, 'data');
    }

    public function test_records_index_page_2_returns_correct_slice()
    {
        // Create 15 records → page 2 should contain exactly 5
        Record::factory()->count(15)->create();

        $response = $this->getJson('/api/records?page=2');

        $response->assertStatus(200)
                 ->assertJsonPath('current_page', 2)
                 ->assertJsonPath('last_page', 2)
                 // 5 records remain on the last page (15 total − 10 on page 1)
                 ->assertJsonCount(5, 'data');
    }
}