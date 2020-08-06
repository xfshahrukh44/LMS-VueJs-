<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lecture extends Model
{
    use SoftDeletes;

    protected $fillable = ['session_id', 'title', 'url'];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function session()
    {
        return $this->belongsTo('App\Models\Session');
    }
}
