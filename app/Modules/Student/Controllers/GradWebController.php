<?php

namespace App\Modules\Student\Controllers;

use App\Http\Controllers\Controller;

use App\Modules\Student\Models\Student;
use App\Modules\Student\Models\Grade;

use Illuminate\Http\Request;
use JWTAuth;
use Datatables;
use Entrust;
use DB;
use Log;

class GradWebController extends Controller {

	/*****************************************************
    * Show the information of all Grades in a data table *
    ******************************************************/
    public function allGrades() {
        return view('Student::grades/all_grades');
    }

    public function getGrades() {
    $grades = Grade::all();
    return Datatables::of($grades)
                    ->addColumn('Link', function ($grades) {
                        if((Entrust::can('user.update') && Entrust::can('user.delete')) || true) {
                        return '<a href="' . url('/grade') . '/' . $grades->id . '/edit/' . '"' . 'class="btn bg-green margin"><i class="glyphicon glyphicon-edit"></i> Edit</a>' .'&nbsp &nbsp &nbsp'.
                                '<a class="btn bg-red margin" id="'. $grades->id .'" data-toggle="modal" data-target="#confirm_delete">
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
    * Create a new Grade *
    **********************/
    public function addGrade() {
        return view('Student::grades/create_grade');
    }

    public function addGradeProcess(Request $request) {
        Grade::create($request->all());
        return redirect("/all_grades");
    }

    /**************************
    * Edit and Update a Grade *
    ***************************/
    public function editGrade(Grade $grade) {
        return view('Student::grades/edit_grade')
        ->with('getGrade', $grade);
    }

    public function gradeUpdate(Request $request, Grade $grade) {
        $grade->update( $request->all()); 
        return redirect('/all_grades');
    }

    /*****************
    * Delete a Grade *
    ******************/ 
    public function deleteGrade(Request $request, Grade $grade) {
        $grade->delete();
        return back();
    }
}