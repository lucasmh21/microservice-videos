<?php

namespace App\Http\Controllers\api;

use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends AbstractBasicCrudController
{
    private $rules = ['is_active' => 'boolean'];
    private const RULE_NAME = 'required|max:255|string|unique:genres,name';

    protected function model()
    {
        return Genre::class;
    }

    protected function rules()
    {
        return $this->rules;
    }

    public function store(Request $request)
    {
        $this->rules['name'] = self::RULE_NAME;
        return parent::store($request);
    }

    public function update(Request $request, $id)
    {
        $this->rules['name'] = self::RULE_NAME.",{$id}";
        return parent::update($request, $id);
    }
}
