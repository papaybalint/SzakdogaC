<?php

namespace App\Services;

use App\Models\Borrowing;
use App\Models\BorrowingMedia;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BorrowingService
{
    /**
     * Új kölcsönzés létrehozása.
     *
     * @param int $userId
     * @param array $itemIds
     * @return Borrowing
     */
    public function createBorrowing(int $userId, array $itemIds): Borrowing
{
    // Ellenőrizzük, hogy a kiválasztott tételek már kölcsönzés alatt állnak-e
    $borrowedItems = BorrowingMedia::whereIn('items_id', $itemIds)
                                   ->whereNull('returned_date')  // Csak a nem visszahozott tételek
                                   ->exists();

    if ($borrowedItems) {
        // Ha van ilyen tétel, akkor hibaüzenetet dobunk
        throw new \Exception('Ez a tétel ki van kölcsönözve!');
    }

    return DB::transaction(function () use ($userId, $itemIds) {
        // Kölcsönzés létrehozása
        $borrowing = Borrowing::create([
            'users_id' => $userId,
            'borrowed_date' => Carbon::now()->addHours(2),
            'due_date' => Carbon::now()->addWeeks(2)->addHours(2),
        ]);

        // Hozzáadjuk az összes kölcsönzött médiát
        foreach ($itemIds as $itemId) {
            BorrowingMedia::create([
                'borrowings_id' => $borrowing->id,
                'items_id' => $itemId,
            ]);
        }

        return $borrowing;
    });
}

    /**
     * Visszavétel rögzítése.
     *
     * @param int $borrowingId
     * @param int $itemId
     * @return void
     */
    public function returnItem(int $borrowingId, int $itemId): void
    {
        BorrowingMedia::where('borrowings_id', $borrowingId)
            ->where('items_id', $itemId)
            ->update(['returned_date' => Carbon::now()]);
    }
}
