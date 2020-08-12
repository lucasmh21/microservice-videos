<?php

namespace Tests\Feature\Http\Controllers\api;

use App\Models\CastMember;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\ValidationTrait;

class CastMemberControllerTest extends TestCase
{
    use DatabaseMigrations;
    use ValidationTrait;
    public function testCreate()
    {
        $this->assertCreate([
            'name' => 'Test',
            'type' => array_key_first(CastMember::TYPE_VALUES)
            ]);
    }
    public function testUpdate()
    {
        $this->assertUpdate([
            'name' => 'TestUpdated',
            'type' => 2
        ]);
    }

    /** @test */
    public function testDestroy()
    {
        $this->assertDestroy();
    }

    /** @test */
    public function testShow()
    {
        $this->assertShow();
    }

    /** @test */
    public function testIndex()
    {
        $this->assertIndex(route('cast_members.index'));
    }
    public function testInvalidData()
    {
        $this->assertEmptyFieldsStore(['name','type']);
        $this->assertEmptyFieldsUpdate(['name','type']);
    }

    protected function model()
    {
        return CastMember::class;
    }

    protected function routeStore()
    {
        return route('cast_members.store');
    }

    protected function routeUpdate(array $parameters)
    {
        return route('cast_members.update',['cast_member' => $parameters['id']]);
    }

    protected function routeDelete(array $parameters)
    {
        return route('cast_members.destroy',['cast_member' => $parameters['id']]);
    }

    protected function routeShow(array $parameters)
    {
        return route('cast_members.show',['cast_member' => $parameters['id']]);
    }

    protected function getTestCase(): TestCase
    {
        return $this;
    }
}
