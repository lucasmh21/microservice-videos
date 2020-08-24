<?php

use App\Models\Category;
use Illuminate\Database\Seeder;

class GenreTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Genre::class,10)->create()->each(function($genre){
            $genre->categories()->saveMany(factory(Category::class,rand(1,3))->create());
        });
    }
}
