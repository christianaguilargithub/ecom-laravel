<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name'  => 'Admin User',
            'email' => 'admin@example.com',
            'role'  => 'admin',
        ]);

        User::factory()->create([
            'name'  => 'Customer User',
            'email' => 'customer@example.com',
            'role'  => 'customer',
        ]);

        $categories = Category::factory(6)->create();

        $categories->each(function (Category $category) {
            Product::factory(8)->create(['category_id' => $category->id]);
        });
    }
}
