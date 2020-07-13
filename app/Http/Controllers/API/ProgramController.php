<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Program;
use Carbon\Carbon;
use DB;

class ProgramController extends Controller
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
        return Program::orderBy('id', 'desc')->paginate(5);
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
            'title' => 'required|string|max:50'

        ]);
        return Program::create([
            'title'=>$request->title
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
            'title' => 'required|string|max:50'

        ]);

        $program = Program::find($id);
        $program->update($request->all());
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
        $program = Program::find($id);
        $program->delete();
        return ['message'=>'Program Deleted'];
    }

    public function search()
    {
        if ($search = \Request::get('q')){
            if(preg_match('(2020|2021)', $search) > 0){
                $program = Program::where(function($query) use ($search){
                    $query->where('created_at','LIKE',"%".Carbon::parse($search)->format('Y-m-d')."%");
                })->paginate(5);
                return $program;
            }
            else{
                $program = Program::where(function($query) use ($search){
                    $query->where('id','LIKE',"%$search%")->orWhere('title','LIKE',"%$search%");
                })->paginate(5);
                return $program;
            }
        }
        else{
            return Program::orderBy('id', 'desc')->paginate(5);
        }
    }
}
