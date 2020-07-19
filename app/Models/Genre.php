<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use Uuid;
    use SoftDeletes;

    protected $fillable = ['name', 'is_active'];
    protected $casts = ['id' => 'string'];
}
