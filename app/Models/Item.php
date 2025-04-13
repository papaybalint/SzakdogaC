<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;

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
                throw new \Exception(Lang::get("messages.item_delete_error"));
            }
        });
    }
    public function borrowing()
    {
        return $this->hasOne(BorrowingMedia::class, 'items_id');
    }
    public function getYearOfPurchasingAttribute($value)
    {
        if (str_contains($value, ".")) {
            return $value;
        }
        return \Carbon\Carbon::parse($value)->format('Y.m.d');
    }
}
