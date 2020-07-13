<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Models\Teacher;

class TeacherController extends Controller
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
        return $teacher = Teacher::with('user')->paginate(5);
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
            'address' => 'required|string|max:30'

        ]);
        return Teacher::create($request->all());
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
            'address' => 'required|string|max:30'

        ]);
        $teacher = Teacher::find($id)->update($request->all());
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
        Teacher::find($id)->delete();

        return ['message'=>'School Deleted'];
    }

    public function search(Request $request)
    {
        if($request->q)
        {
            $user = User::where('name','LIKE','%'.$request->q.'%')->get();
            
            if(count($user) > 0)
            {
                $teacher = Teacher::with('user')->where('user_id',$user[0]->id)->paginate(5);

                return $teacher;   
            }

            else
            {
                $teacher = Teacher::with('user')->where('id','LIKE','%'.$request->q.'%')->orWhere('name','LIKE','%'.$request->q.'%')->orWhere('contact','LIKE','%'.$request->q.'%')->orWhere('address','LIKE','%'.$request->q.'%')->paginate(5);

                return $teacher;
            }

        }
        else
        {
            return Teacher::with('user')->paginate(5);
        }

    }

    public function get_user_teacher(Request $request)
    {
        return User::where('type','teacher')->get();
    }
}
