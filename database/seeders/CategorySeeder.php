<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\User;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();

        $categories = [
            ['name' => 'Work', 'color' => '#2563eb'],
            ['name' => 'Personal', 'color' => '#16a34a'],
            ['name' => 'Urgent', 'color' => '#dc2626'],
            ['name' => 'Learning', 'color' => '#9333ea'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name'], 'user_id' => $user->id],
                $category
            );
        }
    }
}
