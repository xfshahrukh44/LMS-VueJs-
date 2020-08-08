<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lecture;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Support\Facades\Gate;
use DB;

class LectureController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->session_id)
        {
            $lectures = Lecture::where('session_id', $request->session_id)->paginate(10);
            return $lectures;
        }
        else{
            if(Gate::allows('isAdmin'))
            {
                return Lecture::latest()->paginate(10);
            }
            if(Gate::allows('isTeacher'))
            {
                $teacher = (Teacher::where('user_id', auth('api')->user()->id)->get())[0];
                return DB::table('teachers')
                        ->where('teachers.id', $teacher->id)
                        ->join('sessions', 'teachers.id', '=', 'sessions.teacher_id')
                        ->join('lectures', 'sessions.id', '=', 'lectures.session_id')
                        ->select('lectures.title', 'lectures.url', 'lectures.created_at')
                        ->latest()->paginate(5);
                
            }
            if(Gate::allows('isStudent'))
            {
                $student = (Student::where('user_id', auth('api')->user()->id)->get())[0];
                return DB::table('students')
                            ->where('students.id', $student->id)
                            ->join('sessions', 'students.section_id', '=', 'sessions.section_id')
                            ->join('lectures', 'sessions.id', '=', 'lectures.session_id')
                            ->select('lectures.title', 'lectures.url', 'lectures.created_at')
                            ->latest()->paginate(5);
            }
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

        if(Gate::allows('isAdmin')||Gate::allows('isTeacher'))
        {
            $this->validate($request, [
                'session_id' => 'required',
                'title' => 'required',
                'url' => 'required'
            ]);
            $str = $request->url;
            $url = 'https://www.youtube.com/embed/'.substr($str, (strlen($str)) - 11);
            return Lecture::create([
                'session_id' => $request->session_id,
                'title' => $request->title,
                'url' => $url,
            ]);
        }
        // else
        // {
        //     return redirect()->route('api/lecture');
        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Lecture::find($id);
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
        Lecture::find($id)->delete();

        return ['message'=>'Lecture Deleted'];
    }
}
