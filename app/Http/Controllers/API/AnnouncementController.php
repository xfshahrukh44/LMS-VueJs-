<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Announcement;
use App\Models\Teacher;
use App\Models\Student;
use App\User;
use App\Notifications\AnnouncementNotification;
use Gate;
use DB;
use Carbon\Carbon;

class AnnouncementController extends Controller
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
        if(Gate::allows('isAdmin'))
        {
            return Announcement::with('user')->latest()->get();
        }
        if(Gate::allows('isTeacher'))
        {
            return Announcement::with('user')->where('for_teachers', 1)->latest()->get();
        }
        if(Gate::allows('isStudent'))
        {
            return Announcement::with('user')->where('for_students', 1)->latest()->get();
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
        if(Gate::allows('isAdmin'))
        {
            $this->validate($request, [
                'title' => 'required',
                'content' => 'required',
            ]);
            $announcement = Announcement::create($request->all());

            // Get users to be notified
            if($request['for_teachers'] == 1 && $request['for_students'] == 1)
            {
                $users = User::where('type', 'teacher')
                            ->orWhere('type', 'student')
                            ->orWhere('type', 'admin')
                            ->get();
            }
            else if($request['for_teachers'] == 1)
            {
                $users = User::where('type', 'teacher')
                            ->orWhere('type', 'admin')
                            ->get();
            }
            else if($request['for_students'] == 1)
            {
                $users = User::where('type', 'student')
                            ->orWhere('type', 'admin')
                            ->get();
            }
            else
            {
                $users = User::where('type', 'admin')->get();
            }
            // Generate notification for users
            foreach($users as $user)
            {
                $user->notify(new AnnouncementNotification($announcement));
            }
        }
        else
        {
            return [
                'success' => false,
                message => 'Not allowed'
            ];
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
        return Announcement::find($id);
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
        $this->validate($request, [
            'title' => 'required',
            'content' => 'required',
        ]);
        return Announcement::find($id)->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Announcement::find($id)->delete();
    }

    public function notifications(Request $request)
    {
        $main_count = 0;
        $user = User::find(auth('api')->user()->id);

        // general (for every user)
        $announcement_count = count(DB::table('notifications')
                            ->where('type', 'App\Notifications\AnnouncementNotification')
                            ->where('notifiable_id', $user->id)
                            ->where('read_at', NULL)
                            ->get());

        // Students only
        if($user->type == 'student')
        {
            $assignment_count = count(DB::table('notifications')
                                ->where('type', 'App\Notifications\AssignmentNotification')
                                ->where('notifiable_id', $user->id)
                                ->where('read_at', NULL)
                                ->get());

            $quiz_count = count(DB::table('notifications')
                                ->where('type', 'App\Notifications\QuizNotification')
                                ->where('notifiable_id', $user->id)
                                ->where('read_at', NULL)
                                ->get());

            $main_count += $announcement_count + $assignment_count + $quiz_count;

            return [
                'main_count' => $main_count,
                'announcement_count' => $announcement_count,
                'assignment_count' => $assignment_count,
                'quiz_count' => $quiz_count,
            ];
        }

        else
        {
            $main_count += $announcement_count;
        
            return [
                'main_count' => $main_count,
                'announcement_count' => $announcement_count,
            ];
        }
    }

    public function mark_all_read()
    {
        $user = User::find(auth('api')->user()->id);
        $notifications = DB::table('notifications')
                                ->where('notifiable_id', $user->id)
                                ->get();
        foreach($notifications as $notification)
        {
            DB::table('notifications')
            ->where('id', $notification->id)
            ->delete();
        }
    }
}
