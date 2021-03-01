<?php
namespace App\Modules\Reporting\Repository;

use App\Modules\User\Models\Role;

use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\Batch;
use App\Modules\Student\Models\BatchHasStudent;
use App\Modules\Student\Models\InvoiceMaster;
use App\Modules\Student\Models\InvoiceDetail;
use App\Modules\Student\Models\Refund;
use App\Modules\Student\Models\OtherPaymentMaster;
use App\Modules\Student\Models\OtherPaymentDetail;
use App\Modules\Student\Models\OtherPaymentType;
use DB;

class ReportRepository {

	public function getDailyPaymentReportingByDate($date)	{
		// $payments = InvoiceMaster::with('student')
		// 			->with('invoiceDetail.batch')
		// 			->whereHas('invoiceDetail', function($query){
		// 				$query->where('refund', 0);
		// 			})
		// 			->where('payment_date', $date)
		// 			->get();
		$payments = InvoiceMaster::with(['student'=> function($query){
						$query->withTrashed();
					}])
					->with(['invoiceDetail.batch'=> function($query){
						$query->withTrashed();
					}])
					->whereHas('invoiceDetail', function($query){
						$query->where('refund', 0);
					})
					->where('payment_date', $date)
					->get();
		
		return $payments;
	}

	public function getOtherDailyPaymentReportingByDate($date)	{
		$payments = OtherPaymentMaster::with('student', 'other_payment_type')
					->where('payment_date', $date)
					->get();
		
		return $payments;
	}

	
	public function getRefundReporting($payment_for) {
		$refund = InvoiceMaster::with(['student'=> function($query){
						$query->withTrashed();
					}])
					->with(['invoiceDetail.batch'=> function($query){
						$query->withTrashed();
					}])
					->with(['invoiceDetail' => function($query) use ($payment_for) {
						$query->where('invoice_details.refund', '=', 1)->where('invoice_details.payment_from', '=', $payment_for);
					}])
					->get();
		
		$refund = $refund->map(function($invoiceMaster) {
            if (count($invoiceMaster->invoiceDetail) > 0 ) {
            	return $invoiceMaster;
            }
        })
        ->reject(function ($invoiceMaster) {
            return empty($invoiceMaster->invoiceDetail);
        });

		return $refund;
	}

		public function getmonthlyPaymentStatement($statement_month, $statement_year)	{
		// $monthlyStatement = InvoiceDetail::with('invoiceMaster.student','batch')
		// 								->whereYear('payment_from', '=', $statement_year)
		// 								->whereMonth('payment_from', '=', $statement_month)
	 //            						->where('refund', 0)
	 //            						->get();
        $monthlyStatement = InvoiceDetail::with(['invoiceMaster.student'=> function($query){
											$query->withTrashed();
										}])
										->with(['batch'=> function($query){
											$query->withTrashed();
										}])
										->whereYear('payment_from', '=', $statement_year)
										->whereMonth('payment_from', '=', $statement_month)
	            						->where('refund', 0)
	            						->get();
        // $monthlyStatement = $monthlyStatement->map(function($invoicedetail) {
        //     if ($invoicedetail->invoiceMaster->student != null ) {
        //         return $invoicedetail;
        //     }
        // })
        // ->reject(function ($invoicedetail) {
        //     return empty($invoicedetail->invoiceMaster->student);
        // });

        // $monthlyStatement = $monthlyStatement->map(function($invoicedetail) {
        //     if ($invoicedetail->batch != null ) {
        //         return $invoicedetail;
        //     }
        // })
        // ->reject(function ($invoicedetail) {
        //     return empty($invoicedetail->batch);
        // });
        return $monthlyStatement;
	}

	public function getRangePaymentReportingByDate($startDate, $endDate)	{
		$payments = InvoiceMaster::with(['student'=> function($query){
								$query->withTrashed();
							}])
							->with(['invoiceDetail.batch'=> function($query){
								$query->withTrashed();
							}])
							->whereHas('invoiceDetail', function($query){
								$query->where('refund', 0);
							})
							->whereBetween('payment_date', [$startDate, $endDate])
							->get();
		return $payments;
	}

	public function getOtherRangePaymentReportingByDate($startDate, $endDate)	{
		$payments = OtherPaymentMaster::with('student', 'other_payment_type')
					->whereBetween('payment_date', [$startDate, $endDate])
					->get();
		return $payments;
	}


	public function getDueByDate($first_day_of_current_month)	{
		
		$payments = Student::with(['batch' => function ($query) use( $first_day_of_current_month )  {
					    		$query->where('last_paid_date', '<', $first_day_of_current_month);
							}])->get();

		$payments = $payments->map(function($student){
			            			if (count($student->batch) > 0 ) {
						                return $student;
						            }
						        })
						        ->reject(function ($student) {
						            return empty($student);
						        });
		
		return $payments;
		
	}

	public function getmonthlyOtherPaymentStatement($statement_month, $statement_year)	{
		$monthlyOtherPaymentStatement = OtherPaymentMaster::with('student', 'other_payment_type')
															->whereYear('payment_date', '=', $statement_year)
															->whereMonth('payment_date', '=', $statement_month)
						            						->get();
        return $monthlyOtherPaymentStatement;
	}

	public function getmonthlyDueStatement($due_statement_date)	{
		$payments = Student::with(['batch' => function ($query) use( $due_statement_date )  {
    		$query->where('last_paid_date', '<=', $due_statement_date);
		}])->get();
		$payments = $payments->map(function($student){
			            			if (count($student->batch) > 0 ) {
						                return $student;
						            }
						        })
						        ->reject(function ($student) {
						            return empty($student);
						        });
		
		return $payments;

	}

}