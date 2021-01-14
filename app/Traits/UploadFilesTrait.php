<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

trait UploadFilesTrait
{

    protected abstract function storeDir();
    protected abstract static function fileAttributes();

    public static function bootUploadFilesTrait()
    {
        static::updating(function (Model $model){
            $fieldsUpdated = array_keys($model->getDirty());
            $filesUpdated = array_intersect($fieldsUpdated, self::fileAttributes());
            $filesFiltered = Arr::where($filesUpdated, function ($fileField) use ($model) {
                return $model->getOriginal($fileField);
            });
            $model->oldFiles = array_map(function($fileField) use ($model){
                return $model->getOriginal($fileField);
            }, $filesFiltered);
        });
    }

    protected function uploadFile(UploadedFile $file)
    {
        $file->store($this->storeDir());
    }

    protected function uploadFiles(array $files)
    {
        foreach ($files as $file) {
            $this->uploadFile($file);
        }
    }

    /**
     * Generic function that delete file
     *
     * @param UploadedFile|string $file
     * @return void
     */
    protected function deleteFile($file)
    {
        $fileName = $file instanceof UploadedFile ? $file->hashName():$file;
        $path = $this->storeDir().'/'.$fileName;
        Storage::delete($path);
    }

    protected function deleteFiles(array $files)
    {
        foreach ($files as $file) {
            $this->deleteFile($file);
        }
    }

    protected function deleteOldFiles(){
        $this->deleteFiles($this->oldFiles);
    }

    protected static function extractFiles(&$attributes = [])
    {
        $filesAttributes = self::fileAttributes();
        $result = [];
        foreach ($filesAttributes as $file) {
            if (isset($attributes[$file]) && $attributes[$file] instanceof UploadedFile){
                $result[] = $attributes[$file];
                $attributes[$file] = $attributes[$file]->hashName();
            }
        }
        return $result;
    }

    protected function fileDirectory()
    {
        return Storage::url($this->storeDir());
    }
}
