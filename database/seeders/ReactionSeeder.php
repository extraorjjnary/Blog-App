<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use App\Models\Reaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = Post::all();
        $users = User::all();

        Reaction::factory(50)->create([
            'post_id' => function () use ($posts) {
                return fake()->randomElement($posts)->id;
            },

            'user_id' => function () use ($users) {
                fake()->boolean() ? fake()->randomElement($users)->id : null;
            },

            'guest_identifier' => function (array $attributes) {
                return $attributes['user_id'] ? null : fake()->uuid();
            }
        ]);
    }
}
