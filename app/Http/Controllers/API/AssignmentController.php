<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use App\Models\Assignment;
use App\Models\Session;
use App\Models\Section;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Submission;
use DB;
use Carbon\Carbon;

class AssignmentController extends Controller
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
        if($this->authorize('isAdmin'))
        {
            $assignment = Assignment::with('session')->paginate(10);
            return $assignment;
        }
        if($this->authorize('isStudent'))
        {
            $assignment = [];
            $student = Student::where('user_id', auth('api')->user()->id)->paginate(10);
            $section = Section::find($student[0]->section_id);
            foreach($section->sessions as $session)
            {
                foreach($session->assignments as $asgn)
                {
                    $check = Submission::where('student_id', $student[0]->id && 'assignment_id', $asgn->id)->get();
                    if(count($check) == 0)
                    {
                        array_push($assignment, $asgn);
                    }
                    else
                    {
                        continue;
                    }
                }
            }
            return $assignment;

        }
        if($this->authorize('isTeacher'))
        {
            $teacher = (Teacher::where('user_id', auth('api')->user()->id)->get())[0];
            $assignment = [];
            foreach($teacher->sessions as $session)
            {
                foreach($session->assignments as $assignments)
                {
                    array_push($assignment, $assignments);
                }
            }
            return $assignment;
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
                'marks' => 'required',
            ]);
            // $type = $_FILES['file']['type'];
            // $data = file_get_contents($_FILES['file']['tmp_name']);

            //Purging Files Code Start//
            $assignment = Assignment::all();
            if(count($assignment) > 100){
                for($i = 0; $i < 10; $i++){
                    Storage::delete($assignment[$i]->file);
                    Assignment::where('id', $assignment[$i]->id)->delete();
                }
            }
            //Purging Files Code End//

            $session = Session::with('course')->find($request->session_id);
            // dd(auth()->user()->name);
            // exit();
            $file_data = explode(',',$request->file);
            $file_base = explode('/',$file_data[0]);
            $file_type = explode(';',$file_base[1]);
            $file_name = explode('.',$request->file_name);
            Storage::disk('public')->put(Carbon::today().' - '.auth('api')->user()->name.' - '.$session->course->title.' - '.$file_name[0].'.'.$file_type[0],base64_decode($file_data[1]));
            DB::table('assignments')->insert([
                'session_id' => $request->session_id,
                'title' => $request->title,
                'description' => $request->description,
                'marks' => $request->marks,
                'file' => '/storage/'.Carbon::today().' - '.auth('api')->user()->name.' - '.$session->course->title.' - '.$file_name[0].'.'.$file_type[0],
            ]);
        }
        else
        {
            return redirect()->route('api/session');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if($this->authorize('isAdmin'))
        {
            $assignment = Assignment::find($id);
            return $assignment;
        }
        if($this->authorize('isTeacher'))
        {
            $assignment = Assignment::find($id);
            $teacher = Teacher::where('user_id', auth('api')->user()->id)->get();
            if($assignment->session->teacher_id == $teacher[0]->id)
            {
                return $assignment;
            }
            else
            {
                return redirect()->route('api/session');
            }
        }
        if($this->authorize('isStudent'))
        {
            $assignment = Assignment::find($id);
            $student = Student::where('user_id', auth('api')->user()->id)->get();
            if($assignment->session->section_id == $student[0]->section_id)
            {
                return $assignment;
            }
            else
            {
                return redirect()->route('api/session');
            }
        }
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
        if($this->authorize('isAdmin'))
        {
            $this->validate($request, [
                'session_id' => 'required',
                'title' => 'required',
                'marks' => 'required',
            ]);
            return Assignment::find($id)->update($request->all());
        }

        else if($this->authorize('isTeacher'))
        {
            $assignment = Assignment::find($id);
            $teacher = Teacher::where('user_id', auth('api')->user()->id)->get();
            if($assignment->session->teacher_id == $teacher[0]->id)
            {
                $this->validate($request, [
                    'session_id' => 'required',
                    'title' => 'required',
                    'marks' => 'required',
                ]);
                return Assignment::find($id)->update($request->all());
            }
            else
            {
                return redirect()->route('api/session');
            }
        }
        else
        {
            return redirect()->route('api/session');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if($this->authorize('isAdmin'))
        {
            return Assignment::where('id', $id)->delete();
        }
        else if($this->authorize('isTeacher'))
        {
            $assignment = Assignment::find($id);
            $teacher = Teacher::where('user_id', auth('api')->user()->id)->get();
            if($assignment->session->teacher_id == $teacher[0]->id)
            {
                return Assignment::where('id', $id)->delete();
            }
            else
            {
                return redirect()->route('api/session');
            }
        }
        
        else
        {
            return redirect()->route('api/session');
        }
    }

    public function download(Request $request)
    {
        if($this->authorize('isAdmin'))
        {
            $assignment = Assignment::find($request['assignment_id']);
            $file_contents = $assignment->file;
            return Storage::download($assignment->file);
        }
        if($this->authorize('isTeacher'))
        {
            $assignment = Assignment::find($request->assignment_id);
            $teacher = Teacher::where('user_id', auth('api')->user()->id)->get();
            if($assignment->session->teacher_id == $teacher[0]->id)
            {
               return Storage::download($assignment->file);
                // $assignment = Assignment::find($request['assignment_id']);
                // $file_contents = $assignment->file;
                // return response($file_contents)
                // ->header('Cache-Control', 'no-cache private')
                // ->header('Content-Description', 'File Transfer')
                // ->header('Content-Type', $assignment->type)
                // ->header('Content-length', strlen($file_contents))
                // ->header('Content-Transfer-Encoding', 'binary');
            }
            else
            {
                return redirect()->route('api/session');
            }
        }
        
        if($this->authorize('isStudent'))
        {
            $assignment = Assignment::find($id);
            $student = Student::where('user_id', auth('api')->user()->id)->get();
            if($assignment->session->section_id == $student[0]->section_id)
            {
                return $assignment;
            }
            else
            {
                return redirect()->route('api/session');
            }
        }
    }
}


