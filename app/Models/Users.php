<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Sanctum\HasApiTokens;

class Users extends Model
{
    /** @use HasFactory<\Database\Factories\UsersFactory> */
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'first_name',
        'last_name',
        'birth_place',
        'birth_date',
        'phone',
        'email',
        'username',
        'password',
    ];
    protected $guarded = [];

    public function borrowings(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }
}

