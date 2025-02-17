<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       Item::truncate();
        $csvFile = fopen(base_path('database\\data\\leltari_jegyzek0.csv'), 'r');
        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000,',')) !== false) {
            if ($firstline) {
                $firstline = false;
               continue;
            }
             //dd($data);break;
            $category= Category::find($data[9]);
            Item::create([
                'author' => $data[0],
                'title' => $data[1],
                'inventory_number' => $data[2],
                'barcode' => $data[3],
                'isbn' => $data[4],
                'year_of_purchasing' => $data[5],
                'published_year' => $data[6],
                'supplier' => $data[7],
                'categories_id' => $category?->id,

            ]);
        }
        fclose($csvFile);
    }

}
