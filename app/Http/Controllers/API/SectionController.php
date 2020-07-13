<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Classroom;
use DB;

class SectionController extends Controller
{
     public function index()
    {
        return $section = Section::with('classroom', 'sessions.course', 'students')->paginate(5);
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
            'classroom_id' => 'required|integer|max:20'

        ]);
        return Section::create($request->all());
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
            'classroom_id' => 'required|integer|max:20'

        ]);
        $section = Section::find($id)->update($request->all());
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
        Section::find($id)->delete();

        return ['message'=>'School Deleted'];
    }

    public function search(Request $request)
    {
        if($request->q)
        {

            $classroom = Classroom::where('title','LIKE','%'.$request->q.'%')->get();

            if(count($classroom) > 0)
            {
                $section = Section::with('classroom')->where('classroom_id',$classroom[0]->id)->paginate(5);

                return $section;
            }

            if(count($classroom) == 0)
            {
                $section = Section::with('classroom')->where('id','LIKE','%'.$request->q.'%')->orWhere('title','LIKE','%'.$request->q.'%')->paginate(5);

                return $section;
            }

        }
        else
        {
            return Section::with('classroom')->paginate(5);
        }

    }

    public function get_section_classroom()
    {
        return Classroom::all();
    }
}
