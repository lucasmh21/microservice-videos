<?php

namespace App\Models;

use App\Http\Resources\CastMemberResource;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastMember extends Model
{
    use SoftDeletes;
    use Uuid;

    public const TYPE_VALUES = [1 => 'Director', 2 => 'Actor'];
    protected $fillable = ['name','type'];
    protected $casts = ['id' => 'string'];
    public $incrementing = false;

    public static function modelResource()
    {
        return CastMemberResource::class;
    }
}
