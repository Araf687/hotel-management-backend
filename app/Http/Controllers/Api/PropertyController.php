<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PropertyController extends Controller
{
    /**
     * Display a listing of the properties.
     */
    public function index(Request $request)
    {
        // Get the 'per_page' value from the request, default to null if not provided
        $perPage = $request->get('per_page', null);
    
        if ($perPage) {
            // If 'per_page' is provided, paginate the data
            $properties = Property::paginate($perPage);
        } else {
            // If 'per_page' is not provided, get all the data without pagination
            $properties = Property::all();
        }
    
        return response()->json($properties);
    }
    

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Ensure valid image type and size
            'available_rooms' => 'required|integer',
            'per_night_cost' => 'required|numeric',
            'average_rating' => 'nullable|numeric|min:1|max:5',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $imagename = null;

        echo request()->image;
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $path = $image->getClientOriginalExtension();
            $imagename = time() . '.' . $path;
            $image->move(public_path('uploads/blogs/images'), $imagename);
        }

        echo "asa";
        return;
        // Create the new property with the image path
        $property = Property::create([
            'name' => $request->name,
            'address' => $request->address,
            'image' => $imagename,  // Store the image path in the database
            'available_rooms' => $request->available_rooms,
            'per_night_cost' => $request->per_night_cost,
            'average_rating' => $request->average_rating,
            'description' => $request->description,
        ]);

        // Return the created property with a 201 status
        return response()->json($property, 201);
    }


    /**
     * Display the specified property.
     */
    public function show(string $id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json(['error' => 'Property not found'], 404);
        }

        // Get the full URL of the image if it exists
        $property->image_url = $property->image ? asset('storage/' . $property->image) : null;

        return response()->json($property);  // Return the property with the image URL
    }

    /**
     * Update the specified property in storage.
     */
    public function update(Request $request, string $id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json(['error' => 'Property not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Ensure valid image type and size
            'available_rooms' => 'required|integer',
            'per_night_cost' => 'required|numeric',
            'average_rating' => 'nullable|numeric|min:1|max:5',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Handle the image upload if present
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($property->image) {
                Storage::disk('public')->delete($property->image);
            }

            // Store the new image and get the path
            $imagePath = $request->file('image')->store('properties', 'public');
            $property->image = $imagePath;  // Update the image path in the database
        }

        // Update the other properties
        $property->update($request->except('image'));  // Exclude the image field if it's being handled separately

        return response()->json($property);
    }

    /**
     * Remove the specified property from storage.
     */
    public function destroy(string $id)
    {
        $property = Property::find($id);

        if (!$property) {
            return response()->json(['error' => 'Property not found'], 404);
        }

        // Delete the image from storage if it exists
        if ($property->image) {
            Storage::disk('public')->delete($property->image);
        }

        // Delete the property
        $property->delete();

        return response()->json(['message' => 'Property deleted successfully']);
    }
}
