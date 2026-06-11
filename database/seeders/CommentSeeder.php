<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = Post::all();
        $users = User::all();

        Comment::factory(30)->create([
            'post_id' => function () use ($posts) {
                return fake()->randomElement($posts)->id;
            },

            'user_id' => function () use ($users) {
                return fake()->boolean() ? fake()->randomElement($users)->id : null;
            },

            'guest_name' => function (array $attributes) {
                return $attributes['user_id'] ? null : fake()->numerify('guest_####');
            },
        ]);
    }
}
