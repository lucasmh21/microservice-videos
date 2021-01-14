<?php

namespace App\Http\Controllers\api;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends AbstractBasicCrudController
{

    private const RULE_NAME = 'required|max:255|string|unique:categories,name';

    private $rules = [
        'description' => 'max:255|string',
        'is_active' => 'boolean',
    ];

    protected function rulesStore()
    {
        $this->rules['name'] = self::RULE_NAME;
        return $this->rules;
    }

    protected function rulesUpdate(string $id)
    {
        $this->rules['name'] = self::RULE_NAME.','.$id;
        return $this->rules;
    }

    protected function model()
    {
        return Category::class;
    }

    public function show($id)
    {
        return new CategoryResource($this->findOrFail($id));
    }
}
