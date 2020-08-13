<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Quiz;
use App\Models\Session;
use App\Models\Student;
use App\Notifications\QuizNotification;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return Quiz::where('session_id', $request['session_id'])
                    ->with('session.section.classroom.school','quiz_submissions.student', 'questions.options')
                    ->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[ 
            'session_id' => 'required|integer',
            'title' => 'required|string',
            'marks' => 'required|integer',
            'number_of_questions' => 'integer'

        ]);
        $quiz = Quiz::create($request->all());

        // Get students to be notified
        $session = Session::find($request->session_id);
        $students = Student::where('section_id', $session->section->id)->get();
        //notify the students
        foreach($students as $student)
        {
            $student->user->notify(new QuizNotification($quiz));
        }
        return $quiz->id;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Quiz::find($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Quiz::find($id)->delete();

        return ['message'=>'Quiz Deleted'];
    }
}
