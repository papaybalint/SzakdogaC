<?php

namespace Database\Seeders;

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
        $csvFile = fopen(base_path('database/data/leltari_jegyzetek.csv'), 'r');
        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ';')) !== false) {
            if (! $firstline) {
                Item::create([
                    'author' => $data[0],
                    'title' => $data[1],
                    'invertory_number' => $data[2],
                    'barcode' => $data[3],
                    'isbn' => $data[4],
                    'year_of_purchasing' => $data[5],
                    'published_year' => $data[6],
                    'supplier' => $data[7],

                ]);
            }
            $firstline = false;
        }
        fclose($csvFile);
    }

}
