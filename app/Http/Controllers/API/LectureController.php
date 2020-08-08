<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lecture;
use App\Models\Session;
use App\Models\Teacher;
use Illuminate\Support\Facades\Gate;

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
            else if(Gate::allows('isTeacher'))
            {
                $teacher = (Teacher::where('user_id', auth('api')->user()->id)->get())[0];
                $sessions = Session::where('teacher_id', $teacher->id)->get();
                // $lectures = [];
                // foreach($sessions as $session)
                // {
                //     foreach($session->lectures as $lecture)
                //     {
                //         array_push($lectures, $lecture);
                //     }
                // }
                // return $lectures;
                return [Lecture::latest()->paginate(10), $teacher];
                
            }
            return Lecture::latest()->paginate(10);
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
