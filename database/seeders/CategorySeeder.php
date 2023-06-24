<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'Makanan Pembuka',
                'description' => '',
                'image' => '',
            ],
            [
                'name' => 'Hidangan Utama',
                'description' => '',
                'image' => '',
            ],
            [
                'name' => 'Makanan Penutup',
                'description' => '',
                'image' => '',
            ],
            [
                'name' => 'Minuman',
                'description' => '',
                'image' => '',
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}