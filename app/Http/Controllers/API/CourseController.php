<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Classroom;

class CourseController extends Controller
{
     public function index()
    {
        return $course = Course::with('classroom')->paginate(5);
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
            'title' => 'required|string|max:20',
            'classroom_id' => 'required|integer'

        ]);
        return Course::create($request->all());
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
            'title' => 'required|string|max:20',
            'classroom_id' => 'required|integer'

        ]);
        $course = Course::find($id)->update($request->all());
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
        Course::find($id)->delete();

        return ['message'=>'School Deleted'];
    }

    public function search(Request $request)
    {
        if($request->q)
        {
            // if(!(preg_match("/^\d+$/", $number)))
            // {
                $classroom = Classroom::where('title','LIKE','%'.$request->q.'%')->get();
            // }

            if(count($classroom) > 0)
            {
                $course = Course::with('classroom')->where('classroom_id',$classroom[0]->id)->paginate(5);

                return $course;
            }

            if(count($classroom) == 0)
            {
                $course = Course::with('classroom')->where('id','LIKE','%'.$request->q.'%')->orWhere('title','LIKE','%'.$request->q.'%')->paginate(5);

                return $course;
            }

        }
        else
        {
            return Course::with('classroom')->paginate(5);
        }

    }

    public function get_course_classroom()
    {
        return Classroom::all();
    }
}
