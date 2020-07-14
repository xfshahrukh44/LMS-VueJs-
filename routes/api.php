<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::apiResources(['user'=>'API\UserController']);
Route::apiResources(['program'=>'API\ProgramController']);
Route::apiResources(['school'=>'API\SchoolController']);
Route::apiResources(['classroom'=>'API\ClassroomController']);
Route::apiResources(['section'=>'API\SectionController']);
Route::apiResources(['course'=>'API\CourseController']);
Route::apiResources(['student'=>'API\StudentController']);
Route::apiResources(['teacher'=>'API\TeacherController']);
Route::apiResources(['session'=>'API\SessionController']);
Route::apiResources(['attendance'=>'API\AttendanceController']);
Route::apiResources(['assignment'=>'API\AssignmentController']);
Route::apiResources(['quiz'=>'API\QuizController']);
Route::apiResources(['quizsubmission'=>'API\QuizSubmissionController']);
Route::apiResources(['question'=>'API\QuestionController']);
Route::apiResources(['option'=>'API\OptionController']);

Route::get('finduser','API\UserController@search');
Route::get('findprogram','API\ProgramController@search');
Route::get('findschool','API\SchoolController@search');
Route::get('findclassroom','API\ClassroomController@search');
Route::get('findsection','API\SectionController@search');
Route::get('findcourse','API\CourseController@search');
Route::get('findstudent','API\StudentController@search');
Route::get('findteacher','API\TeacherController@search');
Route::get('findsession','API\SessionController@search');


Route::get('get_school_program','API\SchoolController@get_school_program');
Route::get('get_classroom_school','API\ClassroomController@get_classroom_school');
Route::get('get_section_classroom','API\SectionController@get_section_classroom');
Route::get('get_course_classroom','API\CourseController@get_course_classroom');
Route::get('get_student_section','API\StudentController@get_student_section');
Route::get('get_user_student','API\StudentController@get_user_student');
Route::get('get_user_teacher','API\TeacherController@get_user_teacher');
Route::get('get_session_section','API\SessionController@get_session_section');
Route::get('get_session_course','API\SessionController@get_session_course');
Route::get('get_session_teacher','API\SessionController@get_session_teacher');


Route::get('chart_data','API\UserController@chart_data');
Route::get('get_user_name','API\UserController@get_user_name');
Route::get('get_number','API\UserController@get_number');
Route::get('get_line','API\UserController@line_data');
Route::get('adminhome','API\AdminController@view_dashboard');
Route::get('statusURL','API\AttendanceController@find_check_in_out_status');
Route::get('checkInURL', 'API\AttendanceController@check_in');
Route::get('checkOutURL', 'API\AttendanceController@check_out');



