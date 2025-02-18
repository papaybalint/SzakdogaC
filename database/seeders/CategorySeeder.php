<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::truncate();
        $csvFile = fopen(base_path('database\\data\\leltari_jegyzek_kategoria.csv'), 'r');
        $firstline = true;
        $processed = [];
        while (($data = fgetcsv($csvFile, 2000, ',')) !== false) {
            if ($data[1] === '???') {
                $data[1] = '';
            }

            if ($firstline) {
                $firstline = false;
                continue;
            }

            $recordKey = $data[0] .' ### '. $data[1];
            if (in_array($recordKey, $processed)) {
                continue;
            }
            echo implode(',', $data);

            Category::create([
                'name' => $data[0],
                'media_type' => $data[1],
            ]);
            $processed[] = $recordKey;
            echo ' - saved',"\n";
        }
        fclose($csvFile);
    }
}
