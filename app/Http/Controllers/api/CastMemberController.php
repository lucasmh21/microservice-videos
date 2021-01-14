<?php

namespace App\Http\Controllers\api;

use App\Http\Resources\CastMemberResource;
use App\Models\CastMember;

class CastMemberController extends AbstractBasicCrudController
{
    protected $rules;

    public function __construct()
    {
        $keys = implode(",",array_keys(CastMember::TYPE_VALUES));
        $this->rules = [
            'name' => 'required|max:255|string',
            'type' => "required|integer|in:{$keys}"
        ];
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate(string $id)
    {
        return $this->rules;
    }
    protected function model()
    {
        return CastMember::class;
    }
}
