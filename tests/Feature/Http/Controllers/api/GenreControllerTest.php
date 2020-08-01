<?php

namespace Tests\Feature\Http\Controllers\api;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function testCreate()
    {
        $data = [
            'name' => 'Test',
        ];
        $response = $this->json('POST', route('genres.store'), $data);
        $response->assertStatus(201)
                 ->assertJson($data)
                 ->assertJsonMissingValidationErrors('is_active');
    }

    public function testUpdate()
    {
        $genre = factory(\App\Models\Genre::class)->create();
        $data = [
            'name' => 'NewName',
            'is_active' => false
        ];
        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), $data);
        $response->assertStatus(200)
                 ->assertJson($data);
    }

    public function testDelete()
    {
        $genre = factory(\App\Models\Genre::class)->create();
        $response = $this->json('DELETE', route('genres.destroy',['genre' => $genre->id]));
        $response->assertStatus(204);
    }


    public function testIndex()
    {
        $genre = new Genre();
        factory(\App\Models\Genre::class,10)->create();
        $response = $this->get(route('genres.index'));
        $response->assertStatus(200)
                 ->assertJson($genre->toArray());
    }

    public function testShow()
    {
        $genre = factory(\App\Models\Genre::class)->create();
        $response = $this->get(route('genres.show', ['genre' => $genre->id]));
        $response->assertStatus(200)
                 ->assertJson($genre->toArray());
    }

    public function testInvalidData()
    {
        $this->checkEmptyFields();
        $this->checkFieldsLimit();
        $this->checkWrongData();
        $this->checkDuplicate();
    }

    public function checkFieldsLimit()
    {
        $data = [
            'name' => str_repeat('a', 256)
        ];
        $validFieldLimit = function($method, $route, $data){
            $response = $this->json($method,$route, $data);
            $response->assertStatus(422)
                     ->assertJsonFragment([
                         Lang::get('validation.max.string',['attribute' => 'name', 'max' => 255])
                     ]);
        };
        $validFieldLimit('POST', route('genres.store'), $data);
        $genre = factory(\App\Models\Genre::class)->create();
        $validFieldLimit('PUT', route('genres.update', ['genre' => $genre->id]), $data);
    }

    public function checkEmptyFields()
    {
        $data = ['name' => ''];
        $validEmptyField = function($method, $route, $data){
            $response = $this->json($method, $route, $data);
            $response->assertStatus(422)
                     ->assertJsonFragment([
                         Lang::get('validation.required',['attribute' => 'name'])
                     ])
                     ->assertJsonMissingValidationErrors('is_active');
        };
        $validEmptyField('POST', route('genres.store'), $data);
        $genre = factory(\App\Models\Genre::class)->create();
        $validEmptyField('PUT', route('genres.update',['genre' => $genre->id]), $data);
    }

    public function checkWrongData()
    {
        $data = [
            'name' => true,
            'is_active' => 'name'
        ];
        $validWrongData = function($method, $route, $data){
            $response = $this->json($method, $route, $data);
            $response->assertStatus(422)
                     ->assertJsonFragment([
                         Lang::get('validation.string', ['attribute' => 'name'])
                     ])
                     ->assertJsonFragment([
                         Lang::get('validation.boolean', ['attribute' => 'is active'])
                     ]);
        };
        $validWrongData('POST', route('genres.store'), $data);
        $genre = factory(\App\Models\Genre::class)->create();
        $validWrongData('PUT', route('genres.update',['genre' => $genre->id]), $data);
    }

    public function checkDuplicate()
    {
        $data = ['name' => 'test'];
        $validDuplicate = function ($method, $route, $data){
            $response = $this->json($method, $route, $data);
            $response->assertStatus(422)
                     ->assertJsonFragment([
                         Lang::get('validation.unique',['attribute' => 'name'])
                     ]);
        };
        $this->json('POST', route('genres.store'), $data);
        $validDuplicate('POST', route('genres.store'), $data);
        $genre = factory(\App\Models\Genre::class)->create();
        $validDuplicate('PUT', route('genres.update',['genre' => $genre->id]),$data);
    }
}
