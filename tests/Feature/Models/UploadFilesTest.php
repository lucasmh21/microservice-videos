<?php

namespace Tests\Feature\Models;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use ReflectionClass;
use Tests\Stubs\UploadFilesStub;
use Tests\TestCase;

class UploadFilesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
    }

    public function testUploadFile()
    {
        $data = [
            'file1' => UploadedFile::fake()->create('fileTest1',2048,'video/mp4')
        ];
        $obj = UploadFilesStub::create($data);
        $this->assertEquals($data['file1']->hashName(), $obj->file1);
        Storage::assertExists($this->getStoreDir().'/'.$obj->file1);
    }

    public function testUploadFiles()
    {
        $data = [
            'file1' => UploadedFile::fake()->create('fileTest1', 2048, 'video/mp4'),
            'file2' => UploadedFile::fake()->create('fileTest2', 4096, 'video/mp4')
        ];
        $obj = UploadFilesStub::create($data);
        $this->assertEquals($data['file1']->hashName(), $obj->file1);
        $this->assertEquals($data['file2']->hashName(), $obj->file2);
        Storage::assertExists($this->getStoreDir().'/'.$obj->file1);
        Storage::assertExists($this->getStoreDir().'/'.$obj->file2);
    }

    public function testeDeleteFile()
    {
        $data = [
            'file1' => UploadedFile::fake()->create('fileTest1',2048,'video/mp4')
        ];
        $obj = UploadFilesStub::create($data);
        $this->getDeleteFile()->invoke($obj,$obj->file1);
        Storage::assertMissing($this->getStoreDir().'/'.$obj->file1);
    }

    public function testDeleteFiles()
    {
        $data = [
            'file1' => UploadedFile::fake()->create('fileTest1', 2048, 'video/mp4'),
            'file2' => UploadedFile::fake()->create('fileTest2', 4096, 'video/mp4')
        ];
        $obj = UploadFilesStub::create($data);
        $this->getDeleteFiles()->invoke($obj, [$obj->file1, $obj->file2]);
        Storage::assertMissing($this->getStoreDir().'/'.$obj->file1);
        Storage::assertMissing($this->getStoreDir().'/'.$obj->file2);
    }

    private function getStoreDir()
    {
        $reflectionObj = new ReflectionClass(UploadFilesStub::class);
        $method = $reflectionObj->getMethod('storeDir');
        $method->setAccessible(true);
        return $method->invoke(null);
    }

    private function getDeleteFile()
    {
        $reflectionObj = new ReflectionClass(UploadFilesStub::class);
        $method = $reflectionObj->getMethod('deleteFile');
        $method->setAccessible(true);
        return $method;
    }

    public function getDeleteFiles()
    {
        $reflectionObj = new ReflectionClass(UploadFilesStub::class);
        $method = $reflectionObj->getMethod('deleteFiles');
        $method->setAccessible(true);
        return $method;
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
