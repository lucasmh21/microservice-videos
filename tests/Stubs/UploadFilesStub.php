<?php

namespace Tests\Stubs;

use App\Traits\UploadFilesTrait;
use Illuminate\Database\Eloquent\Model;

class UploadFilesStub extends Model
{
    use UploadFilesTrait;

    protected $fillable = [
        'file1', 'file2'
    ];

    protected static function fileAttributes()
    {
        return [
            'file1', 'file2'
        ];
    }

    public static function create($attributes = [])
    {
        $files = self::extractFiles($attributes);
        self::uploadFiles($files);
        $obj = new UploadFilesStub($attributes);
        return $obj;
    }

    protected static function storeDir()
    {
        return 'UploadFileStub';
    }
}
