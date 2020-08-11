<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

abstract class AbstractBasicCrudController extends Controller
{
    protected abstract function model();
    protected abstract function rules();

    public function index()
    {
       return $this->model()::all();
    }

    public function store(Request $request)
    {
        $validData = $request->validate($this->rules());
        return $this->model()::create($validData);
    }

    public function show($id)
    {
        return $this->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $object = $this->findOrFail($id);
        $validData = $request->validate($this->rules());
        $object->update($validData);
        return $object;
    }

    public function destroy($id)
    {
        $object = $this->findOrFail($id);
        $object->delete();
        return response()->noContent();
    }

    private function findOrFail($id)
    {
        $model = $this->model();
        $keyName =  (new $model)->getRouteKeyName();
        return $model::where($keyName, $id)->firstOrFail();
    }
}
