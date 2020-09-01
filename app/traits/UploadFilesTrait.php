<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait UploadFilesTrait
{

    protected abstract static function storeDir();
    protected abstract static function fileAttributes();

    protected static function uploadFile(UploadedFile $file)
    {
        $file->store(self::storeDir());
    }

    protected static function uploadFiles(array $files)
    {
        foreach ($files as $file) {
            self::uploadFile($file);
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
        $path = self::storeDir().'/'.$fileName;
        Storage::delete($path);
    }

    protected function deleteFiles(array $files)
    {
        foreach ($files as $file) {
            $this->deleteFile($file);
        }
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
}
