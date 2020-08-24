<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class GenreUnitTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
    }
    public function testCreate()
    {
        $response = $this->post(route('genres.store'),$this->createGenreFields());
        $this->assertTrue(Uuid::isValid($response->decodeResponseJson('id')));
        $response->assertStatus(201);
    }
    public function testUpdate()
    {
        $genre = Genre::create($this->createGenreFields());
        $response = $this->put(route('genres.update', ['genre' => $genre->id]), $this->createGenreFields());
        $response->assertStatus(200);
    }
    public function testDelete()
    {
        $genre = Genre::create($this->createGenreFields());
        $response = $this->delete(route('genres.destroy',['genre' => $genre->id]));
        $response->assertStatus(204);
    }
    public function createGenreFields()
    {
        $genre = new Genre();
        return [
            'name' => $this->faker()->name(),
            'is_active' => rand(0,5) < 5,
            'categories_id' => [factory(Category::class)->create()->id]
        ];
    }
}
