<?php

namespace Tests\Feature\Http\Controllers\api;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Lang;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    protected function setUp(): void
    {
        $this->category = new Category();
        parent::setUp();
    }


    public function testCreate()
    {
        $data = ['name' => 'Test'];
        $response = $this->post(route('categories.store'), $data);
        $response->assertStatus(201)
                ->assertJson($data, true);
        $this->assertTrue(Uuid::isValid($response->json('id')));
    }

    public function testUpdate()
    {
        $data = [
            'name' => 'TestRenamed',
            'is_active' => false,
            'description' => 'Some description'
        ];
        $category = factory(\App\Models\Category::class)->create(['name' => 'Test']);
        $response = $this->put(route('categories.update',['category' => $category->id]), $data);
        $response->assertStatus(200)
                 ->assertJson($data);
    }

    public function testIndex()
    {
        factory(\App\Models\Category::class,10)->create();
        $response = $this->get(route('categories.index'));
        $response->assertJson($this->category->toArray());
    }

    public function testShow()
    {
        $data = ['name' => 'test'];
        $id = $this->json('POST', route('categories.store'), $data)->json('id');
        $response = $this->json('GET',route('categories.show',['category' => $id]));
        $response->assertJson($data, true);
    }

    /** @test */
    public function testDestroy()
    {
        $id = $this->createGenericCategory();
        $response = $this->json('DELETE', route('categories.destroy',['category' => $id]));
        $response->assertStatus(204);
    }


    public function testInvalidFields()
    {
        $this->checkEmptyFieldsValidation();
        $this->checkFieldsLimit();
        $this->checkWrongValues();
        $this->checkDuplicateFields();
    }

    private function checkEmptyFieldsValidation()
    {
        //GENERIC VALIDATION
        $checkEmptyField = function($method, $route){
            $response = $this->json($method, $route,['name' => null]);
            $response->assertJsonFragment([
                Lang::get('validation.required',['attribute' => 'name'])
            ]);
            $response->assertStatus(422)
                     ->assertJsonMissingValidationErrors('is_active')
                     ->assertJsonMissingValidationErrors('description');
            return $response;
        };
        //CREATE
        $checkEmptyField('POST', route('categories.store'));

        //UPDATE
        $id = $this->createGenericCategory();
        $checkEmptyField('PUT', route('categories.update',['category' => $id]));
    }

    private function checkFieldsLimit()
    {
        $validFieldLimit = function($method, $route, $data){
            $response = $this->json($method, $route, $data);
            $response->assertStatus(422);
            $response->assertJsonFragment([
                Lang::get('validation.max.string',['attribute' => 'name', 'max' => 255])
            ]);
            $response->assertJsonFragment([
                Lang::get('validation.max.string',['attribute' => 'description', 'max' => 255])
            ]);
        };

        $name = str_repeat('a',256);
        $description = str_repeat('z',256);
        $data = [
            'name' => $name,
            'description' => $description
        ];

        $validFieldLimit('POST',route('categories.store'), $data);
        $id = $this->createGenericCategory();
        $validFieldLimit('PUT',route('categories.update',['category' => $id]), $data);
    }

    private function checkWrongValues()
    {
        $validWrongValues = function($method, $route, $data){
            $response = $this->json($method, $route,$data);
            $response->assertStatus(422)
                     ->assertJsonFragment([Lang::get('validation.string',['attribute' => 'name'])])
                     ->assertJsonFragment([Lang::get('validation.string',['attribute' => 'description'])])
                     ->assertJsonFragment([Lang::get('validation.boolean',['attribute' => 'is active'])]);
        };
        $data = [
            'name' => 456,
            'description' => true,
            'is_active' => 'text'
        ];
        $validWrongValues('POST', route('categories.store'), $data);
        $id = $this->createGenericCategory();
        $validWrongValues('PUT', route('categories.update',['category' => $id]), $data);
    }

    private function checkDuplicateFields()
    {
        $data = ['name' => 'test'];
        $response = $this->json('POST',route('categories.store', $data));
        $validDuplicate = function($method, $route, $data){
            $response = $this->json($method,$route,$data);
            $response->assertStatus(422)
                     ->assertJsonFragment([
                         Lang::get('validation.unique',['attribute' => 'name'])
                     ]);
        };
        //CREATE
        $validDuplicate('POST', route('categories.store'), $data);

        //UPDATE
        $response = $this->json('POST',route('categories.store'),[
            'name' => 'test2'
        ]);
        $id = $response->json('id');
        $response = $validDuplicate('PUT', route('categories.update',['category' => $id]), $data);
    }

    private function createGenericCategory(){
        $response = $this->json('POST', route('categories.store'),['name' => $this->faker()->name()]);
        $response->assertStatus(201);
        return $response->json('id');
    }
}
