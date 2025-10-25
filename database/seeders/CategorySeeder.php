<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $categories = ['Engine', 'Frame', 'Rear Suspension', 'Power Pipe', 'Spoken Wheel'];

        foreach ($categories as $name) {
            Category::create(['category_name' => $name]);
        }
    }
}
