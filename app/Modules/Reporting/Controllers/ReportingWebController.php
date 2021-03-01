<?php

namespace App\Modules\Reporting\Controllers;

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
use App\Modules\Teacher\Models\TeacherDetail;
use App\Modules\Student\Models\BatchDaysHasBatchTime;
use App\Modules\Student\Models\BatchHasDaysAndTime;
use App\Modules\Student\Models\BatchHasStudent;
use App\Modules\Student\Models\InvoiceMaster;
use App\Modules\Student\Models\InvoiceDetail;
use App\Modules\Student\Models\Refund;

use App\Modules\Reporting\Repository\ReportRepository;

use Illuminate\Http\Request;
use JWTAuth;
use Datatables;
use Storage;
use File;
use Entrust;
use Carbon\Carbon;


class ReportingWebController extends Controller {

    public function paymentReporting()
    {
        // return view('Reporting::payment_reporting');
        return view('Reporting::payment_reporting_test2');
        // return view('Reporting::payment_reporting_test');
    }

    public function getDailyReporting(ReportRepository $report)
    {
        $today = Carbon::today();
        $today = $today->toDateString();
        $dailyReporting = $report->getDailyPaymentReportingByDate($today);
        return Datatables::of($dailyReporting)
                        ->addColumn('paid_batches', function ($dailyReporting) {
                           return $dailyReporting->invoiceDetail->map(function($invDetail) {
                              if ($invDetail->refund == 0) {
                                $ready_data = "(" . $invDetail->batch->name . ", ".$invDetail->price. ", ". $invDetail->payment_from . ")";
                                return $ready_data;
                              }
                               return "";
                           })->implode(', ');
                        })
                        ->addColumn('discount_per_batch', function ($dailyReporting) {
                           return $dailyReporting->invoiceDetail->map(function($invDetail) {
                              if ($invDetail->refund == 0) {
                                $ready_data = $invDetail->batch->name . " = " . $invDetail->discount_amount . "/-" ;
                                return $ready_data;
                              }
                               return "";
                           })->implode(', ');
                        })
                        ->addColumn('due_per_batch', function ($dailyReporting) {
                           return $dailyReporting->invoiceDetail->map(function($invDetail) {
                              if ($invDetail->refund == 0) {
                                $ready_data = $invDetail->batch->name . " = " . $invDetail->due_amount . "/-" ;
                                return $ready_data;
                              }
                               return "";
                           })->implode(', ');
                        })->make(true);
    }

    public function refundReporting(Request $request, ReportRepository $report)
    {
        $refundStatementDate = Carbon::createFromFormat('d/m/Y', $request->refund_statement_date);
        $refundStatementDate->day = 01;
        $refundReporting = $report->getRefundReporting($refundStatementDate->toDateString());
        return Datatables::of($refundReporting)
                        ->addColumn('paid_batches', function ($refundReporting) {
                           return $refundReporting->invoiceDetail->map(function($invDetail) {
                              if ($invDetail->refund == 1) {
                                $ready_data = "(" . $invDetail->batch->name . ", ".$invDetail->price. ", ". $invDetail->payment_from . ")";
                                return $ready_data;
                              }
                               return "";
                           })->implode(', ');
                        })
                        ->make(true);
    }

    public function paymentDateRange(Request $request, ReportRepository $report)
    {   
        $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->toDateString();
        $endDate = Carbon::createFromFormat('d/m/Y', $request->end_date)->toDateString();
        $dateRangeReporting = $report->getRangePaymentReportingByDate($startDate, $endDate);
        
        return Datatables::of($dateRangeReporting)
                    ->addColumn('paid_batches', function ($allReporting) {
                       return $allReporting->invoiceDetail->map(function($invDetail) {
                           $ready_data = "(" . $invDetail->batch->name . ", ".$invDetail->price. ", ". $invDetail->payment_from . ")";
                           return $ready_data;
                       })->implode(', ');
                    })
                    ->addColumn('discount_per_batch', function ($dateRangeReporting) {
                           return $dateRangeReporting->invoiceDetail->map(function($invDetail) {
                              if ($invDetail->refund == 0) {
                                $ready_data = $invDetail->batch->name . " = " . $invDetail->discount_amount . "/-" ;
                                return $ready_data;
                              }
                               return "";
                           })->implode(', ');
                        })
                        ->addColumn('due_per_batch', function ($dateRangeReporting) {
                           return $dateRangeReporting->invoiceDetail->map(function($invDetail) {
                              if ($invDetail->refund == 0) {
                                $ready_data = $invDetail->batch->name . " = " . $invDetail->due_amount . "/-" ;
                                return $ready_data;
                              }
                               return "";
                           })->implode(', ');
                        })->make(true);
    }

    public function monthlyStatement(Request $request, ReportRepository $report)
    {
        $statementDate = Carbon::createFromFormat('d/m/Y', $request->statement_date);
        $monthlyStatement = $report->getmonthlyPaymentStatement($statementDate->month, $statementDate->year);
        return Datatables::of($monthlyStatement)->make(true);
    }

    public function getDueReporting(ReportRepository $report)
    {
        $first_day_of_current_month = new Carbon('first day of this month');
        $first_day_of_current_month = $first_day_of_current_month->toDateString();

        $dueReporting = $report->getDueByDate($first_day_of_current_month);
        
        return Datatables::of($dueReporting)
        ->addColumn('TotalDuePrice', function ($dueReporting) {
            $batches = $dueReporting->batch;
            $total_due = 0;
            foreach ($batches as $batch) {

               $last_paid_date = Carbon::parse($batch->pivot->last_paid_date); 
               $now = Carbon::now();
               
               $diff_in_months = $now->diffInMonths($last_paid_date);
               $amount = $diff_in_months * $batch->price;
               $total_due = $total_due + $amount;
            }
            return $total_due;
        })
        ->addColumn('due_batches', function ($allReporting) {
           return $allReporting->batch->map(function($bat) {
               $ready_data = "(" . $bat->name . ", ".$bat->price. ", ". $bat->pivot->last_paid_date . ")";
               return $ready_data;
           })->implode(', ');
        })
        ->make(true);
    }

    public function monthlyDueStatement(Request $request, ReportRepository $report)
    {
        $due_statement_date = Carbon::createFromFormat('d/m/Y', $request->due_statement_date);
        $due_statement_date->day = 01;
        $due_statement_date = $due_statement_date->subMonth();
        $due_statement_date = $due_statement_date->toDateString();
        $monthlyDueStatement = $report->getmonthlyDueStatement($due_statement_date);
        return Datatables::of($monthlyDueStatement)
        ->addColumn('TotalDuePrice', function ($dueReporting) {
            $batches = $dueReporting->batch;
            $total_due = 0;
            foreach ($batches as $batch) {

               $last_paid_date = Carbon::parse($batch->pivot->last_paid_date); 
               $now = Carbon::now();

               $total_due = $total_due + $batch->price;
            }
            return $total_due;
        })
        ->addColumn('due_batches', function ($dueReporting) {
           return $dueReporting->batch->map(function($bat) {
               $ready_data = "(" . $bat->name . ", ".$bat->price. ", ". $bat->pivot->last_paid_date . ")";
               return $ready_data;
           })->implode(', ');
        })
        ->make(true);
    }

    public function otherPaymentReporting()
    {
      return view('Reporting::other_payment_reporting');
    }

    public function getOtherDailyReporting(ReportRepository $report)
    {
        $today = Carbon::today();
        $today = $today->toDateString();
        
        $dailyOtherPaymentReporting = $report->getOtherDailyPaymentReportingByDate($today);
        
        return Datatables::of($dailyOtherPaymentReporting)->make(true);
    }

    public function getOtherPaymentDateRange(Request $request, ReportRepository $report)
    {   
        $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->toDateString();
        $endDate = Carbon::createFromFormat('d/m/Y', $request->end_date)->toDateString();
        
        $dateRangeReporting = $report->getOtherRangePaymentReportingByDate($startDate, $endDate);
        
        return Datatables::of($dateRangeReporting)->make(true);
    }

    public function getMonthlyOtherStatement(Request $request, ReportRepository $report)
    {
        $statementDate = Carbon::createFromFormat('d/m/Y', $request->statement_date);
        
        $monthlyOtherPaymentStatement = $report->getmonthlyOtherPaymentStatement($statementDate->month, $statementDate->year);
        
        return Datatables::of($monthlyOtherPaymentStatement)->make(true);
    }

    public function getOtherDueReporting()
    {
        $not_admitted_students = Student::with('school')->where('admitted_status', 0)->get();
        return Datatables::of($not_admitted_students)->make(true);
    }





}