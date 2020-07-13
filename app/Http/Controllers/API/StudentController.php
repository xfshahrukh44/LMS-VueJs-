<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\Section;
use App\Models\Student;

class StudentController extends Controller
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
        return $student = Student::with('section.classroom','user')->paginate(5);
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
            'user_id' => 'required|integer',
            'name' => 'required|string|max:20',
            'contact' => 'required|string|max:20',
            'address' => 'required|string|max:30',
            'section_id' => 'required|integer|max:20'

        ]);
        return Student::create($request->all());
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
        $this->validate($request,[ 
            'user_id' => 'required|integer',
            'name' => 'required|string|max:20',
            'contact' => 'required|string|max:20',
            'address' => 'required|string|max:30',
            'section_id' => 'required|integer'

        ]);
        $student = Student::find($id)->update($request->all());
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
        Student::find($id)->delete();

        return ['message'=>'School Deleted'];
    }

    public function search(Request $request)
    {
        if($request->q)
        {
            $section = Section::where('title','LIKE','%'.$request->q.'%')->get();
            
            if(count($section) > 0)
            {
                $student = Student::with('section.classroom','user')->where('section_id',$section[0]->id)->paginate(5);

                return $student;   
            }

            else
            {
                $user = User::where('name','LIKE','%'.$request->q.'%')->get();

                if(count($user) > 0)
                {
                    $student = Student::with('section.classroom','user')->where('user_id',$user[0]->id)->paginate(5);

                    return $student;
                }

                else
                {
                    $student = Student::with('section.classroom','user')->where('id','LIKE','%'.$request->q.'%')->orWhere('name','LIKE','%'.$request->q.'%')->orWhere('contact','LIKE','%'.$request->q.'%')->orWhere('address','LIKE','%'.$request->q.'%')->paginate(5);

                    return $student;
                }
            }

        }
        else
        {
            return Student::with('section.classroom','user')->paginate(5);
        }

    }

    public function get_student_section()
    {
        return Section::all();
    }

    public function get_user_student(Request $request)
    {
        return User::where('type','student')->get();
    }
}
