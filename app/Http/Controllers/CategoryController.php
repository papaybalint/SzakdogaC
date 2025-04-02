<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all()->load('items');
        return response()->json([ 'categories' => $categories, 200]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();
        $category = Category::create($validated);
        return response()->json([ 'category' => $category, 201]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = Category::find($id)->load('items');
        return response()->json([ 'category' => $category, 200]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, $request)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([ 'message' => 'Category not found'],404);
        }
        $validated = $request->validated();
        $category->update($validated);
        return response()->json([ 'category' => $category, 200]);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        $category->delete();
        return response()->json([ 'category' => $category, 200]);
    }
}
