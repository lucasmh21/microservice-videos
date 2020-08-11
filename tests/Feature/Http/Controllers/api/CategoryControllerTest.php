<?php

namespace Tests\Feature\Http\Controllers\api;

use App;
use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Lang;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;
use Tests\Traits\ValidationTrait;

class CategoryControllerTest extends TestCase
{
    use ValidationTrait;
    use DatabaseMigrations;
    use WithFaker;

    protected function routeDelete(array $parameters)
    {
        return route('categories.destroy',['category'=>$parameters['id']]);
    }

    protected function routeShow(array $parameters)
    {
        return route('categories.show',['category'=>$parameters['id']]);
    }

    protected function routeStore()
    {
        return route('categories.store');
    }

    protected function routeUpdate(array $parameters)
    {
        return route('categories.update',['category' => $parameters['id']]);
    }
    protected function getTestCase(): TestCase
    {
        return $this;
    }

    protected function model()
    {
        return Category::class;
    }

    protected function setUp(): void
    {
        $this->category = new Category();
        parent::setUp();
    }

    public function testCreate()
    {
        $response = $this->assertCreate(['name' => 'Test']);
        $this->assertTrue(Uuid::isValid($response->json('id')));
    }

    public function testUpdate()
    {
        $data = [
            'name' => 'TestRenamed',
            'is_active' => false,
            'description' => 'Some description'
        ];
        $this->assertUpdate($data);
    }

    public function testIndex()
    {
        $this->assertIndex(route('categories.index'));
    }

    public function testShow()
    {
        $this->assertShow();
    }

    /** @test */
    public function testDestroy()
    {
        $this->assertDestroy();
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
        $this->assertEmptyFieldsStore(['name'],['is_active', 'description']);
        $this->assertEmptyFieldsUpdate(['name'],['is_active', 'description']);
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
        $id = $this->createGenericModel();
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
        $id = $this->createGenericModel();
        $validWrongValues('PUT', route('categories.update',['category' => $id]), $data);
    }

    private function checkDuplicateFields()
    {
        $this->assertDuplicateStore(['name']);
        $this->assertDuplicateUpdate(['name']);
    }
}
