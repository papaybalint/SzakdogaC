<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Http\Requests\StoreBorrowingRequest;
use App\Http\Requests\UpdateBorrowingRequest;

class BorrowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $borrowing = Borrowing::all();

        return response()->json([ 'borrowing' => $borrowing, 200]);
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
    public function store(StoreBorrowingRequest $request)
    {
        $validated = $request->validated();
        $borrowing = Borrowing::create($validated);
        return response()->json([ 'borrowing' => $borrowing, 201]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $borrowing = Borrowing::find($id);
        if (!$borrowing) {
            return response()->json([ 'message' => ' Borrowing not found'],404);
        }
        return response()->json([ 'borrowing' => $borrowing, 200]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Borrowing $borrowing)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, $request)
    {
        $borrowing = Borrowing::find($id);
        if (!$borrowing) {
            return response()->json([ 'message' => ' Borrowing not found'],404);
        }
        $validated = $request->validated();
        $borrowing->update($validated);
        return response()->json([ 'borrowing' => $borrowing, 200]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $borrowing = Borrowing::find($id);
        if (!$borrowing) {
            return response()->json([ 'message' => ' Borrowing not found'],404);
        }
        $borrowing->delete();
        return response()->json([ 'message' => 'Borrowing deleted successfully', 200]);
    }
    
}
