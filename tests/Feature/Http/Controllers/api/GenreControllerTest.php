<?php

namespace Tests\Feature\Http\Controllers\api;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Lang;
use Tests\TestCase;
use Tests\Traits\ValidationTrait;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;
    use ValidationTrait;

    protected function model()
    {
        return Genre::class;
    }

    protected function routeStore()
    {
        return route('genres.store');
    }

    protected function routeUpdate(array $parameters)
    {
        return route('genres.update',['genre' => $parameters['id']]);
    }

    protected function routeDelete(array $parameters)
    {
        return route('genres.destroy',['genre' => $parameters['id']]);
    }

    protected function routeShow(array $parameters)
    {
        return route('genres.show',['genre' => $parameters['id']]);
    }

    protected function getTestCase(): TestCase
    {
        return $this;
    }

    public function testCreate()
    {
        $data = [
            'name' => 'Test',
            'categories_id' => [factory(Category::class)->create()->id]
        ];
        $this->assertCreate($data,['categories_id' =>'categories'])
             ->assertJsonMissingValidationErrors('is_active');
    }

    public function testUpdate()
    {
        $data = [
            'name' => 'NewName',
            'is_active' => false,
            'categories_id' => [factory(Category::class)->create()->id]
        ];
        $this->assertUpdate($data,['categories_id' =>'categories']);
    }

    public function testDelete()
    {
        $this->assertDestroy();
    }


    public function testIndex()
    {
        $this->assertIndex(route('genres.index'));
    }

    public function testShow()
    {
        $this->assertShow();
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
        $this->assertEmptyFieldsStore(['name'],['is_active']);
        $this->assertEmptyFieldsUpdate(['name'],['is_active']);
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
        $this->assertDuplicateStore(['name']);
        $this->assertDuplicateUpdate(['name']);
    }

    protected function createGenericModel()
    {
        /** @var Genre $model */
        $model = factory($this->model())->create();
        $model->categories()->saveMany(factory(Category::class,rand(1,3)));
        return $model;
    }
}
