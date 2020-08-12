<?php

namespace App\Http\Controllers\api;

use App\Models\CastMember;

class CastMemberController extends AbstractBasicCrudController
{
    protected function model()
    {
        return CastMember::class;
    }

    protected function rules()
    {
        $keys = implode(",",array_keys(CastMember::TYPE_VALUES));
        $rules = [
            'name' => 'required|max:255|string',
            'type' => "required|integer|in:{$keys}"
        ];
        return $rules;
    }
}
