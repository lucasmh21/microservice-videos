<?php

namespace App\Http\Controllers\api;

use App\Http\Resources\GenreResource;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends AbstractBasicCrudController
{
    private $rules = ['is_active' => 'boolean',
                      'categories_id' => 'required|array|exists:categories,id'];
    private const RULE_NAME = 'required|max:255|string|unique:genres,name';

    public function store(Request $request)
    {
        $validData = $request->validate($this->rulesStore());
        /**@var Genre $model */
        $model = $this->model()::create($validData);
        $model->categories()->sync($request->get('categories_id'));
        $model->refresh();
        return new GenreResource($model);
    }

    public function update(Request $request, $id)
    {
        /**@var Genre $object */
        $object = $this->findOrFail($id);
        $validData = $request->validate($this->rulesUpdate($id));
        $object->update($validData);
        $object->categories()->sync($request->get('categories_id'));
        $object->refresh();
        return new GenreResource($object);
    }

    protected function rulesStore()
    {
        $this->rules['name'] = self::RULE_NAME;
        return $this->rules;
    }

    protected function rulesUpdate(string $id)
    {
        $this->rules['name'] = self::RULE_NAME.",{$id}";
        return $this->rules;
    }

    protected function model()
    {
        return Genre::class;
    }
}
