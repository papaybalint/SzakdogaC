<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowingMedia extends Model
{
    /** @use HasFactory<\Database\Factories\BorrowingMediaFactory> */
    use HasFactory;

    protected $fillable = [
        'borrowing_date',
        'return_date',
        'users_id',
        'items_id',
    ];

    protected $guarded = [];

    public function users(): BelongsTo
    {
        return $this->belongsTo(Users::class, "users_id" ,Item::class, "items_id");
    }
}
