<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Illuminate\Http\Request;

class RecordController extends Controller
{
    // 1. READ: Fetch all records for the User Portal Grid
    public function index()
    {
        // Grabs everything from the 'records' table and sends it back as JSON
        $records = Record::all();
        return response()->json($records);
    }

    // 2. CREATE: Save new data from the Admin Portal
    public function store(Request $request)
    {
        // STRICT VALIDATION: Ensures no empty fields and validates specific choices
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'shape' => 'required|string',
            'color' => 'required|string',
        ]);

        // If validation passes, create a new row in the database
        $record = Record::create($validatedData);

        // Return a success response with the newly created record
        return response()->json([
            'message' => 'Record successfully created!',
            'data' => $record
        ], 201);
    }

    // 3. UPDATE: Edit existing data
    public function update(Request $request, $id)
    {
        // Find the specific record by its ID, or fail if it doesn't exist
        $record = Record::findOrFail($id);
        
        // Validate the new incoming data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'shape' => 'required|string',
            'color' => 'required|string',
        ]);

        // Update the database and save
        $record->update($validatedData);

        return response()->json([
            'message' => 'Record successfully updated!', 
            'data' => $record
        ]);
    }

    // 4. DELETE: Remove data
    public function destroy($id)
    {
        // Find the specific record and delete it
        $record = Record::findOrFail($id);
        $record->delete();

        return response()->json(['message' => 'Record successfully deleted!']);
    }
}