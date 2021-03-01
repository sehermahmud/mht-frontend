<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\Batch;

use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller {

    public function index() {        
        if (Auth::check()) {
			/* Calculating Total number of Students for current month */
	        $students = Student::with('batch','school')->has('batch')->get();
	        $students = Student::all();
	        $total_students = count($students);

	        /* Calculating Total number of Students for current month */
	        $active_students = Student::with('batch','school')->has('batch')->get();
	        $total_active_students = count($active_students);
	        
	        
	        /* Calculating Total Expected Amount current month */
	        $batches = Batch::with('student')->has('student')->get();
	        $total_expected_amount = 0;
	        for ($i=0; $i < count($batches); $i++) { 
	            $total_expected_amount = $total_expected_amount + $batches[$i]->price * count($batches[$i]->student);
	        }


	        /* For which Month the Payment Calculations are done */
	        $now = new Carbon('first day of this month');
	        $now = $now->toDateString();
	        
	        /* Calculating Total Paid Amount for a Particular Month */
	        $total_paid_amount = 0;
	        for ($i=0; $i < count($batches); $i++) { // operation for a single batch
	            $no_of_paid_students = 0;
	            $student = $batches[$i]->student;
	            for ($c=0; $c < count($student); $c++) {  // operation for a single student
	                if ($student[$c]->pivot->last_paid_date >= $now)  {
	                    $no_of_paid_students = $no_of_paid_students + 1;
	                }
	            }
	            $total_paid_amount = $total_paid_amount + ($no_of_paid_students * $batches[$i]->price);
	            
	        }
	        
	        /* Calculating Total Unpaid Amount for a Particular Month */
	        $total_unpaid_amount = 0;
	        for ($i=0; $i < count($batches); $i++) {
	            $no_of_unpaid_students = 0;
	            $student = $batches[$i]->student;
	            for ($c=0; $c < count($student); $c++) { 
	                if ($student[$c]->pivot->last_paid_date < $now)  {
	                    $no_of_unpaid_students = $no_of_unpaid_students + 1;
	                }
	            }
	            $total_unpaid_amount = $total_unpaid_amount + ($no_of_unpaid_students * $batches[$i]->price);
	            
	        }


			return view('Dashboard::dashboard')
	        ->with('total_students', $total_students)
	        ->with('total_active_students', $total_active_students)
	        ->with('total_expected_amount', $total_expected_amount)
	        ->with('total_paid_amount', $total_paid_amount)
	        ->with('total_unpaid_amount', $total_unpaid_amount);

			// return view('Dashboard::dashboard');
	        // return view('Dashboard::dashboard_second');
	        // return view('Dashboard::default_dashboard');
	        // return redirect('allusers'); 
        }

       return redirect('login'); 
    }
  
}
