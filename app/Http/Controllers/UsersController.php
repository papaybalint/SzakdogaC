<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Http\Requests\StoreUsersRequest;
use App\Http\Requests\UpdateUsersRequest;

class UsersController extends Controller
{

    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = Users::all();

        return response()->json(['users' => $users, 200]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUsersRequest $request)
    {
        $validated = $request->validated();
        $users = Users::create($validated);
        return response()->json(['users' => $users, 201]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Users::find($id);
        if (!$user) {
            return response()->json(['message' => ' User not found'], 404);
        }
        return response()->json(['user' => $user, 200]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUsersRequest $request, $id)
    {
        $user = Users::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $validated = $request->validated();
        $user->update($validated);
        return response()->json(['user' => $user], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = Users::find($id);
        if (!$user) {
            return response()->json(['message' => ' User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted successfully', 200]);
    }
}
