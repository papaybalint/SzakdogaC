<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();

        return response()->json(['users' => $users, 200]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $users = User::create($validated);
        return response()->json(['users' => $users, 201]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => ' User not found'], 404);
        }
        return response()->json(['user' => $user, 200]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::find($id);
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
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => ' User not found'], 404);
        }
        if (Auth::id() === $id) {
            return response()->json(['message' => 'Nem törölheted a saját fiókodat!'], 403);
        }
        try {
            $user->delete();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 406);
        }
        return response()->json(['message' => 'User deleted successfully', 200]);
    }
}
