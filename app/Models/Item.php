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
        'inventory_number',
        'barcode',
        'isbn',
        'year_of_purchasing',
        'published_year',
        'supplier',
        'categories_id',


    ];

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class, "categories_id");
    }
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($item) {
            if ($item->borrowing()->exists()) {
                throw new \Exception("A tartalom nem törölhető, mert van aktív kölcsönzés.");
            }
        });
    }
    public function borrowing()
    {
        return $this->hasOne(BorrowingMedia::class, 'items_id');
    }
}
