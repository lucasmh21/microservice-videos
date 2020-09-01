<?php

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Lang;
use ReflectionClass;
use Tests\TestCase;

trait ValidationTrait
{
    abstract protected function model();
    abstract protected function routeStore();
    abstract protected function routeUpdate(array $parameters);
    abstract protected function routeDelete(array $parameters);
    abstract protected function routeShow(array $parameters);
    abstract protected function getTestCase(): TestCase;

    protected function assertCreate(array $data, array $relationships = [])
    {
        $response = $this->getTestCase()->json('POST',$this->routeStore(), $data);
        $response->assertStatus(201);
        $this->assertRelationships($response, $data, $relationships);
        $response->assertJson($data);
        return $response;
    }

    protected function assertUpdate(array $data, array $relationships = [])
    {
        $modelObject = $this->createGenericModel();
        $arrayObject = $this->removeEmpty($modelObject->toArray());

        foreach ($data as $key => $value) {
            $arrayObject[$key] = $value;
        }

        $response = $this->getTestCase()->json('PUT',$this->routeUpdate($modelObject->toArray()),$arrayObject);
        $response->assertStatus(200);
        $this->assertRelationships($response, $data, $relationships);
        $response->assertJson($data);
        return $response;
    }

    protected function assertIndex(string $route)
    {
        $model = $this->model();
        factory($this->model(),10)->create();
        $response = $this->getTestCase()->get($route);
        $response->assertJson((new $model)->toArray());
        return $response;
    }

    protected function assertShow()
    {
        $model = $this->createGenericModel();
        $response = $this->json('GET',$this->routeShow($model->toArray()));
        $response->assertJson($model->toArray(), true);
        return $response;
    }

    protected function assertDestroy()
    {
        $model = $this->createGenericModel();
        $response = $this->json('DELETE', $this->routeDelete($model->toArray()));
        $response->assertStatus(204);
        return $response;
    }

    protected function assertEmptyFieldsStore(array $requiredFields, array $optionalFields = null)
    {
        $testCase = $this->getTestCase();
        $data = $this->formatEmptyParametersArray($requiredFields);
        $response = $testCase->json('POST', $this->routeStore(), $data);
        return $this->validEmptyFields($response, $data, $optionalFields);
    }

    protected function assertEmptyFieldsUpdate(array $requiredFields, array $optionalFields = null)
    {
        $testCase = $this->getTestCase();
        $data = $this->formatEmptyParametersArray($requiredFields);
        $model = $this->createGenericModel();
        $response = $testCase->json('PUT', $this->routeUpdate($model->toArray()), $data);
        return $this->validEmptyFields($response, $data, $optionalFields);
    }

    protected function assertDuplicateStore(array $fields, array $requiredFields = null)
    {
        $model = $this->createGenericModel();
        $valuesModelArray = $model->toArray();
        $data = array();
        foreach ($fields as $field) {
            $data[$field] = $valuesModelArray[$field];
        }

        if(is_array($requiredFields)){
            foreach ($requiredFields as $field) {
                $data[$field] = $valuesModelArray[$field];
            }
        }

        $keys = array_keys($data);
        $response = $this->json('POST', $this->routeStore(), $data);
        $response->assertStatus(422);
        foreach ($keys as $key) {
            $response->assertJsonFragment([
                Lang::get('validation.unique',['attribute' => $key])
            ]);
        }
        return $response;
    }

    protected function assertDuplicateUpdate(array $fields, array $requiredFields = null)
    {
        $model = $this->createGenericModel();
        $secondModel = $this->createGenericModel();
        $valuesModelArray = $secondModel->toArray();
        $data = array();
        foreach ($fields as $field) {
            $data[$field] = $valuesModelArray[$field];
        }

        if(is_array($requiredFields)){
            foreach ($requiredFields as $field) {
                $data[$field] = $valuesModelArray[$field];
            }
        }

        $keys = array_keys($data);
        $response = $this->json('PUT', $this->routeUpdate($model->toArray()), $data);
        $response->assertStatus(422);
        foreach ($keys as $key) {
            $response->assertJsonFragment([
                Lang::get('validation.unique',['attribute' => $key])
            ]);
        }
        return $response;
    }

    private function validEmptyFields(TestResponse $response, array $data, array $optionalFields = null)
    {
        foreach ($data as $key => $value) {
            $response->assertJsonFragment([
                Lang::get('validation.required',['attribute' => $key])
            ]);
        }

        $response->assertStatus(422);
        if(is_array($optionalFields)){
            foreach ($optionalFields as $field) {
                $response->assertJsonMissingValidationErrors($field);
            }
        }

        return $response;
    }

    private function formatEmptyParametersArray(array $values)
    {
        $result = array();
        foreach ($values as $value) {
            $result[$value] = null;
        }
        return $result;
    }

    protected function createGenericModel()
    {
        return factory($this->model())->create();
    }

    protected function assertRelationships(TestResponse $response, array &$data, array $relationships)
    {
        $id = $response->json('id');
        $model = $this->model()::find($id);
        $testCase = $this->getTestCase();

        $reflection = new ReflectionClass($this->model());
        foreach ($relationships as $field => $name) {
            $method = $reflection->getMethod($name);
            $arrayRelationship = array_map(function($array){
                return $array['id'];
            },$method->invoke($model)->get()->toArray());
            $testCase->assertEquals($arrayRelationship,$data[$field]);
        }

        $relationShipFields = array_keys($relationships);

        foreach ($relationShipFields as $fieldName) {
            unset($data[$fieldName]); //Remove os relacionamentos para fazer assert do Json como o $data recebido
        }
    }

    private function removeEmpty($array) {
        return array_filter($array, function ($value) {
            return !empty($value) || $value === 0;
        });
    }
}
