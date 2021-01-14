<?php

namespace App\Models;

use App\Http\Resources\CategoryResource;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use Uuid;
    use SoftDeletes;
    protected $fillable = ['name', 'description', 'is_active'];
    protected $casts = [
        'id' => 'string',
        'is_active' => 'boolean'
    ];
    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at'
    ];
    public $incrementing = false;

    public static function modelResource()
    {
        return CategoryResource::class;
    }
}
