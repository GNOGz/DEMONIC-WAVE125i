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
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Product::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $productList = [
            [
                // Engine Category
                'name' => 'Wave-120 Engine',
                'price' => 4500,
                'in_stock' => 10,
                'description' => 'Original condition, brand new out of the box',
                'image_url' => 'engine-1.jpg',
                'category_id' => 1,
            ],
            [
                'name' => 'Wave-125R Engine',
                'price' => 6500,
                'in_stock' => 10,
                'description' => 'Good condition, ready to use',
                'image_url' => 'engine-2.jpg',
                'category_id' => 1,
            ],
            [
                'name' => 'Wave-125 Engine (No Documents)',
                'price' => 4000,
                'in_stock' => 10,
                'description' => 'Used engine without registration papers',
                'image_url' => 'engine-3.jpg',
                'category_id' => 1,
            ],
            [
                'name' => 'Wave-110 Engine (Not Working)',
                'price' => 1200,
                'in_stock' => 10,
                'description' => 'Engine not running, needs repair',
                'image_url' => 'engine-4.jpg',
                'category_id' => 1,
            ],
            // Frame Category
            [
                'name' => 'Wave 110 Frame',
                'price' => 700,
                'in_stock' => 10,
                'description' => 'Good condition frame for Wave 110 motorcycle.',
                'image_url' => 'frame-1.jpg',
                'category_id' => 2,
            ],
            [
                'name' => 'Wave 110i Original Frame',
                'price' => 1000,
                'in_stock' => 10,
                'description' => 'Original condition frame for Wave 110i motorcycle.',
                'image_url' => 'frame-2.jpg',
                'category_id' => 2,
            ],
            [
                'name' => 'Wave 125i Freshly Disassembled Frame',
                'price' => 9800,
                'in_stock' => 10,
                'description' => 'Recently disassembled frame for Wave 125i, in great shape.',
                'image_url' => 'frame-3.jpg',
                'category_id' => 2,
            ],
            [
                'name' => 'Wave Frame (No Documents)',
                'price' => 3999,
                'in_stock' => 10,
                'description' => 'Wave motorcycle frame without ownership documents.',
                'image_url' => 'frame-4.jpg',
                'category_id' => 2,
            ],
            // Rear Suspension Category
            [
                'name' => 'Gazi Rear Shock Absorbers',
                'price' => 2500,
                'in_stock' => 10,
                'description' => 'Pair of Gazi shocks, perfect for daily ride or light racing setup. Soft rebound, smooth cornering — plug and play for most underbone bikes!',
                'image_url' => 'rear-1.jpg',
                'category_id' => 3,
            ],
            [
                'name' => 'OHLINS Rear Shock (Thailand Spec)',
                'price' => 16500,
                'in_stock' => 10,
                'description' => 'Authentic OHLINS shocks, top-tier setup for street racers. Ultimate control and damping — for riders who love both looks and performance!',
                'image_url' => 'rear-2.jpg',
                'category_id' => 3,
            ],
            [
                'name' => 'Showa Gold-Top Rear Shocks (Full Set)',
                'price' => 12900,
                'in_stock' => 10,
                'description' => 'Original Showa gold-top shocks, freshly rebuilt and tuned. Shiny look, strong rebound, ready to ride — rare set for collectors or pro street bikes!',
                'image_url' => 'rear-3.jpg',
                'category_id' => 3,
            ],
            [
                'name' => 'Para Suspension Set (With Seals)',
                'price' => 2250,
                'in_stock' => 10,
                'description' => 'Full Para fork set, new seals installed and ready to use. Smooth stroke and stylish finish — perfect match for Wave, Dream, or any Thai street racer setup!',
                'image_url' => 'rear-4.jpg',
                'category_id' => 3,
            ],
            // Power pipe Category
            [
                'name' => 'Chan-Tang Racing Exhaust (Good Condition)',
                'price' => 400,
                'in_stock' => 10,
                'description' => 'Legendary Chan-Tang pipe, still in good shape. Deep tone, loud enough to wake the whole neighborhood — perfect for Wave or Dream setups!',
                'image_url' => 'pipe-1.jpg',
                'category_id' => 4,
            ],
            [
                'name' => 'Naj Racing Exhaust (Cut Muffler Style)',
                'price' => 600,
                'in_stock' => 10,
                'description' => 'Naj-style “Pha Mok” pipe, loud and mean. Stainless body with clean welds — a real street sound for Thai-style racers!',
                'image_url' => 'pipe-2.jpg',
                'category_id' => 4,
            ],
            [
                'name' => 'Whale Exhaust for 56-59mm Bore',
                'price' => 700,
                'in_stock' => 10,
                'description' => 'Whale-style performance pipe, made for 56-59mm pistons. Smooth pull, killer sound, and eye-catching curve — ready to roar!',
                'image_url' => 'pipe-3.jpg',
                'category_id' => 4,
            ],
            [
                'name' => 'Pha Mok 28mm Neck Exhaust (Wave 110i Old Block)',
                'price' => 1100,
                'in_stock' => 10,
                'description' => 'Pha Mok exhaust with 28mm neck — tight bass tone, tuned for old-block Wave 110i. Clean welds, aggressive sound, and ready for swaps!',
                'image_url' => 'pipe-4.jpg',
                'category_id' => 4,
            ],
            // Spoke Wheel Category
            [
                'name' => 'ARC Vector Rims (Gold Painted)',
                'price' => 6600,
                'in_stock' => 10,
                'description' => 'ARC Vector wheels, gold color painted by owner — not real gold but shines like one! Clean set, no cracks, perfect for Thai street style builds.',
                'image_url' => 'wheel-1.jpg',
                'category_id' => 5,
            ],
            [
                'name' => 'Wave 110 Wheel Set (Freshly Removed)',
                'price' => 1100,
                'in_stock' => 10,
                'description' => 'Wheel set just taken off from Wave 110 — selling to grab some food money. Still straight and true, ready to install and ride!',
                'image_url' => 'wheel-2.jpg',
                'category_id' => 5,
            ],
            [
                'name' => 'Wave 110 Wheel Set (Freshly Painted)',
                'price' => 1200,
                'in_stock' => 10,
                'description' => 'Freshly repainted Wave 110 wheel set — glossy finish, looks brand new. Plug and play for daily ride or custom bike projects!',
                'image_url' => 'wheel-3.jpg',
                'category_id' => 5,
            ],
            [
                'name' => 'Original Knight 110 Mag Wheels (Orange)',
                'price' => 3600,
                'in_stock' => 10,
                'description' => 'Genuine Knight 110 orange mags — original color, tight bearing hubs, zero bend or wobble. Premium set for show bikes or smooth riders!',
                'image_url' => 'wheel-4.jpg',
                'category_id' => 5,
            ]
        ];
        foreach ($productList as $prod) {
        Product::create($prod);
        }
    }
}
