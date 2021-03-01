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
use App\Modules\Student\Models\InvoiceMaster;
use App\Modules\Student\Models\InvoiceDetail;
use App\Modules\Student\Models\Refund;
use App\Modules\Student\Models\OtherPaymentMaster;
use App\Modules\Student\Models\OtherPaymentDetail;
use App\Modules\Student\Models\OtherPaymentType;

use App\Modules\Student\Helper\StudentHelper;

use Illuminate\Http\Request;
use JWTAuth;
use Datatables;
use Storage;
use File;
use Entrust;
use DB;
use Log;
use Carbon\Carbon;

class StudentPaymentController extends Controller {


	/***********************
    * Payment of a Student *
    ************************/
    public function batchPaymentStudent() {

        $getStudent = Student::all();

        $refDate = Carbon::now();
        $refDate = $refDate->toDateString();
		$refDate = Carbon::createFromFormat('Y-m-d', $refDate)->format('d/m/Y');

        return view('Student::student_payment/batch_payment_of_a_student',compact('getStudent','refDate'));
    }

    public function getAllStudentForPayment(Request $request) {
        
        $search_term = $request->input('term');
        
        $getStudent = Student::where('name', "LIKE", "%{$search_term}%")
                    ->get(['id', 'name as text']);
        return response()->json($getStudent);
    }

    public function getStudentInfoForPayment(Request $request) {
        $getStudent = Student::with('school')->find($request->student_id);
        return response()->json($getStudent);
    }

    public function getBatchInfoForPayment(Request $request) {

        error_log("Student ID");
        error_log($request->input('student_id'));
        error_log("Student ID END");
        $students = Student::with('school', 'batch','subject')->where('id', $request->input('student_id'))->first();
        // return response()->json($students);
        return response()->json($students->batch);
    }

    public function studentPaymentProcess(Request $request) {
        // return $request['due_or_discount_0'];
        // return $request->all();
        if ( $request->total > 0 ) {
            
            $invoice_master = InvoiceMaster::create($request->all());
            // $invoice_master->invoiceDetail()->createMany($request->all());
            
            for ( $i=0; $i < count($request->batch_id); $i++) {
                $last_payment_date = 0;
                $due_or_discount_ = "due_or_discount_";
                if($request->month[$i] != 0) {

                    for ($month=1; $month <= $request->month[$i]; $month++) {
                        
                        $due_or_discount_ = $due_or_discount_ . $i;
                        
                        if ( $request[$due_or_discount_][0]   == 'due') {
                            
                            // return $request[$due_or_discount_];
                            $invoice_detail = new InvoiceDetail();
                            $invoice_detail->invoice_masters_id = $invoice_master->id;
                            $invoice_detail->batch_id = $request->batch_id[$i];
                            $invoice_detail->subjects_id = $request->subjects_id[$i];
                            $calculated_price = $request->batch_unit_price[$i] - $request[$due_or_discount_][1];
                            if ($calculated_price > 0) {
                                $invoice_detail->price = $calculated_price;
                            }
                            else {
                                $invoice_detail->price = 0;
                            }
                            $invoice_detail->status = 1;
                            $invoice_detail->due_amount = $request[$due_or_discount_][1];
                            $last_paid_date_from = Carbon::createFromFormat('Y-m-d', $request->last_paid_date[$i]);
                            $last_paid_date_from = $last_paid_date_from->addMonths($month);  
                            $invoice_detail->payment_from = $last_paid_date_from->toDateString();
                            
                            $last_paid_date_to = Carbon::createFromFormat('Y-m-d', $request->last_paid_date[$i]);
                            $last_paid_date_to = $last_paid_date_to->addMonths($month);
                            $invoice_detail->payment_to = $last_paid_date_to->toDateString();
                            $last_payment_date = $invoice_detail->payment_to;
                            $invoice_detail->refund = false;
                            $invoice_detail->save();
                        
                        } elseif ($request[$due_or_discount_][0] == 'discount') {
                            
                            // return $request[$due_or_discount_];
                            $invoice_detail = new InvoiceDetail();
                            $invoice_detail->invoice_masters_id = $invoice_master->id;
                            $invoice_detail->batch_id = $request->batch_id[$i];
                            $invoice_detail->subjects_id = $request->subjects_id[$i];
                            $calculated_price = $request->batch_unit_price[$i] - $request[$due_or_discount_][1];
                            if ($calculated_price > 0) {
                                $invoice_detail->price = $calculated_price;
                            }
                            else {
                                $invoice_detail->price = 0;
                            }
                            $invoice_detail->status = 2;
                            $invoice_detail->discount_amount = $request[$due_or_discount_][1];
                            $last_paid_date_from = Carbon::createFromFormat('Y-m-d', $request->last_paid_date[$i]);
                            $last_paid_date_from = $last_paid_date_from->addMonths($month);  
                            $invoice_detail->payment_from = $last_paid_date_from->toDateString();
                            
                            $last_paid_date_to = Carbon::createFromFormat('Y-m-d', $request->last_paid_date[$i]);
                            $last_paid_date_to = $last_paid_date_to->addMonths($month);
                            $invoice_detail->payment_to = $last_paid_date_to->toDateString();
                            $last_payment_date = $invoice_detail->payment_to;
                            $invoice_detail->refund = false;
                            $invoice_detail->save();
                        
                        } else {
                            
                            // return $request[$due_or_discount_];
                            $invoice_detail = new InvoiceDetail();
                            $invoice_detail->invoice_masters_id = $invoice_master->id;
                            $invoice_detail->batch_id = $request->batch_id[$i];
                            $invoice_detail->subjects_id = $request->subjects_id[$i];
                            $invoice_detail->price = $request->batch_unit_price[$i];
                            $invoice_detail->status = 0;
                            $last_paid_date_from = Carbon::createFromFormat('Y-m-d', $request->last_paid_date[$i]);
                            $last_paid_date_from = $last_paid_date_from->addMonths($month);  
                            $invoice_detail->payment_from = $last_paid_date_from->toDateString();
                            
                            $last_paid_date_to = Carbon::createFromFormat('Y-m-d', $request->last_paid_date[$i]);
                            $last_paid_date_to = $last_paid_date_to->addMonths($month);
                            $invoice_detail->payment_to = $last_paid_date_to->toDateString();
                            $last_payment_date = $invoice_detail->payment_to;
                            $invoice_detail->refund = false;
                            $invoice_detail->save();
                        
                        }
                    }
                	$batch_has_student = BatchHasStudent::where('batch_id',$request->batch_id[$i])
                										->where('students_id', $request->students_id)
                										->update(['last_paid_date' => $last_payment_date]);
                }
            }
        }

		return "Success";
        
    }

    public function getInvoiceIdforPrint()  {
        $refDate = Carbon::now();

        $data = InvoiceMaster::whereYear('payment_date', '=', $refDate->year)
                                ->whereMonth('payment_date', '=', $refDate->month)
                                ->get();
                                // ->sortByDesc("serial_number");


        
        if (count($data) == 0) {
            return 1;
        }
        else {
            error_log($data[count($data)-1]->serial_number);
            return $data[count($data)-1]->serial_number;
        }
    }
    
    public function get_payment_invoice_id()  {
        
        $refDate = Carbon::now();
        $data = InvoiceMaster::whereYear('payment_date', '=', $refDate->year)
                                ->whereMonth('payment_date', '=', $refDate->month)
                                ->get();

        if (count($data) == 0) {
            $formated_serial_number = $refDate->year. "" . sprintf('%02d', $refDate->month)."".sprintf('%02d', $refDate->day). "" .sprintf('%04d', 1);
            return $formated_serial_number;
        }
        else {
            $get_full_serial_no = $data[count($data)-1]->serial_number;
            $get_last_four_no = substr($get_full_serial_no, -4);
            $set_last_four_no = $get_last_four_no  + 1;
            $formated_serial_number = $refDate->year."". sprintf('%02d', $refDate->month)."".sprintf('%02d', $refDate->day)."".sprintf('%04d', $set_last_four_no);
            return $formated_serial_number;
            // return $data[count($data)-1]->serial_number + 1;
        }
    }


    public function getOtherInvoiceIdforPrint()  {
        $refDate = Carbon::now();

        $data = OtherPaymentMaster::whereYear('payment_date', '=', $refDate->year)
                                ->whereMonth('payment_date', '=', $refDate->month)
                                ->get();
                                // ->sortByDesc("serial_number");


        
        if (count($data) == 0) {
            return 1;
        }
        else {
            error_log($data[count($data)-1]->serial_number);
            return $data[count($data)-1]->serial_number;
        }
    }
    
    public function get_other_payment_invoice_id(Request $request)  {
        
        $refDate = Carbon::now();
        $data = OtherPaymentMaster::whereYear('payment_date', '=', $refDate->year)
                                ->whereMonth('payment_date', '=', $refDate->month)
                                ->get();

        if (count($data) == 0) {
            $formated_serial_number = $request->payment_type . "" . $refDate->year. "" . sprintf('%02d', $refDate->month)."".sprintf('%02d', $refDate->day). "" .sprintf('%04d', 1);
            return $formated_serial_number;
        }
        else {
            $get_full_serial_no = $data[count($data)-1]->serial_number;
            $get_last_four_no = substr($get_full_serial_no, -4);
            $set_last_four_no = $get_last_four_no  + 1;
            $formated_serial_number = $request->payment_type . "" . $refDate->year."". sprintf('%02d', $refDate->month)."".sprintf('%02d', $refDate->day)."".sprintf('%04d', $set_last_four_no);
            return $formated_serial_number;
            // return $data[count($data)-1]->serial_number + 1;
        }
    }



    public function invoiceDetailPage($id)
    {
        $student_details = Student::find($id);
        return view('Student::student_payment/all_invoice_details')->with('studentDetails', $student_details);
    }

    public function getAllInvoiceDetailsForAStudent(Request $request) {
        
        $student_id = $request->student_id;
        $invoice_details = InvoiceDetail::with('invoiceMaster')
                                        ->whereHas('invoiceMaster', function($query) use ($student_id){
                                            $query->where('students_id', $student_id);
                                        })
                                        ->where('refund',false)
                                        ->with(['batch' => function($query){
                                            $query->withTrashed();
                                        }])
                                        ->orderBy('payment_to', 'DESC')
                                        ->get()
                                        ->unique('batch_id');
        
        return Datatables::of($invoice_details)
                        ->addColumn('Link', function ($invoice_details) use ($student_id) {
                                        if((Entrust::can('user.update') && Entrust::can('user.delete')) || true) {
                                        return '<a href="' . url('/refund') . '/' . $invoice_details->id . '/'. $student_id .'/' . '"' . 'class="btn btn-xs btn-info"><i class="glyphicon glyphicon-edit"></i> Refund</a>';
                                        }
                                        else {
                                            return 'N/A';
                                        }
                                    })
                        ->make(true);
    }

    public function refundPayment($invoice_detail_id, $student_id) {
        
        $invoice_details = InvoiceDetail::find($invoice_detail_id);
        
        $current_date = new Carbon('first day of this month');
        $current_date = $current_date->toDateString();
        
        if ($current_date == $invoice_details->payment_to) {
            
            $invoice_details->refund = true;
            $invoice_details->save();
            
            $refund = new Refund();
            $refund->refund_from = $invoice_details->payment_to;
            $refund->amount = $invoice_details->price;
            $refund->invoice_details_id = $invoice_details->id;
            $refund->save();

            $last_payment_date = new Carbon('first day of last month');
            $batch_has_student = BatchHasStudent::where('batch_id', $invoice_details->batch_id)
                                                    ->where('students_id', $student_id)
                                                    ->update(['last_paid_date' => $last_payment_date->toDateString()]);
            return back();
        }
        elseif ( $current_date > $invoice_details->payment_to ) {
            $invoice_details->refund = true;
            $invoice_details->save();

            $refund = new Refund();
            $refund->refund_from = $current_date;
            $refund->amount = $invoice_details->price;
            $refund->invoice_details_id = $invoice_details->id;
            $refund->save();

            $batch_has_student = BatchHasStudent::where('batch_id',$invoice_details->batch_id)
                                                    ->where('students_id', $student_id)
                                                    ->update(['last_paid_date' => $current_date]);
            return back();
        }
        else {
            $invoice_details->refund = true;
            $invoice_details->save();
            
            $refDate = Carbon::createFromFormat('Y-m-d', $invoice_details->payment_to);
            $last_payment_date = $refDate->subMonths(1);
            $last_payment_date = $last_payment_date->toDateString();
            $batch_has_student = BatchHasStudent::where('batch_id',$invoice_details->batch_id)
                                                    ->where('students_id', $student_id)
                                                    ->update(['last_paid_date' => $last_payment_date]);
            return back();
        }
    }

    public function due_payment_student(Request $request) {
        $data = DB::table('students')
            ->leftJoin('invoice_masters', 'students.id', '=', 'invoice_masters.students_id')
            ->leftJoin('invoice_details', 'invoice_masters.id', '=', 'invoice_details.invoice_masters_id')
            ->leftJoin('batch', 'invoice_details.batch_id', '=', 'batch.id')
            ->whereNull('students.deleted_at')
            // ->whereNull('batch.deleted_at')
            ->where('students.id', '=', $request->student_id)
            // ->where('students.phone_home', '=', $request->input('student_phonenumber'))
            ->where('due_amount','!=', 0)
            ->select('students.id as student_id', 'invoice_masters.id as invoice_masters_id','invoice_details.id as invoice_details_id','discount_amount','due_amount','invoice_details.price as invoice_details_price','payment_date','batch.name as batch_name','invoice_details.payment_to');
        return Datatables::of($data)
                        ->addColumn('Link', function ($data) {
                                        if((Entrust::can('user.update') && Entrust::can('user.delete')) || true) {
                                        return '<button id="'. $data->invoice_details_id .'" class="btn btn-xs btn-danger temp_due"><i class="glyphicon glyphicon-edit"></i> Clear</button>';
                                        }
                                        else {
                                            return 'N/A';
                                        }
                                    })
                        ->make(true);
    }

    public function clear_due_payment(Request $request)
    {
        $invoice_details = InvoiceDetail::find($request->invoice_detail_id);
        
        $invoice_master = InvoiceMaster::find($invoice_details->invoice_masters_id);
        $invoice_master->total = $invoice_master->total + $invoice_details->due_amount;
        $invoice_master->save();

        $invoice_details->price = $invoice_details->price + $invoice_details->due_amount;
        $invoice_details->due_amount = 0;
        $invoice_details->save();

        $invoice_details = InvoiceDetail::find($request->invoice_detail_id);
        return $invoice_details;
    }

    public function lastPaidUpdatePage($id)
    {
        $student_details = Student::find($id);
        return view('Student::student_payment/last_paid_update_page')->with('studentDetails', $student_details);
    }

    public function get_all_batches_for_last_paid_update(Request $request)
    {
        $student_id = $request->student_id;
        $student_details = Student::withTrashed('batch')->find($student_id);
        $student_details = $student_details->batch;
        
        return Datatables::of($student_details)
                        ->addColumn('LastPaidDate', function ($student_details) use ($student_id) {
                                        if((Entrust::can('user.update') && Entrust::can('user.delete')) || true) {
                                        
                                            $refDate = Carbon::createFromFormat('Y-m-d', $student_details->pivot->last_paid_date)->format('d/m/Y');
                                            $class_name = 'form-control ref_date update_'.$student_details->id;
                                            return "<div class='input-group date'><input type='text' class='".$class_name."' name='last_payment_date' value='" . $refDate . "' autocomplete='off'></div>";
                                        }
                                        else {
                                            return 'N/A';
                                        }
                                    })
                        ->addColumn('Link', function ($student_details) use ($student_id) {
                                        if((Entrust::can('user.update') && Entrust::can('user.delete')) || true) {
                                        return "<button id='".$student_details->id."' class='btn btn-xs btn-info update_button'><i class='glyphicon glyphicon-edit'></i> Update</button>";
                                        }
                                        else {
                                            return 'N/A';
                                        }
                                    })
                        ->make(true);
    }
    
    public function last_payment_date_update(Request $request)
    {   
        $last_paid_date = Carbon::createFromFormat('d/m/Y', $request->last_paid_date);
        $last_paid_date->day = 01;
        $last_paid_date = $last_paid_date->toDateString();
        
        $batch_has_student = BatchHasStudent::where('batch_id',$request->batch_id)
                                                    ->where('students_id', $request->student_id)
                                                    ->update(['last_paid_date' => $last_paid_date]);
    }

    public function get_student_payment_history(Request $request)                   
    {
        $get_student_payment_history = Student::with('batch')->find($request->student_id);
        $get_student_payment_history = $get_student_payment_history->batch;
        return Datatables::of($get_student_payment_history)->make(true);
    }

    public function get_student_transaction_history(Request $request)
    {
        $student_id = $request->student_id;

        $get_student_transaction_history = InvoiceDetail::with('invoiceMaster')
                                        ->whereHas('invoiceMaster', function($query) use ($student_id){
                                            $query->where('students_id', $student_id);
                                        })
                                        ->with(['batch' => function($query){
                                            $query->withTrashed();
                                        }])
                                        ->where('refund', 0)
                                        ->orderBy('payment_to', 'DESC')
                                        ->get();
        return Datatables::of($get_student_transaction_history)->make(true);
    }

    public function get_student_refund_history(Request $request)
    {
        $student_id = $request->student_id;

        $get_student_refund_history = Refund::with('invoiceDetail.invoiceMaster')
                                            ->whereHas('invoiceDetail.invoiceMaster', function($query) use ($student_id){
                                                $query->where('students_id', $student_id);
                                            })
                                            ->with(['invoiceDetail.batch' => function($query){
                                                $query->withTrashed();
                                            }])
                                            ->get();
        return Datatables::of($get_student_refund_history)->make(true);
    }

    public function otherPayment() {

        $getStudent = Student::all();

        $refDate = Carbon::now();
        $refDate = $refDate->toDateString();
        $refDate = Carbon::createFromFormat('Y-m-d', $refDate)->format('d/m/Y');

        return view('Student::student_payment/other_payment',compact('refDate'));
    }

    public function admission_payment_info(Request $request)
    {
        $admission_status = Student::find($request->student_id);
        
        return response()->json($admission_status);
    }

    public function student_admission_payment_process(Request $request)
    {
        $payment_type = OtherPaymentType::where('description', 'admission')->first();
        
        $other_payment_master = new OtherPaymentMaster();
        $other_payment_master->payment_date = $request->payment_date;
        $other_payment_master->price = $request->admission_fee;
        $other_payment_master->serial_number = $request->serial_number;
        $other_payment_master->students_id = $request->students_id;
        $other_payment_master->other_payment_type_id = $payment_type->id;
        $other_payment_master->note = $request->description;
        $other_payment_master->save();

        $student = Student::find($request->students_id);
        $student->admitted_status = 1;
        $student->save();
        
        return $request->all();
    }

    public function student_other_payment_process(Request $request)
    {
        $payment_type = OtherPaymentType::where('description', 'other')->first();

        $other_payment_master = new OtherPaymentMaster();
        $other_payment_master->payment_date = $request->payment_date;
        $other_payment_master->price = $request->other_fee;
        $other_payment_master->serial_number = $request->serial_number;
        $other_payment_master->students_id = $request->students_id;
        $other_payment_master->other_payment_type_id = $payment_type->id;
        $other_payment_master->note = $request->other_dsecription;
        $other_payment_master->save();
        
        return $request->all();
    }

}