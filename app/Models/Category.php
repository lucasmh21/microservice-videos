<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use Uuid;
    use SoftDeletes;
    protected $fillable = ['name', 'description'];
    protected $casts = [
        'id' => 'string'
    ];
}
