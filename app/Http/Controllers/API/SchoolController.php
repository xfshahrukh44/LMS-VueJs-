<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\School;
use App\Models\Program;
use Carbon\Carbon;
use DB;

class SchoolController extends Controller
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
        return $school = DB::table('schools')
                    ->join('programs','programs.id','=','schools.program_id')
                    ->select('schools.id as school_id','programs.id as program_id','schools.location','schools.title as school_title', 'programs.title as program_title')
                    ->orderBy('schools.id','desc')
                    ->paginate(5);
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
            'school_title' => 'required|string|max:20',
            'program_id' => 'required|integer|max:20',
            'location' => 'required|string|max:50',

        ]);
        return School::create([
            'title'=>$request->school_title,
            'program_id' => $request->program_id,
            'location'=>$request->location,
        ]);
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
            'school_title' => 'required|string|max:20',
            'program_id' => 'required|integer|max:20',
            'location' => 'required|string|max:50',

        ]);

        $school = School::where('id',$id);
        $school->update([
            'title'=>$request->school_title,
            'program_id' => $request->program_id,
            'location'=>$request->location,
        ]);
        return['message'=>'Successfull'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        School::find($id)->delete();

        return ['message'=>'School Deleted'];
    }

    public function search()
    {
        if ($search = \Request::get('q')){
            if(preg_match('(2020|2021)', $search) > 0){
                $school = School::where(function($query) use ($search){
                    $query->where('created_at','LIKE',"%".Carbon::parse($search)->format('Y-m-d')."%");
                })->paginate(5);
                return $school;
            }
            else{
                $school = DB::table('schools')
                    ->join('programs','programs.id','=','schools.program_id')
                    ->select('schools.id as school_id','programs.id as program_id','schools.location','schools.title as school_title', 'programs.title as program_title')
                    ->where('schools.id','LIKE',"%$search%")
                    ->orWhere('schools.title','LIKE',"%$search%")
                    ->orWhere('programs.title','LIKE',"%$search%")
                    ->orWhere('schools.location','LIKE',"%$search%")
                    ->paginate(5);

                return $school;
            }
        }
        else{
            return $school = DB::table('schools')
                    ->join('programs','programs.id','=','schools.program_id')
                    ->select('schools.id as school_id','programs.id as program_id','schools.location','schools.title as school_title', 'programs.title as program_title')
                    ->orderBy('schools.id','desc')
                    ->paginate(5);
        }
    }

    public function get_school_program()
    {
        return Program::all();
    }
}
