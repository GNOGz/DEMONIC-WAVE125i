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
        $categories = [
            ['name' => 'Engine', 'description' => 'The core power source that drives performance and energy.'],
            ['name' => 'Frame', 'description' => 'The structural backbone that ensures stability and support.'],
            ['name' => 'Rear Suspension', 'description' => 'Provides comfort and control, absorbing shocks from every journey.'],
            ['name' => 'Power Pipe', 'description' => 'Enhances performance and efficiency, channeling energy effectively.'],
            ['name' => 'Spoke Wheel', 'description' => 'A lightweight, sturdy spoke wheel that ensures smooth rotation and evenly distributes load.'],
        ];


        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']], // search by unique column
                [
                    'description' => $category['description'],
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ]
            );
        }
    }
}
