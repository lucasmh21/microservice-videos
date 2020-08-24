<?php

namespace Tests\Feature\Http\Controllers\api;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\ValidationTrait;

class VideoControllerTest extends TestCase
{
    use ValidationTrait;
    use DatabaseMigrations;

    protected function model()
    {
        return Video::class;
    }

    protected function routeStore()
    {
        return route('videos.store');
    }

    protected function routeUpdate(array $parameters)
    {
        return route('videos.update',['video' => $parameters['id']]);
    }

    protected function routeDelete(array $parameters)
    {
        return route('videos.destroy',['video' => $parameters['id']]);
    }

    protected function routeShow(array $parameters)
    {
        return route('videos.show',['video' => $parameters['id']]);
    }

    protected function createGenericModel()
    {
        /** @var Video $video */
        $video = factory($this->model())->create();
        $video->categories()->saveMany(factory(Category::class, rand(1,3))->create());
        $video->genres()->saveMany(factory(Genre::class, rand(1,3))->create());
        $video->refresh();
        return $video;
    }

    protected function getTestCase(): TestCase
    {
        return $this;
    }
    public function testCreate()
    {
        /** @var Category $category */
        $category = factory(Category::class)->create();
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create();
        $genre->categories()->save($category);
        $data = [
            'title' => 'The Witcher EP 1 Temp 1',
            'description' => 'Nois que avoa bruxao',
            'year_launched' => 2019,
            'opened' => true,
            'rating' => '18',
            'duration' => 60,
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ];
        return $this->assertCreate($data,['categories_id' => 'categories', 'genres_id' =>'genres']);
    }

    public function testUpdate()
    {
        /** @var Category $category */
        $category = factory(Category::class)->create();
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create();
        $genre->categories()->save($category);
        $data = [
            'title' => 'Harry Potter',
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ];
        $this->assertUpdate($data,['categories_id' => 'categories', 'genres_id' =>'genres']);
    }

    /** @test */
    public function testeDelete()
    {
        $this->assertDestroy();
    }

    /** @test */
    public function testIndex()
    {
        $this->assertIndex(route('videos.index'));
    }


    public function testShow()
    {
        $this->assertShow();
    }

    /** @test */
    public function testEmptyFields()
    {
        $this->assertEmptyFieldsStore(
            [
                'title',
                'description',
                'year launched',
                'rating',
                'duration',
                'categories id',
                'genres id'],
            [
                'opened'
            ]
        );
        $this->assertEmptyFieldsStore(
            [
                'title',
                'description',
                'year launched',
                'rating',
                'duration',
                'categories id',
                'genres id'],
            [
                'opened'
            ]
        );
    }

    /** @test */
    public function testGenreRelationship()
    {
        $this->verifyGenreRelationshipStore();
        $this->verifyGenreRelationshipUpdate();
    }

    private function verifyGenreRelationshipStore()
    {
        /** @var Category $category */
        $category = factory(Category::class)->create();
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create();
        $data = [
            'title' => 'The Witcher EP 1 Temp 1',
            'description' => 'Nois que avoa bruxao',
            'year_launched' => 2019,
            'opened' => true,
            'rating' => '18',
            'duration' => 60,
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ];
        $response = $this->json('POST',$this->routeStore(),$data);
        $response->assertStatus(422);
    }

    private function verifyGenreRelationshipUpdate()
    {
        /** @var Category $category */
        $category = factory(Category::class)->create();
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create();
        $data = [
            'title' => 'Harry Potter',
            'categories_id' => [$category->id],
            'genres_id' => [$genre->id]
        ];

        $modelObject = $this->createGenericModel();
        $arrayObject = $modelObject->toArray();

        foreach ($data as $key => $value) {
            $arrayObject[$key] = $value;
        }

        $response = $this->json('PUT',$this->routeUpdate($modelObject->toArray()),$data);
        $response->assertStatus(422);
    }
}
