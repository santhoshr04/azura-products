<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class AddedProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryIds = Category::query()->pluck('id', 'name');

        $catalog = [
            'Electronics' => [
                ['name' => 'Aurora 27" 4K UHD Monitor', 'sku' => 'ELC-1032', 'price' => 329.99],
            ],
            'Home & Kitchen' => [
                ['name' => 'Cedar Pour-Over Coffee Set', 'sku' => 'HMK-2322', 'price' => 54.00],
            ],
            'Apparel' => [
                ['name' => 'Ridgeline Merino Wool Sweater', 'sku' => 'APR-3032', 'price' => 98.00],
            ],
            'Books & Stationery' => [
                ['name' => 'The Pragmatic Architect (Hardcover)', 'sku' => 'BKS-4322', 'price' => 42.00],
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
