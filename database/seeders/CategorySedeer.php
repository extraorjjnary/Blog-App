<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySedeer extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Career & Work',             'slug' => 'career-work'],
            ['name' => 'Relationships',             'slug' => 'relationships'],
            ['name' => 'Fitness',                   'slug' => 'fitness'],
            ['name' => 'Finance',                   'slug' => 'finance'],
            ['name' => 'Hobbies',                   'slug' => 'hobbies'],
            ['name' => 'Family',                    'slug' => 'family'],
            ['name' => 'Funny Moments',             'slug' => 'funny-moments'],
            ['name' => 'Mental & Physical Health',  'slug' => 'mental-physical-health'],
            ['name' => 'Passion',                   'slug' => 'passion'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
