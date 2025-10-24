<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::insert([
    [
        'name' => 'Wave-120 Engine',
        'price' => '4500',
        'instock' => '10',
        'description' => 'Original condition, brand new out of the box',
        'image_url' => '',
        'category_id' => '',
    ],
    [
        'name' => 'Wave-125R Engine',
        'price' => '6500',
        'instock' => '10',
        'description' => 'Good condition, ready to use',
        'image_url' => '',
        'category_id' => '',
    ],
    [
        'name' => 'Wave-125 Engine (No Documents)',
        'price' => '4000',
        'instock' => '10',
        'description' => 'Used engine without registration papers',
        'image_url' => '',
        'category_id' => '',
    ],
    [
        'name' => 'Wave-110 Engine (Not Working)',
        'price' => '1200',
        'instock' => '10',
        'description' => 'Engine not running, needs repair',
        'image_url' => '',
        'category_id' => '',
    ],
]);
    }
}
