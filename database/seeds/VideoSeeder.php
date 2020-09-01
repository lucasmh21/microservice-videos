<?php

use App\Models\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Models\Video::class,10)->create()->each(function(Video $video){
            $video->categories()->saveMany(factory(App\Models\Category::class,rand(1,3))->create());
            $video->genres()->saveMany(factory(App\Models\Genre::class, rand(1,3))->create());
        });
    }
}
