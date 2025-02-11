<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admin = Admin::all();

        return response()->json([ 'admin' => $admin, 200]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdminRequest $request)
    {
        $validated = $request->validated();
        $admin = Admin::create($validated);
        return response()->json([ 'admin' => $admin, 201]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json([ 'message' => ' Admin not found'],404);
        }
        return response()->json([ 'admin' => $admin, 200]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Admin $admin)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($request, $id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json([ 'message' => ' Admin not found'],404);
        }
        $validated = $request->validated();
        $admin->update($validated);
        return response()->json([ 'admin' => $admin, 200]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json([ 'message' => ' Admin not found'],404);
        }
        $admin->delete();
        return response()->json([ 'message' => 'Admin deleted successfully', 200]);
    }
   
}
