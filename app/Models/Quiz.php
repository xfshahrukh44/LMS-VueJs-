<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use SoftDeletes;

    protected $fillable = ['session_id', 'title', 'marks', 'number_of_questions', 'minutes'];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function session()
    {
        return $this->belongsTo('App\Models\Session');
    }

    public function questions()
    {
        return $this->hasMany('App\Models\Question');
    }

    public function quiz_submissions()
    {
        return $this->hasMany('App\Models\QuizSubmission');
    }
}
