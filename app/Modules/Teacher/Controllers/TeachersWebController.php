<?php

namespace App\Modules\Teacher\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Models\User;
use App\Modules\User\Models\Role;
use App\Modules\Student\Models\School;
use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\Batch;
use App\Modules\Student\Models\BatchType;
use App\Modules\Student\Models\Grade;
use App\Modules\Student\Models\Subject;
use App\Modules\Teacher\Models\TeacherDetail;
use App\Modules\Student\Models\InvoiceDetail;
use App\Modules\Student\Models\InvoiceMaster;
use App\Modules\Student\Models\Refund;
use Illuminate\Http\Request;
use JWTAuth;
use Datatables;
use Storage;
use File;
use Entrust;
use Carbon\Carbon;
use DB;
use Crypt;


class TeachersWebController extends Controller {

    /******************************************************
    * Show the information of all Teachers in a data table *
    *******************************************************/
	public function allTeachers() {
		return view('Teacher::all_teachers');
    }
    
	public function getTeachers() {
	$teachers = TeacherDetail::with('user')->get();
    return Datatables::of($teachers)
    				->addColumn('Link', function ($teachers) {
    					if((Entrust::can('user.update') && Entrust::can('user.delete')) || true) {
                        return '<a href="' . url('/teacher') . '/' . $teachers->id . '/show/' . '"' . 'class="btn bg-purple margin"><i class="glyphicon glyphicon-edit"></i> Detail</a>' .'&nbsp &nbsp &nbsp'.
                        		'<a href="' . url('/teacher') . '/' . Crypt::encrypt($teachers->id) . '/edit/' . '"' . 'class="btn bg-green margin"><i class="glyphicon glyphicon-edit"></i> Edit</a>' .'&nbsp &nbsp &nbsp'.
                        		'<a class="btn bg-red margin" id="'. $teachers->users_id .'" data-toggle="modal" data-target="#confirm_delete">
                                <i class="glyphicon glyphicon-trash"></i> Delete
                                </a>';
                        }
                        else {
                        	return 'N/A';
                        }
                    })
                    ->make(true);
    }


    /***********************************************************
    * Show the information of a Particular Teacher. Incomplete *
    ************************************************************/
    public function get_one_Teacher($id) {
        $getTeacher = TeacherDetail::with('user')->find($id);
        $batchType = BatchType::all();
        $getGrades = Grade::all();
        $getSubjects = Subject::all();
    	return view('Teacher::show_a_teacher_details')
        ->with('getTeacher', $getTeacher)
        ->with('batchType', $batchType)
        ->with('getGrades', $getGrades)
        ->with('getSubjects', $getSubjects);
    }

    /***********************
    * Create a new Teacher *
    ************************/
    public function addTeacher() {
		return view('Teacher::create_teacher');
	}


	public function addTeacherProcess(\App\Http\Requests\TeacherCreateRequest $request) {
        // return $request->all();
        $user = User::create($request->all());
        $teacher = new TeacherDetail($request->all());
        $teacher->user()->associate($user);
        $teacher->save();

        $user->attachRole(2); 
               
        return redirect("all_teachers");
    }


    /***************************
    * Edit and Update a Teacher*
    ****************************/
    public function editTeacher($id) {
        $nid = Crypt::decrypt($id);
        $getTeacher = TeacherDetail::with('user')->find($nid);
        
        return view('Teacher::edit_teacher')
		->with('getTeacher', $getTeacher);
	}

    public function teacherUpdate(\App\Http\Requests\TeacherCreateRequest $request, $id) {
        $teacherdetail = TeacherDetail::with('user')->find($id);
        $teacherdetail->update( $request->all());
        $user = User::find($teacherdetail->user->id);
        $user->update( $request->all());
    	return redirect('all_teachers');
    }

    
    /******************
    * Delete a Teacher*
    *******************/
	public function deleteTeacher(Request $request, $id) {
        $user = User::find($id);
        $user->teacher_detail()->delete();
		$user->delete();
		return redirect('all_teachers');
	}

    public function teacherPaymentAllBatch()
    {
        $refDate = Carbon::now();
        $refDate = $refDate->toDateString();
        $refDate = Carbon::createFromFormat('Y-m-d', $refDate)->format('d/m/Y');
        return view('Teacher::teacher_payment_for_all_batch',compact('refDate'));
    }

    public function getAllTeacherForPayment(Request $request) {
        
        $search_term = $request->input('term');
        return response()->json(User::whereHas('roles', function($query){
            $query->where('name', 'teacher');
        })->where('name', "LIKE", "%{$search_term}%")->
        get(['id', 'name as text']));


        // dd(User::has('roles', 'admin'));
        $getTeacher = User::where('name', "LIKE", "%{$search_term}%")
                    ->get(['id', 'name as text']);

        // $getTeacher = TeacherDetail::with('user')->get();
        return response()->json($getTeacher);
    }

    
    public function getAllBatchForTeacherPayment(Request $request) {
        $get_payment_date_month_year = \Carbon\Carbon::createFromFormat('d/m/Y', $request->ref_date);
        $get_payment_date_month_year->day = 01;
        $get_payment_date_month_year = $get_payment_date_month_year->toDateString();
        
         
        $teacher_details = TeacherDetail::where('users_id', $request->teacher_user_id)->first();
        $teacher_id = $teacher_details->id;
        $teacher_percentage = $teacher_details->teacher_percentage;
            // SELECT 
            //     batch.id, batch.name, COUNT(DISTINCT (students.id)), COUNT(DISTINCT (invoice_details.id))
            // FROM
            //     batch
            //         JOIN
            //     teacher_details ON batch.teacher_details_id = teacher_details.id
            //         JOIN
            //     batch_has_students ON batch_has_students.batch_id = batch.id
            //         JOIN
            //     students ON students.id = batch_has_students.students_id
            //         LEFT JOIN
            //     invoice_details ON invoice_details.batch_id = batch.id AND invoice_details.payment_from = '2017-07-01'
            // WHERE
            //     teacher_details.id = 1
            //         AND students.deleted_at IS NULL
            //         AND batch.start_date <= '2017-07-01'
            //         AND batch.end_date >= '2017-07-01'
            // GROUP BY batch.id
        
        $teacher_payment_per_batch = DB::table('batch')
                    ->leftJoin('teacher_details', 'batch.teacher_details_id', '=', 'teacher_details.id')
                    ->leftJoin('batch_has_students', 'batch.id', '=', 'batch_has_students.batch_id')
                    ->leftJoin('students', 'batch_has_students.students_id', '=', 'students.id')
                    ->leftJoin('invoice_details', function ($join) use ($get_payment_date_month_year)  {
                        $join->on('invoice_details.batch_id', '=', 'batch.id')
                        ->where('invoice_details.payment_from', '=', $get_payment_date_month_year)
                        ->where('refund', '=', 0);
                    })
                    ->where('batch_has_students.joining_date', '<=', $get_payment_date_month_year)
                    ->where('teacher_details.id', '=', $teacher_id)
                    ->where('students.deleted_at', '=', NULL)
                    ->where('batch.deleted_at', '=', NULL)
                    ->where('batch.start_date', '<=', $get_payment_date_month_year)
                    ->where('batch.end_date', '>=', $get_payment_date_month_year)
                    ->groupBy('invoice_details.batch_id')
                    ->select('batch.id as batch_id',
                        'batch.name as batch_name',
                        'batch.schedule as batch_schedule',
                        DB::raw("COUNT(DISTINCT(students.id)) as total_no_students"),
                        DB::raw("COUNT(DISTINCT(invoice_details.id)) as no_of_paid_students"),
                        DB::raw("(COUNT(DISTINCT(students.id)) - COUNT(DISTINCT(invoice_details.id))) as no_of_unpaid_students"), 
                        DB::raw("(COUNT(DISTINCT(students.id)) * batch.price * " . $teacher_percentage . " / 100 ) as total_expected_amount"),
                        DB::raw("((COUNT(DISTINCT(students.id)) - COUNT(DISTINCT(invoice_details.id))) * batch.price * " . $teacher_percentage . " / 100 ) as pending_amount"),
                        // DB::raw("(COUNT(DISTINCT(invoice_details.id)) * batch.price * " . $teacher_percentage . " / 100 ) as calculated_price"),
                        DB::raw("((COUNT(DISTINCT(invoice_details.id)) * batch.price - invoice_details.discount_amount - invoice_details.due_amount) * " . $teacher_percentage . " / 100 ) as calculated_price"),
                        DB::raw("COUNT(invoice_details.due_amount)  as sum_price"),
                        DB::raw("SUM(invoice_details.price) as final_price")
                        );
                    // return $teacher_payment_per_batch->get();
        return Datatables::of($teacher_payment_per_batch)
        ->addColumn('Link', function ($invoice_details) use($get_payment_date_month_year) {
            return '<a id="batch_'. $invoice_details->batch_id .'"" href="' . url('/batch') . '/' . $invoice_details->batch_id .'/'.$get_payment_date_month_year.'/'.$invoice_details->batch_name. '/get_paid_and_non_paid_std_teacher_payment/'. '"' . 'class="btn bg-purple margin"target="_blank"><i class="glyphicon glyphicon-edit"></i> Detail</a>';
        })
        ->make(true);
    }

    public function allStudentForTeacherPayment($id, $date, $batchName)
    {
        return view('Teacher::teacher_payment_for_a_single_batch')
        ->with('batchID', $id)
        ->with('refDate', $date)
        ->with('batchName', $batchName);
    }

    public function getPaidStudentsForABatch(Request $request)
    {
        $get_date_month_year = Carbon::parse($request->ref_date);
        $get_date_month_year->day = 01;
        $get_date_month_year = $get_date_month_year->toDateString();

        $batches = DB::table('students')
                    ->leftJoin('invoice_masters', 'students.id', '=', 'invoice_masters.students_id')
                    ->leftJoin('invoice_details', 'invoice_details.invoice_masters_id', '=', 'invoice_masters.id')
                    ->leftJoin('batch', 'invoice_details.batch_id', '=', 'batch.id')
                    ->leftJoin('batch_has_students', 'batch_has_students.students_id', '=', 'students.id')
                    ->where('invoice_details.payment_from', '=', $get_date_month_year)
                    ->where('batch.id', '=', $request->batch_id)
                    ->where('students.deleted_at', '=', NULL)
                    ->where('refund', '=', 0)
                    ->groupBy('students.id')
                    ->select('students.name','students.student_phone_number','invoice_details.price',
                            'invoice_details.invoice_masters_id', 'invoice_details.payment_from', 
                            'invoice_details.batch_id', 'batch_has_students.joining_date');
        $teacher_percentage = Batch::with('teacherDetail')->find($request->batch_id);
        $teacher_percentage = $teacher_percentage->teacherDetail->teacher_percentage;
        
        return Datatables::of($batches)
                    ->addColumn('paid_money', function ($batches) use($teacher_percentage) {
                        if((Entrust::can('user.update') && Entrust::can('user.delete')) || true) {
                            return ( ($batches->price * $teacher_percentage)  / 100 ) ;
                        }
                    })->make(true);
    }

    public function getNonPaidStudentsForABatch(Request $request)
    {
        $get_date_month_year = Carbon::parse($request->ref_date);
        $get_date_month_year->day = 01;
        $get_date_month_year = $get_date_month_year->toDateString();
        

        $batches = DB::table('students')
                    ->leftJoin('batch_has_students', 'students.id', '=', 'batch_has_students.students_id')
                    ->leftJoin('batch', 'batch_has_students.batch_id', '=', 'batch.id')
                    ->where('batch_has_students.last_paid_date', '<', $get_date_month_year)
                    ->where('batch.id', '=', $request->batch_id)
                    ->where('students.deleted_at', '=', NULL)
                    ->groupBy('students.id')
                    ->select('students.name','students.student_phone_number', 'batch_has_students.joining_date');
        
        return Datatables::of($batches)
                ->addColumn('price', function ($batches){
                    return 0;
                })
                ->make(true);
    }

    public function getStudentRefundforTeacherPayment(Request $request)
    {
        $get_current_date_month_year = Carbon::createFromFormat('d/m/Y', $request->ref_date);
        $get_current_date_month_year->day = 01;
        $get_current_date_month_year = $get_current_date_month_year->toDateString();

        $data = DB::table('refunds')
                    ->leftJoin('invoice_details', 'invoice_details.id', '=', 'refunds.invoice_details_id')
                    ->leftJoin('invoice_masters', 'invoice_masters.id', '=', 'invoice_details.invoice_masters_id')
                    ->leftJoin('students', 'students.id', '=', 'invoice_masters.students_id')
                    ->leftJoin('batch', 'batch.id', '=', 'invoice_details.batch_id')
                    ->leftJoin('teacher_details', 'teacher_details.users_id', '=', 'batch.teacher_details_users_id')
                    ->where('teacher_details.users_id', '=', $request->teacher_user_id)
                    ->where('refunds.refund_from', '=', $get_current_date_month_year)
                    ->select('students.name as student_name','batch.name as batch_name','refunds.*', 'invoice_details.payment_to as refunded_month','teacher_details.teacher_percentage');
        
        return Datatables::of($data)
        ->addColumn('price_per_student', function ($data){
                return ($data->teacher_percentage * $data->amount) / 100;
        })
        ->addColumn('validate', function ($data){
                return "<button id='".$data->id."' class='btn btn-xs btn-primary refunded_amount'><i class='glyphicon glyphicon-edit'></i> Validate</button>";
        })
        ->make(true);
        


        
    }
// http://localhost:8000/get_student_refund_for_teacher_payment?teacher_user_id=?ref_date=17/01/2017
// $request->ref_date
}