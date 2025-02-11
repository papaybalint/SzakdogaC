<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    /** @use HasFactory<\Database\Factories\ItemFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'publisher',
        'publication_date',
        'categories_id',
        'quantity',
        'price',
        'description',
        'image',
    ];

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class, "categories_id");
    }
}
