<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Borrowing extends Model
{
    /** @use HasFactory<\Database\Factories\BorrowingFactory> */
    use HasFactory;

    protected $fillable = [
        'borrowed_date',
        'return_date',
        'due_date',
        'users_id',
    ];

    protected $guarded = [];

    public function users(): BelongsTo
    {
        return $this->belongsTo(Users::class);
    }
}
