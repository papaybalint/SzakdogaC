<?php

namespace App\Http\Controllers;

use App\Models\BorrowingMedia;
use App\Http\Requests\StoreBorrowingMediaRequest;
use App\Http\Requests\UpdateBorrowingMediaRequest;

class BorrowingMediaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $borrowingMedia = BorrowingMedia::all();

        return response()->json([ 'borrowingMedia' => $borrowingMedia, 200]);
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
    public function store(StoreBorrowingMediaRequest $request)
    {
        $validated = $request->validated();
        $borrowingMedia = BorrowingMedia::create($validated);
        return response()->json([ 'borrowingMedia' => $borrowingMedia, 201]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $borrowingMedia = BorrowingMedia::find($id);
        if (!$borrowingMedia) {
            return response()->json([ 'message' => ' BorrowingMedia not found'],404);
        }
        return response()->json([ 'borrowingMedia' => $borrowingMedia, 200]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BorrowingMedia $borrowingMedia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, $request)
    {
        $borrowingMedia = BorrowingMedia::find($id);
        if (!$borrowingMedia) {
            return response()->json([ 'message' => ' BorrowingMedia not found'],404);
        }
        $validated = $request->validated();
        $borrowingMedia->update($validated);
        return response()->json([ 'borrowingMedia' => $borrowingMedia, 200]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $borrowingMedia = BorrowingMedia::find($id);
        if (!$borrowingMedia) {
            return response()->json([ 'message' => ' BorrowingMedia not found'],404);
        }
        $borrowingMedia->delete();
        return response()->json([ 'message' => 'BorrowingMedia deleted successfully', 200]);
    }
}
