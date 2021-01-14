<?php

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Resources\Json\JsonResource;

trait ResourceTrait
{
    protected function assertResource(TestResponse $response, JsonResource $jsonResource)
    {
        $response->assertJson($jsonResource->response()->getData(true));
    }
}
