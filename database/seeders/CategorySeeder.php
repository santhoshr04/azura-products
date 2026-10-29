<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Electronics',
            'Home & Kitchen',
            'Apparel',
            'Books & Stationery',
        ];

        foreach ($categories as $name) {
            Category::query()->updateOrCreate(
                ['name' => $name],
                ['status' => Status::Active],
            );
        }
    }
}
