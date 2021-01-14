<?php

namespace App\Http\Controllers\api;

use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Rules\GenreCategoryRelationship;
use Illuminate\Http\Request;

class VideoController extends AbstractBasicCrudController
{

    private $rules;

    public function __construct()
    {
        $this->rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'year_launched' => 'required|integer|between:1895,2100',
            'opened' => 'boolean',
            'rating' => 'required|string|max:3',
            'duration' => 'required|integer',
            'categories_id' => 'required|array|exists:categories,id',
            'genres_id' => ['required','array','exists:genres,id'],
            'video_file' => 'mimetypes:video/mp4|max:52428800',
            'thumb_file' => 'max:5120',
            'banner_file' => 'max:10240',
            'trailer_file' => 'max:1048576'
        ];
    }

    public function store(Request $request)
    {
        //Validations
        $rules = $this->rulesStore();
        array_push($rules['genres_id'],new GenreCategoryRelationship($request->get('categories_id')));
        $validData = $request->validate($rules);

        /** @var Video $obj */
        $obj = $this->model()::create($validData);
        return $this->getResource($this->model()::modelResource(), $obj);
    }

    public function update(Request $request, $id)
    {
        //Validations
        /** @var Video $object */
        $object = $this->findOrFail($id);
        $rules = $this->rulesUpdate($id);
        array_push($rules['genres_id'],new GenreCategoryRelationship($request->get('categories_id')));
        $validData = $request->validate($rules);

        $object->update($validData);
        $object->categories()->sync($request->get('categories_id'));
        $object->genres()->sync($request->get('genres_id'));
        $object->refresh();
        return $this->getResource($this->model()::modelResource(), $object);
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
        return Video::class;
    }
}
