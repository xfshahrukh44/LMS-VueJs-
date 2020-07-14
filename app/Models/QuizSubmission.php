<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuizSubmission extends Model
{
    use SoftDeletes;

    protected $fillable = ['quiz_id', 'student_id', 'marks'];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function quiz()
    {
        return $this->belongsTo('App\Models\Quiz');
    }

    public function student()
    {
        return $this->belongsTo('App\Models\Student');
    }
}
