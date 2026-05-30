<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Record;

class RecordApiTest extends TestCase
{
    // This trait tells Laravel to wipe the fake SQLite database clean before EVERY test
    use RefreshDatabase;

    /**
     * Test that the API can successfully create a new record.
     */
    public function test_api_can_create_a_record()
    {
        // 1. Prepare the fake data payload we want to send
        $payload = [
            'name' => 'Automated Test Triangle',
            'shape' => 'triangle',
            'color' => 'red'
        ];

        // 2. Simulate a frontend Fetch POST request to our API
        $response = $this->postJson('/api/records', $payload);

        // 3. Verify everything worked perfectly
        
        // Assert the API returned a "201 Created" HTTP status code
        $response->assertStatus(201);
        
        // Assert the JSON response contains our success message
        $response->assertJsonFragment([
            'message' => 'Record successfully created!'
        ]);

        // Assert the database physically contains our new shape
        $this->assertDatabaseHas('records', [
            'name' => 'Automated Test Triangle',
            'shape' => 'triangle'
        ]);
    }

    /**
     * Test that the API can successfully update an existing record.
     */
    public function test_api_can_update_a_record()
    {
        // 1. Create an initial record in the fake database
        $record = Record::create([
            'name' => 'Original Square',
            'shape' => 'square',
            'color' => 'blue'
        ]);

        // Prepare the new data we want to change it to
        $updatePayload = [
            'name' => 'Updated Circle',
            'shape' => 'circle',
            'color' => 'green'
        ];

        // 2. Simulate a PUT request to the specific record's ID
        $response = $this->putJson('/api/records/' . $record->id, $updatePayload);

        // 3. Verify the response and database update
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Record successfully updated!'
        ]);
        
        // Check that the database contains the NEW data for this specific ID
        $this->assertDatabaseHas('records', [
            'id' => $record->id,
            'name' => 'Updated Circle',
            'shape' => 'circle',
            'color' => 'green'
        ]);
    }

    /**
     * Test that the API can successfully delete a record.
     */
    public function test_api_can_delete_a_record()
    {
        // 1. Create a temporary record that we plan to destroy
        $record = Record::create([
            'name' => 'Doomed Triangle',
            'shape' => 'triangle',
            'color' => 'red'
        ]);

        // 2. Simulate a DELETE request to that specific ID
        $response = $this->deleteJson('/api/records/' . $record->id);

        // 3. Verify the response and that the data is completely gone
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Record successfully deleted!'
        ]);
        
        // Laravel's built-in assertion to ensure a row NO LONGER exists
        $this->assertDatabaseMissing('records', [
            'id' => $record->id
        ]);
    }
}
