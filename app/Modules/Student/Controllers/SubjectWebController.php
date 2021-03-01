<?php

namespace App\Modules\Student\Controllers;

use App\Http\Controllers\Controller;

use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\Subject;



use Illuminate\Http\Request;
use JWTAuth;
use Datatables;
use Entrust;
use DB;
use Log;
class SubjectWebController extends Controller {

	/*****************************************************
    * Show the information of all Subjects in a data table *
    ******************************************************/
    public function allSubjects() {
        return view('Student::subjects/all_subjects');
    }

    public function getSubjects() {
    $subjects = Subject::all();
    return Datatables::of($subjects)
                    ->addColumn('Link', function ($subjects) {
                        if((Entrust::can('user.update') && Entrust::can('user.delete')) || true) {
                        return '<a href="' . url('/subject') . '/' . $subjects->id . '/edit/' . '"' . 'class="btn bg-green margin"><i class="glyphicon glyphicon-edit"></i> Edit</a>' .'&nbsp &nbsp &nbsp'.
                                '<a class="btn bg-red margin" id="'. $subjects->id .'" data-toggle="modal" data-target="#confirm_delete">
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
    * Create a new Subject *
    **********************/
    public function addSubject() {
        return view('Student::subjects/create_subject');
    }

    public function addSubjectProcess(Request $request) {
        Subject::create($request->all());
        return redirect("all_subjects");
    }

    /**************************
    * Edit and Update a Subject *
    ***************************/
    public function editSubject(Subject $subject) {
    	return view('Student::subjects/edit_subject')
        ->with('getSubject', $subject);
    }

    public function subjectUpdateProcess(Request $request, Subject $subject) {
        $subject->update( $request->all()); 
        return redirect('all_subjects');
    }

    /*****************
    * Delete a Subject *
    ******************/ 
    public function deleteSubject(Request $request, Subject $subject) {
        $subject->delete();
        return back();
    }
}