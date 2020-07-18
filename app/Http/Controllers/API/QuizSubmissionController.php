<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QuizSubmission;
use App\Models\Student;
use DB;

class QuizSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return QuizSubmission::where('quiz_id', $request['quiz_id'])
                    ->where('student_id', $request['student_id'])
                    ->with('student')
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
        $student = (Student::where('user_id', auth('api')->user()->id)->get())[0];
        DB::table('quiz_submissions')->insert([
            'quiz_id' => $request->quiz_id,
            'student_id' => $student->id,
            'marks' => $request->marks_obtained,
        ]);
        return ['message' => 'Submitted!'];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        QuizSubmission::find($id)->delete();

        return ['message'=>'Quiz Submission Deleted'];
    }

    public function check_quiz_submission(Request $request)
    {
        $student = (Student::where('user_id', auth('api')->user()->id)->get())[0];
        $quiz_submission = QuizSubmission::where('student_id', $student->id)
                                        ->where('quiz_id', $request->quiz_id)
                                        ->where('deleted_at', NULL)
                                        ->get();
        if(count($quiz_submission) > 0)
        {
            return 0;
        }
        return 1;
    }
}
