<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Option extends Model
{
    protected $fillable = ['question_id', 'is_correct', 'is_selected', 'content'];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function question()
    {
        return $this->belongsTo('App\Models\Question');
    }
}
