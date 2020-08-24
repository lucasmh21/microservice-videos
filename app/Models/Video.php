<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use Uuid;
    use SoftDeletes;

    public $incrementing = false;
    protected $fillable = ['title', 'description', 'year_launched', 'opened', 'rating', 'duration'];
    protected $casts = [
        'opened' => 'boolean',
        'id' => 'string'
    ];
    protected $dates = [
        'created_at',
        'deleted_at',
        'updated_at'
    ];

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category');
    }

    public function genres()
    {
        return $this->belongsToMany('App\Models\Genre');
    }
}
