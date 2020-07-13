<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $attendance = DB::table('attendances')
        ->join('users', 'attendances.user_id','=', 'users.id')
        ->select('attendances.id','attendances.check_in','attendances.check_out','users.name AS user_name')
        ->where('attendances.user_id', auth('api')->user()->id)
        ->paginate(10);

        return $attendance;
    }

    public function check_in(Request $request)
    {
        Attendance::create([
            'user_id' => $request->user_id,
            'check_in' => Carbon::now()->addHours(5),
        ]);
        $check_in = Carbon::now()->addHours(5);
        echo ($check_in);    
    }

    public function check_out(Request $request)
    {
        DB::table('attendances')
            ->where('user_id',$request->user_id)
            ->where('check_in','>',Carbon::today())
            ->update(['check_out' => Carbon::now()->addHours(5)]); 
    }

    public function find_check_in_out_status(Request $request)
    {
        $status = DB::table('attendances')
            ->where('user_id',$request->user_id)
            ->where('check_in','>',Carbon::today())
            ->get();
        echo json_encode($status);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        //
    }
}
