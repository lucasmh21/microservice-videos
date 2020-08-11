<?php

namespace App\Http\Controllers\api;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends AbstractBasicCrudController
{

    private const RULE_NAME = 'required|max:255|string|unique:categories,name';

    private $rules = [
        'description' => 'max:255|string',
        'is_active' => 'boolean',
    ];

    protected function model()
    {
        return Category::class;
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
