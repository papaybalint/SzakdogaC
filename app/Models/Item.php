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
        'inventory_number',
        'barcode',
        'isbn',
        'year_of_purchasing',
        'publication_date',
        'supplier',
        'categories_id',


    ];

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class, "categories_id");
    }
}
