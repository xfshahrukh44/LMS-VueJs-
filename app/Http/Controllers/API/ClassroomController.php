<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Classroom;
use DB;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $classroom = Classroom::with('school', 'sections')->paginate(5);
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
            'school_id' => 'required|integer|max:20'

        ]);
        return Classroom::create($request->all());
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
            'school_id' => 'required|integer|max:20'

        ]);
        $classroom = Classroom::find($id)->update($request->all());
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
        Classroom::find($id)->delete();

        return ['message'=>'School Deleted'];
    }

    public function search(Request $request)
    {
        if($request->q)
        {

            $school = School::where('title','LIKE','%'.$request->q.'%')->get();

            if(count($school) > 0)
            {
                $classroom = Classroom::with('school')->where('school_id',$school[0]->id)->paginate(5);

                return $classroom;
            }

            if(count($school) == 0)
            {
                $classroom = Classroom::with('school')->where('id','LIKE','%'.$request->q.'%')->orWhere('title','LIKE','%'.$request->q.'%')->paginate(5);

                return $classroom;
            }

        }
        else
        {
            return Classroom::with('school')->paginate(5);
        }

    }

    public function get_classroom_school()
    {
        return School::all();
    }
}
