<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\JsonResource;

trait ModelResourceTrait
{
    protected function getResource ($jsonResource, $data)
    {
        return new $jsonResource($data);
    }

    protected function getCollection ($jsonResource, $data)
    {
        return $jsonResource::collection($data);
    }
}
