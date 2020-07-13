<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Session;
use App\Models\Submission;
use App\Models\Assignment;
use App\Models\Student;
use App\Models\Teacher;
use DB;
use Carbon\Carbon;
use Auth;

class SubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if($this->authorize('isAdmin'))
        {
            $submission = Submission::all();
            return $submission;
        }
        else if($this->authorize('isTeacher'))
        {
            $teacher = (Teacher::where('user_id', auth()->user()->id)->get())[0];
            $sessions = Session::where('teacher_id', $teacher->id)->get();
            $submissions = [];
            foreach($sessions as $session)
            {
                foreach($session->assignments as $assignment)
                {
                    foreach($assignment->submissions as $submission)
                    {
                        array_push($submissions, $submission);
                    }
                }
            }
            return $submissions;
        }
        else
        {
            $student =(Student::where('user_id', auth()->user()->id)->get())[0];
            $submission = Submission::where('student_id', $student->id)->get();
            return $submissions;
        }
        {
            return redirect()->route('api/session');
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
        if($this->authorize('isStudent') || $this->authorize('isTeacher'))
        {
            $this->validate($request, [
                'title' => 'required',
                'assignment_id' => 'required',
                'student_id' => 'required',
                'file' => 'required',
            ]);
            // dd($_FILES);
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
            
            $assignment = Assignment::find($request->assignment_id);
            $path = $request->file('file')->storeAs('public', auth()->user()->name.' - '.$assignment->session->course->title.' - '.$_FILES['file']['name']);
            return DB::table('submissions')->insert([
                'title' => $request->title,
                'assignment_id' => $request->assignment_id,
                'student_id' => $request->student_id,
                'file' => $path,
                'marks' => $request->marks,
            ]);
            // Assignment::create($request->all());
        }
        else
        {
            return redirect()->route('api/submission');
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
            $submission = Submission::find($id);
            return $submission;
        }
        else if($this->authorize('isTeacher'))
        {
            $teacher = Teacher::where('user_id', auth()->user()->id)->get();
            $submission = Submission::find($id);
            if($submission->assignment->session->teacher_id == $teacher[0]->id)
            {
                return $submission;              
            }
            else
            {
                return redirect()->route('api/submission');
            }
        }
        else
        {
            $student = Student::where('user_id', auth()->user()->id)->get();
            $submission = Submission::find($id);
            if($submission->student_id == $student[0]->id)
            {
                return $submission;
            }
            else
            {
                return redirect()->route('api/subimssion');
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
        if($this->authorize('isTeacher'))
        {
            $teacher = Teacher::where('user_id', auth()->user()->id)->get();
            $submission = Submission::find($id);
            if($submission->assignment->session->teacher_id == $teacher[0]->id)
            {
                $this->validate($request, [
                    'marks' => 'required',
                ]);
                return Submission::find($id)->update($request->all());
            }
            else
            {
                return redirect()->route('api/submission');
            }
        }
        else
        {
            return redirect()->route('api/submission');
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
            return Submission::where('id', $id)->delete();
        }
        else
        {
            return redirect()->route('api/submission');
        }
    }

    public function download(Request $request)
    {
        if($this->authorize('isAdmin'))
        {
            $submission = Submission::find($request['submission_id']);
            return Storage::download($submission->file);
            // $file_contents = $submission->file;
            // return response($file_contents)
            //         ->header('Cache-Control', 'no-cache private')
            //         ->header('Content-Description', 'File Transfer')
            //         ->header('Content-Type', $submission->type)
            //         ->header('Content-length', strlen($file_contents))
            //         ->header('Content-Transfer-Encoding', 'binary');
        }
        if($this->authorize('isTeacher'))
        {
            $teacher = Teacher::where('user_id', auth()->user()->id)->get();
            $submission = Submission::find($request['submission_id']);
            if($submission->assignment->session->teacher_id == $teacher[0]->id)
            {
                return Storage::download($submission->file);
                // $file_contents = $submission->file;
                // return response($file_contents)
                //         ->header('Cache-Control', 'no-cache private')
                //         ->header('Content-Description', 'File Transfer')
                //         ->header('Content-Type', $submission->type)
                //         ->header('Content-length', strlen($file_contents))
                //         ->header('Content-Transfer-Encoding', 'binary');
            }
            else
            {
                return redirect()->route('session.index');
            }
        }
        if($this->authorize('isStudent'))
        {
            $student = Student::where('user_id', auth()->user()->id)->get();
            $submission = Submission::find($request['submission_id']);
            if($submission->student_id == $student[0]->id)
            {
                return Storage::download($submission->file);
                // $file_contents = $submission->file;
                // return response($file_contents)
                //         ->header('Cache-Control', 'no-cache private')
                //         ->header('Content-Description', 'File Transfer')
                //         ->header('Content-Type', $submission->type)
                //         ->header('Content-length', strlen($file_contents))
                //         ->header('Content-Transfer-Encoding', 'binary');
            }
            else
            {
                return redirect()->route('api/submission');
            }
        }
    }

    public function unmarked_submissions()
    {
        if($this->authorize('isTeacher'))
        {
            $submission = [];
            $teacher = (Teacher::where('user_id', auth()->user()->id)->get())[0];
            foreach($teacher->sessions as $session)
            {
                foreach($session->assignments as $assignment)
                {
                    foreach($assignment->submissions as $submissions)
                    {
                        if($submissions->marks == NULL)
                        {
                            array_push($submission, $submissions);
                        }
                        else
                        {
                            continue;
                        }
                    }
                }
            }
            return $submission;
        }
        else
        {
            return redirect()->route('api/submission');
        }
    }
}
