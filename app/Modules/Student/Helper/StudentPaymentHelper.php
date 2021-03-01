<?php
namespace App\Modules\Student\Helper\StudentPaymentHelper;

class StudentPaymentHelper{
	

	public function getAllStudent(){
		// somefunction();
		// StudentPaymentHelper::somefunction
	}

	public function somefunction(){
		$students = Student::with('school', 'batch');
		if((Auth::user())->hasRole('teacher')){
			$students->where();	
		}
		$student->get();
	}


}