<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Http\Requests\StoreBorrowingRequest;
use App\Http\Requests\UpdateBorrowingRequest;
use Illuminate\Support\Facades\Request;
use App\Services\BorrowingService;
use Illuminate\Foundation\Http\FormRequest;

class BorrowingController extends Controller
{

    public function __construct(
        protected BorrowingService $borrowingService
    )
    {
    }
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

     /**
     * Kölcsönzés létrehozása.
     */
    public function borrow(StoreBorrowingRequest $request)
    {
        $request->validate([
            'userId' => 'required|exists:users,id',
            'itemIds' => 'required|array',
            'itemIds.*' => 'exists:items,id',
        ]);

        $borrowing = $this->borrowingService->createBorrowing($request->userId, $request->itemIds);

        return response()->json(['message' => 'Kölcsönzés sikeres!', 'data' => $borrowing]);
    }

    /**
     * Visszahozatal rögzítése.
     */
    public function returnItem(FormRequest $request)
    {
        $request->validate([
            'borrowings_id' => 'required|exists:borrowings,id',
            'items_id' => 'required|exists:items,id',
        ]);

        $this->borrowingService->returnItem($request->input('borrowings_id'), $request->input('items_id'));

        return response()->json(['message' => 'Visszahozatal rögzítve!']);
    }
}


