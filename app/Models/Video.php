<?php

namespace App\Models;

use App\Traits\UploadFilesTrait;
use App\Traits\Uuid;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Video extends Model
{
    use Uuid;
    use SoftDeletes;
    use UploadFilesTrait;

    public $oldFiles = [];
    public $incrementing = false;
    protected $fillable = [
        'title',
        'description',
        'year_launched',
        'opened',
        'rating',
        'duration',
        'video_file',
        'thumb_file',
        'banner_file',
        'trailer_file'
    ];
    protected $casts = [
        'opened' => 'boolean',
        'id' => 'string'
    ];
    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at'
    ];


    protected static function storeDir()
    {
        return 'Videos';
    }

    protected static function fileAttributes()
    {
        return [
            'video_file',
            'thumb_file',
            'banner_file',
            'trailer_file'
        ];
    }

    public static function create(array $attributes = [])
    {
        try {
            DB::beginTransaction();
            $obj = static::query()->create($attributes);
            Video::handleRelations($obj, $attributes);
            $files = Video::extractFiles($attributes);
            Video::uploadFiles($files);
            DB::commit();
            return $obj;
        } catch (Exception $e) {
            if(isset($obj)){
                $obj->deleteFiles($files);
            }
            DB::rollback();
            throw $e;
        }
    }

    /**
     * Update the model in the database.
     *
     * @param  array  $attributes
     * @param  array  $options
     * @return bool
     */
    public function update(array $attributes = [], array $options = [])
    {
        try {
            DB::beginTransaction();
            $files = Video::extractFiles($attributes);
            $saved = parent::update($attributes, $options);
            Video::handleRelations($this, $attributes);
            if($saved){
                Video::uploadFiles($files);
            }
            DB::commit();
            if($saved && count($files)){
                $this->deleteOldFiles();
            }
        } catch (Exception $e) {
            $this->deleteFiles($files);
            DB::rollback();
            throw $e;
        }
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category');
    }

    public function genres()
    {
        return $this->belongsToMany('App\Models\Genre');
    }

    private static function handleRelations(Video $video, $attributes = [])
    {
        $video->categories()->sync($attributes['categories_id']);
        $video->genres()->sync($attributes['genres_id']);
        $video->refresh();
    }
}
