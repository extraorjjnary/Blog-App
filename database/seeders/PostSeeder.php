<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();

        Post::factory(45)->create([
            'user_id' => function () use ($users) {
                return fake()->randomElement($users)->id;
            },
            'category_id' => function () use ($categories) {
                return fake()->randomElement($categories)->id;
            }
        ]);
    }
}
