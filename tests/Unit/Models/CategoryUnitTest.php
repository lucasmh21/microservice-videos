<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class CategoryUnitTest extends TestCase
{
    use DatabaseMigrations;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $category = new Category();
        $this->setUpFaker();
    }
    public function testCreateCategory()
    {
        $response = $this->post(route('categories.store'), $this->createFieldsCategory());
        $this->assertTrue(Uuid::isValid($response->decodeResponseJson('id')));
        $response->assertStatus(201);
    }
    public function testUpdateCategory()
    {
        $category = Category::create($this->createFieldsCategory());
        $response = $this->put(route('categories.update',['category' => $category->id]),$this->createFieldsCategory());
        $response->assertStatus(200);
    }
    public function testDelete()
    {
        $category = Category::create($this->createFieldsCategory());
        $response = $this->delete(route('categories.destroy',['category' => $category->id]));
        $response->assertStatus(204);
    }

    private function createFieldsCategory()
    {
        return [
            'name' => $this->faker->name,
            'description' => rand (0,4) < 4 ? $this->faker->sentence:null,
            'is_active' => rand(0,4) > 0];
    }
}
