<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryIds = Category::query()->pluck('id', 'name');

        $catalog = [
            'Electronics' => [
                ['name' => 'Aurora 27" 4K UHD Monitor', 'sku' => 'ELC-1001', 'price' => 329.99],
                ['name' => 'Nimbus Wireless Mechanical Keyboard', 'sku' => 'ELC-1002', 'price' => 79.50],
                ['name' => 'Pulse Noise-Cancelling Headphones', 'sku' => 'ELC-1003', 'price' => 249.00],
                ['name' => 'Vertex Portable SSD 1TB', 'sku' => 'ELC-1004', 'price' => 119.00],
                ['name' => 'Lumen Smart Desk Lamp', 'sku' => 'ELC-1005', 'price' => 45.75, 'status' => Status::Inactive],
            ],
            'Home & Kitchen' => [
                ['name' => 'Cedar Pour-Over Coffee Set', 'sku' => 'HMK-2001', 'price' => 54.00],
                ['name' => 'Terra Stoneware Dinner Set (16pc)', 'sku' => 'HMK-2002', 'price' => 89.90],
                ['name' => 'Breeze HEPA Air Purifier Mini', 'sku' => 'HMK-2003', 'price' => 139.00],
                ['name' => 'Solace Memory Foam Pillow', 'sku' => 'HMK-2004', 'price' => 39.99],
                ['name' => 'Forge 12" Cast Iron Skillet', 'sku' => 'HMK-2005', 'price' => 42.50, 'status' => Status::Inactive],
            ],
            'Apparel' => [
                ['name' => 'Ridgeline Merino Wool Sweater', 'sku' => 'APR-3001', 'price' => 98.00],
                ['name' => 'Trailhead Waterproof Shell Jacket', 'sku' => 'APR-3002', 'price' => 189.00],
                ['name' => 'Everyday Organic Cotton Tee', 'sku' => 'APR-3003', 'price' => 24.00],
                ['name' => 'Summit Insulated Down Vest', 'sku' => 'APR-3004', 'price' => 115.00],
                ['name' => 'Baseline Canvas Low-Top Sneakers', 'sku' => 'APR-3005', 'price' => 65.00],
            ],
            'Books & Stationery' => [
                ['name' => 'The Pragmatic Architect (Hardcover)', 'sku' => 'BKS-4001', 'price' => 42.00],
                ['name' => 'Field Notes Notebook Set (3-pack)', 'sku' => 'BKS-4002', 'price' => 18.50],
                ['name' => 'Inkwell Brass Fountain Pen', 'sku' => 'BKS-4003', 'price' => 56.00],
                ['name' => 'Atlas Desk Planner 2026', 'sku' => 'BKS-4004', 'price' => 29.00, 'status' => Status::Inactive],
                ['name' => 'Quiet Mind Guided Journal', 'sku' => 'BKS-4005', 'price' => 21.75],
            ],
        ];

        $createdAt = now()->subDays(50);

        foreach ($catalog as $categoryName => $products) {
            foreach ($products as $product) {
                $createdAt = $createdAt->copy()->addDays(2);

                $record = Product::query()->create([
                    'category_id' => $categoryIds[$categoryName],
                    'name' => $product['name'],
                    'sku' => $product['sku'],
                    'price' => $product['price'],
                    'status' => $product['status'] ?? Status::Active,
                ]);

                $record->forceFill([
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ])->save();
            }
        }
    }
}
