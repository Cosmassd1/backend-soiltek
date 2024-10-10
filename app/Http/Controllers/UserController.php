<?php

namespace App\Http\Controllers;

use App\Models\UserTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function userDetails()
    {
        // Fetch user information along with their location details
        $userInformation = DB::table('user_table as u')
            ->join('location_table as l', 'u.user_id', '=', 'l.user_id')
            ->select(
                'u.user_id',
                DB::raw("CONCAT(u.first_name, ' ', u.surname) AS name"), // Combine first_name and surname
                'u.email',
                DB::raw("DATE(u.created_at) AS created_at"), // Get only the date part of created_at
                'l.district',
                'l.physical_address'
            )
            ->get();
    
        return response()->json($userInformation);
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'national_id' => 'required|string|max:25',
            'first_name' => 'required|string|max:65',
            'middle_name' => 'nullable|string|max:65',
            'surname' => 'required|string|max:65',
            'phone_number' => 'required|string|max:15',
            'email' => 'required|string|email|max:65|unique:user_table',
            // 'password' => 'required|string|min:8',
        ]);

        // Hash the password
        $validatedData['password'] = bcrypt($validatedData['password']);

        // Create the user
        $user = UserTable::create($validatedData);

        // Return a JSON response with the created user
        return response()->json($user, 201); // 201 Created
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = UserTable::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = UserTable::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        // Validate the incoming request data
        $validatedData = $request->validate([
            'national_id' => 'sometimes|required|string|max:25',
            'first_name' => 'sometimes|required|string|max:65',
            'middle_name' => 'nullable|string|max:65',
            'surname' => 'sometimes|required|string|max:65',
            'phone_number' => 'sometimes|required|string|max:15',
            'password' => 'nullable|required|string|min:8',
        ]);

        // Hash the password if it's being updated
        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        // Update the user
        $user->update($validatedData);

        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = UserTable::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }

    public function getUsersWithLocation()
    {
        // Fetch users with their associated location
        $users = UserTable::with('location')->get();

        return response()->json($users);
    }
}
