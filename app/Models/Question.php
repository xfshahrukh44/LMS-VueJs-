<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = ['quiz_id', 'content'];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function quiz()
    {
        return $this->belongsTo('App\Models\Quiz');
    }

    public function options()
    {
        return $this->hasMany('App\Models\Option');
    }
}
