<?php

namespace App\Modules\Student\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Models\User;
use App\Modules\User\Models\Role;
use App\Modules\Student\Models\School;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\Batch;
use App\Modules\Student\Models\BatchType;
use App\Modules\Student\Models\Grade;
use App\Modules\Student\Models\Subject;
use App\Modules\Student\Models\BatchDay;
use App\Modules\Student\Models\BatchTime;
use App\Modules\Student\Models\BatchDaysHasBatchTime;
use App\Modules\Student\Models\BatchHasDaysAndTime;
use App\Modules\Student\Models\BatchHasStudent;
use App\Modules\Teacher\Models\TeacherDetail;


use Illuminate\Http\Request;
use JWTAuth;
use Datatables;
use Storage;
use File;
use Entrust;
use DB;
use Log;
use Carbon\Carbon;

class SchoolWebController extends Controller {

	/******************************************************
    * Show the information of all Schools in a data table *
    *******************************************************/
    public function allSchools() {
        return view('Student::schools/all_schools');
    }

    public function getSchools() {
    $schools = School::all();
    return Datatables::of($schools)
                    ->addColumn('Link', function ($schools) {
                        if((Entrust::can('user.update') && Entrust::can('user.delete')) || true) {
                        return '<a href="' . url('/school') . '/' . $schools->id . '/edit/' . '"' . 'class="btn bg-green margin"><i class="glyphicon glyphicon-edit"></i> Edit</a>' .'&nbsp &nbsp &nbsp'.
                                '<a class="btn bg-red margin" id="'. $schools->id .'" data-toggle="modal" data-target="#confirm_delete">
                                <i class="glyphicon glyphicon-trash"></i> Delete
                                </a>';
                        }
                        else {
                            return 'N/A';
                        }
                    })
                    ->make(true);
    }

	/****************
    * Create School *
    *****************/
    public function addSchool() {
        return view('Student::schools/create_school');
    }

    public function addSchoolProcess(Request $request){
        School::create($request->all());
        return back();     
    }

    /**************************
    * Edit and Update a Subject *
    ***************************/
    public function editSchool(School $school) {
    	return view('Student::schools/edit_school')
        ->with('getSchool', $school);
    }

    public function schoolUpdateProcess(Request $request, School $school) {
        $school->update( $request->all()); 
        return redirect('/all_schools');
    }

    /******************
    * Delete a Subject*
    *******************/ 
    public function deleteSchool(Request $request, School $school) {
        $school->delete();
        return back();
    }

    public function edit_std_phn_num() {
        

        // $old_data = DB::connection('myconnection')->select("select id, phone_home, phone_away from students");
        // foreach ($old_data as $old) {
        //     // return $old->id;
        //     // return $old;
        //     // $student = Student::find($old->id);
        //     // $student = DB::connection('mysql')->update("select id, phone_home, phone_away from students");
        //     $affected = DB::connection('mysql')->update('update students set student_phone_number = ?, 
        //                                                 guardian_phone_number = ?  
        //                                                 where id = ?', [$old->phone_home, $old->phone_away, $old->id]);
        // }
        


        // $students = Student::with('batch')->has('batch')->get();

        // foreach ($students as $student) {
        //     $batches = $student->batch;
        //     foreach ($batches as $batch) {
        //         // $batch_has_student = BatchHasStudent::where('batch_id', $batch->id)->where('students_id', $student->id)->get();
        //         $current_date = new Carbon('first day of this month');
        //         $current_date = Carbon::parse($current_date->toDateString());
        //         $batch_start_date = Carbon::createFromFormat('d/m/Y', '01/01/2018')->format('Y-m-d '); 
        //         $batch_start_date = Carbon::parse($batch_start_date);
        //         $joining_date = $batch_start_date->toDateString();
        //         // $batch_has_student = BatchHasStudent::where('batch_id',$batch->id)
        //         //                                     ->where('students_id', $student->id)
        //         //                                     ->update(['joining_date' => $joining_date]);
        //         // if ($batch_start_date->gte($current_date)) {
        //         //     $joining_date = $batch_start_date->toDateString();
        //         //     $last_payment_date = $batch_start_date->subMonths(1);
        //         //     $last_payment_date = $last_payment_date->toDateString();
        //         //     $batch_has_student = BatchHasStudent::where('batch_id',$batch->id)
        //         //                                     ->where('students_id', $student->id)
        //         //                                     ->update([
        //         //                                         'last_paid_date' => $last_payment_date,
        //         //                                         'joining_date' => $joining_date
        //         //                                         ]);
        //         //     return $joining_date . " " . $last_payment_date;
        //         // }
        //         // else {
        //         //     $joining_date = $batch_start_date->toDateString();
        //         //     $batch_has_student = BatchHasStudent::where('batch_id',$batch->id)
        //         //                                     ->where('students_id', $student->id)
        //         //                                     ->update(['joining_date' => $joining_date]);
        //         //     return $joining_date;
        //         // }

        //         // return $temp . " " . $current_date . " " . $batch_start_date;
        //     }
        // }

        $batches = DB::table('students')
                    ->leftJoin('invoice_masters', 'students.id', '=', 'invoice_masters.students_id')
                    ->leftJoin('invoice_details', 'invoice_details.invoice_masters_id', '=', 'invoice_masters.id')
                    ->leftJoin('batch', 'invoice_details.batch_id', '=', 'batch.id')
                    // ->where('invoice_details.payment_from', '=', $get_date_month_year)
                    // ->where('batch.id', '=', $request->batch_id)
                    ->whereNull('deleted_at')
                    // ->where('refund', '=', 0)
                    ->select('students.name','students.student_phone_number','invoice_details.price',
                            'invoice_details.invoice_masters_id', 'invoice_details.payment_from', 
                            'invoice_details.batch_id');

        $batches = DB::table('batch')
                    ->leftJoin('teacher_details', 'batch.teacher_details_id', '=', 'teacher_details.id')
                    ->leftJoin('batch_has_students', 'batch.id', '=', 'batch_has_students.batch_id')
                    ->leftJoin('students', 'batch_has_students.students_id', '=', 'students.id')
                    ->leftJoin('invoice_details', 'invoice_details.batch_id', '=', 'batch.id')
                    ->where('invoice_details.payment_from', '!=', "2017-07-01")
                    ->where('students.deleted_at', '=', NULL)
                    ->where('teacher_details.id', '=', 1)
                    ->groupBy('batch.id')
                    ->select('batch.id as batch_id',
                            'batch.name as batch_name',
                            DB::raw("COUNT(DISTINCT(students.id)) as total_no_students"))            
                    ->get();

        return $batches;
    }
}