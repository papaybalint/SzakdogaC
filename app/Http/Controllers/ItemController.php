<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $item = Item::all();

        return response()->json([ 'item' => $item, 200]);
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
    public function store(StoreItemRequest $request)
    {
        $validated = $request->validated();
        $item = Item::create($validated);
        return response()->json([ 'item' => $item, 201]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $item = Item::find($id);
        if (!$item) {
            return response()->json([ 'message' => ' Item not found'],404);
        }
        return response()->json([ 'item' => $item, 200]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id, Item $item)
    {
        $item = Item::findOrFail($id);
            return view('item.edit', ['item' => $item]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( $request, $id)
    {
        $item = Item::find($id);
        if (!$item) {
            return response()->json([ 'message' => ' Item not found'],404);
        }
        $validated = $request->validated();
        $item->update($validated);
        return response()->json([ 'item' => $item, 200]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $item = Item::find($id);
        if (!$item) {
            return response()->json([ 'message' => ' Item not found'],404);
        }
        $item->delete();
        return response()->json([ 'message' => 'Item deleted successfully', 200]);
    }

}
