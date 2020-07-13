<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Session;
use App\Models\Submission;
use App\Models\Attendance;
use App\Models\Teacher;
use App\Models\Student;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;


class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

    public function view_dashboard(Request $request)
    {
        $session = Session::all();
        $section = Section::all();

        //assignments
        $assignment_count = 0;
        foreach($session as $session1)
        {
            foreach($session1->assignments as $asgn)
            {
                $assignment_count += 1;
            }
        }
        
        //submissions
        $submission = Submission::all();
        $submission_count = count($submission);

        //live classes
        $live_class_count = 0;
        foreach($session as $session2)
        {
            if($session2->state == "enable")
            {
                $live_class_count += 1;
            }
        }

        //Teacher count
        $teacher = Teacher::all();
        $teacher_count = count($teacher);

        //Active teacher count
        $active_teacher_count = 0 ;
        foreach($teacher as $teacher)
        {
            $user = User::find($teacher->user_id);
            $check1 = Attendance::where('user_id', $user->id)
                                ->whereDate('check_in', '>=', Carbon::today())
                                ->get();
            if(count($check1) > 0)
            {
                $active_teacher_count += 1;
            }
            else continue;
        }

        //Student count
        $student = Student::all();
        $student_count = count($student);

        //Active student count
        // $active_student_count = 0 ;
        // foreach($student as $student)
        // {
        //     $user = User::find($student->user_id);
        //     $check2 = Attendance::where('user_id', $user->id)
        //                         ->whereDate('check_in', '>=', Carbon::today())
        //                         ->get();
        //     if(count($check2) > 0)
        //     {
        //         $active_student_count += 1;
        //     }
        //     else continue;
        // }

        //time spent this month

        $attendances = Attendance::where('user_id', $request->user('api')->id)
                        ->whereDate('check_in', '<', Carbon::now()->addMonths(1))
                        ->whereDate('check_in', '>', Carbon::now()->subMonths(1))
                        ->get();
        $previous_work_hours = 0;
        $counter = -1;
        if(count($attendances) > 0){
            foreach($attendances as $attendance)
            {
                if($attendance->check_in && $attendance->check_out)
                {
                    $check_in = Carbon::parse($attendance->check_in);
                    $check_out = Carbon::parse($attendance->check_out);
                    $previous_work_hours += $check_in->diffInSeconds($check_out);
                }
                $counter++;
            }

            if($attendances[$counter]->check_out)
            {
                $wh = gmdate('H:i:s', $previous_work_hours);
                $check = 'out';
                $days = gmdate('d H:i:s', $previous_work_hours);
                $total_days = substr($days,0,2)-1;
            }
            else
            {
                $current_checkin = Carbon::parse($attendances[$counter]->check_in);
                $currenttime = Carbon::now()->addHours(5);
                $work_hours = $current_checkin->diffInSeconds($currenttime);
                $total_work_hours = $work_hours + $previous_work_hours;
                $wh = gmdate('H:i:s', $total_work_hours);
                $check = 'in';
                $days = gmdate('d H:i:s', $total_work_hours);
                $total_days = substr($days,0,2)-1;
            }
        }
        else
        {
            $wh = gmdate('H:i:s',0);
            $check = 'out';
            $total_days = 0;
        }

        return [
            'assignment_count' => $assignment_count,
            'submission_count' => $submission_count,
            'live_class_count' => $live_class_count,
            'teacher_count' => $teacher_count,
            'active_teacher_count' => $active_teacher_count,
            'student_count' => $student_count,
            'wh' => $wh,
            'check' => $check,
            'total_days' => $total_days,
        ];
    }
}
