<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Section;
use App\Models\Course;
use App\Models\Session;
use Illuminate\Support\Facades\Gate;

class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

     public function index()
    {
        if(Gate::allows('isAdmin'))
        {
            return Session::with('section.classroom','course.classroom','teacher', 'assignments', 'quizzes.questions.options', 'quizzes.quiz_submissions.student', 'quizzes.session.section.classroom.school', 'quizzes.session.course', 'quizzes.session.teacher', 'lectures')->paginate(5);
        }
        if(Gate::allows('isTeacher'))
        {
            $teacher = (Teacher::where('user_id', auth('api')->user()->id)->get())[0];

            return Session::with('section.classroom','course.classroom','teacher', 'assignments', 'quizzes.questions.options', 'quizzes.quiz_submissions.student', 'quizzes.session.section.classroom.school', 'quizzes.session.course', 'quizzes.session.teacher', 'lectures')
            ->where('teacher_id', $teacher->id)
            ->paginate(5);
        }
        if(Gate::allows('isStudent'))
        {
            $student = (Student::where('user_id', auth('api')->user()->id)->get())[0];

            return Session::with('section.classroom','course.classroom','teacher', 'assignments', 'quizzes.questions.options', 'quizzes.quiz_submissions.student', 'quizzes.session.section.classroom.school', 'quizzes.session.course', 'quizzes.session.teacher', 'lectures')
            ->where('section_id', $student->section->id)
            ->paginate(5);
        }
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
            'section_id' => 'required|integer',
            'course_id' => 'required|integer',
            'teacher_id' => 'required|integer'

        ]);
        return Session::create($request->all());
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
        return dd($request);
        if($request->change)
        {
            return Session::find($id)->update($request->all());
        }
        $this->validate($request,[ 
            'section_id' => 'required|integer',
            'course_id' => 'required|integer',
            'teacher_id' => 'required|integer'

        ]);
        $session = Session::find($id)->update($request->all());
        return['message'=>'Successfull'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Session::find($id)->delete();

        return ['message'=>'School Deleted'];
    }

    public function search(Request $request)
    {
        if($request->q)
        {
            $section = Section::where('title','LIKE','%'.$request->q.'%')->get();
            
            if(count($section) > 0)
            {
                $session = Session::with('section','course','teacher')->where('section_id',$section[0]->id)->paginate(5);

                return $session;   
            }

            else
            {
                $course = Course::where('title','LIKE','%'.$request->q.'%')->get();

                if(count($course) > 0)
                {
                    $session = Session::with('section','course','teacher')->where('course_id',$course[0]->id)->paginate(5);

                    return $session;
                }

                else
                {
                    $teacher = Teacher::where('name','LIKE','%'.$request->q.'%')->get();

                    if(count($teacher) > 0)
                    {
                        $session = Session::with('section','course','teacher')->where('teacher_id',$teacher[0]->id)->paginate(5);

                        return $session;
                    }

                    else
                    {
                        $session = Session::with('section','course','teacher')->where('id','LIKE','%'.$request->q.'%')->orWhere('name','LIKE','%'.$request->q.'%')->orWhere('contact','LIKE','%'.$request->q.'%')->orWhere('address','LIKE','%'.$request->q.'%')->paginate(5);

                        return $session;
                    }

                }

            }

        }
        else
        {
            return Session::with('section','course','teacher')->paginate(5);
        }

    }

    public function get_session_section()
    {
        return Section::with('classroom', 'sessions.course', 'students')->get();
    }

    public function get_session_course()
    {
        return Course::all();
    }

    public function get_session_teacher()
    {
        return Teacher::all();
    }

    public function change_meeting_state(Request $request)
    {
        $session = Session::find($request->session_id);
        if($session->state == 'enable')
        {
            $session->update([
                'state' => 'disable'
            ]);
            return $session->save();
        }
        else
        {
            $session->update([
                'state' => 'enable'
            ]);
            return $session->save();
        }
    }
}
