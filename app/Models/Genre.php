<?php

namespace App\Models;

use App\Http\Resources\GenreResource;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use Uuid;
    use SoftDeletes;

    protected $fillable = ['name', 'is_active'];
    protected $casts = ['id' => 'string',
                        'is_active' => 'boolean'];
    public $incrementing = false;

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category');
    }

    public static function modelResource()
    {
        return GenreResource::class;
    }
}
