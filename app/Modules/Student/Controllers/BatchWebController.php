<?php

namespace App\Modules\Student\Controllers;

use App\Http\Controllers\Controller;


use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\Batch;
use App\Modules\Student\Models\BatchType;
use App\Modules\Student\Models\Grade;
use App\Modules\Student\Models\Subject;
use App\Modules\Teacher\Models\TeacherDetail;
use App\Modules\Student\Models\BatchHasStudent;

use Illuminate\Http\Request;
use JWTAuth;
use Datatables;
use Entrust;
use DB;
use Log;
use Carbon\Carbon;

class BatchWebController extends Controller {
	/******************************************************
    * Show the information of all Batches in a data table *
    *******************************************************/
    public function allBatches() {
        return view('Student::batches/all_batches');
    }

    public function getBatches($teacherDetailID) {
        
        $batches = Batch::with('batchType', 'grade', 'student')->where('teacher_details_id', $teacherDetailID)->get();
        
        return Datatables::of($batches)
            ->addColumn('total_students', function ($batches) {
                return count($batches->student);
            })
            ->addColumn('Link', function ($batches) {
                if((Entrust::can('user.update') && Entrust::can('user.delete')) || true) {
                // return '<a href="' . url('/batch') . '/' . $batches->id . '/edit/' . '"' . 'class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> Edit</a>' .'&nbsp &nbsp &nbsp'.
                //         '<a class="btn btn-xs btn-danger" id="'. $batches->id .'" data-toggle="modal" data-target="#confirm_delete">
                //         <i class="glyphicon glyphicon-trash"></i> Delete
                //         </a>';
                
                return '<a class="btn bg-yellow margin" id="'. $batches->id .'" data-toggle="modal" data-target="#confirm_edit">
                        <i class="glyphicon glyphicon-trash"></i> Edit </a>' .'&nbsp &nbsp &nbsp'.
                        '<a class="btn bg-red margin" id="'. $batches->id .'" data-toggle="modal" data-target="#confirm_delete">
                        <i class="glyphicon glyphicon-trash"></i> Delete
                        </a>';

                }
                else {
                    return 'N/A';
                }
            })
            ->make(true);
    }


    /*********************
    * Create a new Batch *
    **********************/
    public function addBatch() {
        $batchType = BatchType::all();
        $getGrades = Grade::all();
        return view('Student::batches/create_batch',compact("batchType", "getGrades","getSubjects"));
    }

    public function addBatchProcess(Request $request) {
        return $request->all();
        $batch = Batch::create($request->all());
        Batch::where('id', $batch->id)
          ->update(['name' => $batch_name]);
        return redirect("/all_batches");
    }

    public function addNewBatchProcess(\App\Http\Requests\AddNewBatchRequest $request) {
        // return $request->all();
        if($request->batch_number == null) {
            $batch_number = 1;
        }
        else {
            $batch_number = $request->batch_number;
        }
        
        $batch = Batch::create($request->all());

        $subject_name = Subject::find($request->subjects_id);
        $subject_name = substr($subject_name->name,0,3);

        $batch_type = BatchType::find($request->batch_types_id);
        $batch_type = substr($batch_type->name,0,3);
        
        $grade = Grade::find($request->grades_id);
        $grade = $grade->name;

        $year = $request->end_date;
        $year = substr( $year,(strlen($year) - 2),strlen($year));
        
        $batch_name = $subject_name . "-" . $batch_type . "-" . $grade . "-" . $year ."-". $batch_number;
        
        Batch::where('id', $batch->id)->update(['name' => $batch_name]);
        return back();
    }

    /**************************
    * Select2 helper Function *
    ***************************/
    public function getAllBatch(Request $request) {
        
        // $batch_information = Batch::where('batch_types_id',$request->input('batchType_id'))
        //                             ->where('grades_id', $request->input('grades_id'))
        //                             ->where('subjects_id', $request->input('subject_id'))
        //                             ->get(['id', 'name as text']);
        
        // $batch_information = Batch::where('subjects_id', $request->input('subject_id'))
        //                             ->get(['id', 'CONCAT(name, " ", start_date) as text']);
        
        $batch_information = Batch::where('subjects_id', $request->input('subject_id'))
                                    ->selectRaw('id, CONCAT(name, " ( ", start_date, " -> ", end_date, " )") as text')
                                    ->get();

        return response()->json($batch_information);
    }

    /**************************
    * Edit and Update a Batch *
    ***************************/
    public function editBatch($id) {
        
        $getBatch = Batch::with('batchType', 'grade','subject')->find($id);
        return response()->json($getBatch);
    }

    public function batchUpdateProcess(Request $request) {
        if($request->batch_number == null) {
            $batch_number = 1;
        }
        else {
            $batch_number = $request->batch_number;
        }
        
        $subject_name = Subject::find($request->subjects_id);
        $subject_name = substr($subject_name->name,0,3);

        $batch_type = BatchType::find($request->batch_types_id);
        $batch_type = substr($batch_type->name,0,3);
        
        $grade = Grade::find($request->grades_id);
        $grade = $grade->name;

        $year = $request->end_date;
        $year = substr( $year,(strlen($year) - 2),strlen($year));
        
        $batch_name = $subject_name . "-" . $batch_type . "-" . $grade . "-" . $year ."-". $batch_number;
        
        $batch = Batch::find($request->batch_id);
        $batch->update( $request->all());
        Batch::where('id', $request->batch_id)->update(['name' => $batch_name]);
    }

    /*****************
    * Delete a Batch *
    ******************/
    public function deleteBatch(Request $request, $id) {
        // BatchHasStudent::where('batch_id', $id)->delete();
        $batch = Batch::with('student')->find($id);
        $students = $batch->student;
        foreach ($students as $student) {
            DB::table('students_has_subjects')
                ->where('students_id', '=', $student->id)
                ->where('subjects_id', '=', $batch->subjects_id)
                ->delete();
        }
        $batch->delete();
        $batch->student()->detach();
        $batch->subject()->detach();
    }

    public function batchWiseStudentPage() {
        $getSubjects = Subject::all();
        return view('Student::batches/batch_wise_student_page')
        ->with('getSubjects', $getSubjects);
    }

    public function get_all_batches_for_a_subject(Request $request)  {
        
        $batches = Batch::with('batchType', 'subject', 'grade','teacherDetail.user','student')
                            ->where('subjects_id',$request->subjects_id)
                            ->get();
        
        return Datatables::of($batches)
            ->addColumn('total_number_of_students', function ($batches) {
                return count($batches->student);
            })
            ->addColumn('total_expected_amount', function ($batches) {
                return count($batches->student) * $batches->price;
            })
            ->addColumn('number_of_paid_students', function ($batches) {
                    $no_of_paid_students = 0;
                    $now = new Carbon('first day of this month');
                    $now = $now->toDateString();
                    $all_students = $batches->student;
                    for ($i=0; $i < count($all_students); $i++) { 
                        $student = $all_students[$i];
                        if ($student->pivot->last_paid_date >= $now)  {
                            $no_of_paid_students = $no_of_paid_students + 1;
                        }
                    }
                    return $no_of_paid_students;
                
            })
            ->addColumn('total_paid_amount', function ($batches) {
                    $no_of_paid_students = 0;
                    $now = new Carbon('first day of this month');
                    $now = $now->toDateString();
                    $all_students = $batches->student;
                    for ($i=0; $i < count($all_students); $i++) { 
                        $student = $all_students[$i];
                        if ($student->pivot->last_paid_date >= $now)  {
                            $no_of_paid_students = $no_of_paid_students + 1;
                        }
                    }
                    return $no_of_paid_students * $batches->price;
                
            })
            ->addColumn('number_of_unpaid_students', function ($batches) {
                    $no_of_unpaid_students = 0;
                    $now = new Carbon('first day of this month');
                    $now = $now->toDateString();
                    $all_students = $batches->student;
                    for ($i=0; $i < count($all_students); $i++) { 
                        $student = $all_students[$i];
                        if ($student->pivot->last_paid_date < $now)  {
                            $no_of_unpaid_students = $no_of_unpaid_students + 1;
                        }
                    }
                    return $no_of_unpaid_students;
            })
            ->addColumn('total_unpaid_amount', function ($batches) {
                    $no_of_unpaid_students = 0;
                    $now = new Carbon('first day of this month');
                    $now = $now->toDateString();
                    $all_students =  $batches->student;
                    for ($i=0; $i < count($all_students); $i++) { 
                        $student = $all_students[$i];
                        if ($student->pivot->last_paid_date < $now)  {
                            $no_of_unpaid_students = $no_of_unpaid_students + 1;
                        }
                    }
                    return $no_of_unpaid_students * $batches->price;
                
            })
            ->addColumn('Link', function ($batches) {
                return '<a href="' . url('/students_all_students_per_batch_page') . '/' . $batches->id . '/'.count($batches->student) . '"' . 'class="btn bg-purple margin" target="_blank"><i class="glyphicon glyphicon-edit"></i> Detail</a>';
            })
            ->make(true);
    }

    public function all_students_per_batch_page($batch_id, $total_student) {
        
        $batch = Batch::with('student')->find($batch_id);
        
        $number_of_inactive_paid_students = 0;
        $number_of_active_paid_students = 0;
        $number_of_unpaid_students = 0;
        $current_date = new Carbon('first day of this month');
        $current_date = Carbon::parse($current_date->toDateString());        
        
        foreach ($batch->student as $key => $student) {
            $last_paid_date = Carbon::parse($student->pivot->last_paid_date);
            if ($last_paid_date->eq($current_date)) {
                $number_of_active_paid_students++;
            }
            elseif ($last_paid_date->gt($current_date)) {
                $number_of_inactive_paid_students++;
            }
            else {
                $number_of_unpaid_students++;
            }
        }
        
        return view('Student::batches/get_all_students_per_batch_page')
                ->with('batch_id', $batch_id)
                ->with('batch_name', $batch->name)
                ->with('schedule', $batch->schedule)
                ->with('number_of_inactive_paid_students', $number_of_inactive_paid_students)
                ->with('number_of_active_paid_students', $number_of_active_paid_students)
                ->with('number_of_unpaid_students', $number_of_unpaid_students)
                ->with('total_student', $total_student);
    }


    public function students_get_all_inactive_students_per_batch(Request $request) {
        
        $current_date = new Carbon('first day of this month');
        $students = DB::table('batch')
                    ->leftJoin('batch_has_students', 'batch_has_students.batch_id', '=', 'batch.id')
                    ->leftJoin('students', 'students.id', '=', 'batch_has_students.students_id')
                    ->leftJoin('batch_types', 'batch_types.id', '=', 'students.batch_types_id')
                    ->leftJoin('schools', 'schools.id', '=', 'students.schools_id')
                    ->where('batch.id', '=', $request->batch_id)
                    ->where('joining_date', '>', $current_date)
                    ->whereNull('students.deleted_at')
                    ->whereNull('batch.deleted_at')
                    ->select('student_permanent_id', 'students.id as student_id', 'students.student_phone_number as student_phone_number','students.guardian_phone_number as guardian_phone_number','students.name as student_name','schools.name as school_name', 'batch_types.name as batch_type_name','last_paid_date', 'joining_date');
        
        return Datatables::of($students)
        ->addColumn('payment_status', function ($students) {
                if((Entrust::can('user.update') && Entrust::can('user.delete')) || true) {
                    
                    $current_date = new Carbon('first day of this month');
                    $current_date = Carbon::parse($current_date->toDateString());
                    
                    $last_paid_date = Carbon::parse($students->last_paid_date);
                    
                    $difference_in_month = $last_paid_date->gte($current_date);
                    
                    return $difference_in_month;
                }
                else {
                    return 'N/A';
                }
            })
        ->addColumn('Link', function ($students) {
                        if((Entrust::can('user.update') && Entrust::can('user.delete')) || true) {
                        return  '<a href="' . url('/students_student') . '/' . $students->student_id . '/detail/' . '"' . 'class="btn bg-purple margin" target=_blank><i class="glyphicon glyphicon-edit"></i> Detail</a>';
                        }
                        else {
                            return 'N/A';
                        }
                    })
        ->make(true);
    }


    public function students_get_all_active_students_per_batch(Request $request) {
        
        $current_date = new Carbon('first day of this month');
        $students = DB::table('batch')
                    ->leftJoin('batch_has_students', 'batch_has_students.batch_id', '=', 'batch.id')
                    ->leftJoin('students', 'students.id', '=', 'batch_has_students.students_id')
                    ->leftJoin('batch_types', 'batch_types.id', '=', 'students.batch_types_id')
                    ->leftJoin('schools', 'schools.id', '=', 'students.schools_id')
                    ->where('batch.id', '=', $request->batch_id)
                    // ->where('last_paid_date', '<=', $current_date)
                    ->where('joining_date', '<=', $current_date)
                    ->whereNull('students.deleted_at')
                    ->whereNull('batch.deleted_at')
                    ->select('student_permanent_id', 'students.id as student_id', 'students.student_phone_number as student_phone_number','students.guardian_phone_number as guardian_phone_number','students.name as student_name','schools.name as school_name', 'batch_types.name as batch_type_name','last_paid_date', 'joining_date');

        return Datatables::of($students)
        ->addColumn('payment_status', function ($students) {
                if((Entrust::can('user.update') && Entrust::can('user.delete')) || true) {
                    
                    $current_date = new Carbon('first day of this month');
                    $current_date = Carbon::parse($current_date->toDateString());
                    
                    $last_paid_date = Carbon::parse($students->last_paid_date);
                    
                    $difference_in_month = $last_paid_date->gte($current_date);
                    
                    return $difference_in_month;
                }
                else {
                    return 'N/A';
                }
            })
        ->addColumn('Link', function ($students) {
                        if((Entrust::can('user.update') && Entrust::can('user.delete')) || true) {
                        return  '<a href="' . url('/students_student') . '/' . $students->student_id . '/detail/' . '"' . 'class="btn bg-purple margin" target=_blank><i class="glyphicon glyphicon-edit"></i> Detail</a>';
                        }
                        else {
                            return 'N/A';
                        }
                    })
        ->make(true);
    }

}