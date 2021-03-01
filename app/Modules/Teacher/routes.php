<?php

/*
  |--------------------------------------------------------------------------
  | User Routes
  |--------------------------------------------------------------------------
  |
  | All the routes for User module has been written here
  |
  |
 */
Route::group(['middleware' => ['web','auth']], function () {

    /*******************************************************
    * Show the information of all Teachers in a data table *
    ********************************************************/
    Route::get('all_teachers', 'App\Modules\Teacher\Controllers\TeachersWebController@allTeachers');
    Route::get('get_teachers', 'App\Modules\Teacher\Controllers\TeachersWebController@getTeachers');
    

    /**********************************************
    * Show the information of a Particular Teacher *
    ***********************************************/
    Route::get('teacher/{Teacher}/show/', 'App\Modules\Teacher\Controllers\TeachersWebController@get_one_Teacher');

    
    /***********************
    * Create a new Teacher *
    ************************/
    Route::get('create_teacher', 'App\Modules\Teacher\Controllers\TeachersWebController@addTeacher');
    Route::post('create_teacher_process', 'App\Modules\Teacher\Controllers\TeachersWebController@addTeacherProcess');


    /***************************
    * Edit and Update a Teacher*
    ****************************/    
    Route::get('teacher/{teacher}/edit/', 'App\Modules\Teacher\Controllers\TeachersWebController@editTeacher');
    Route::patch('/teacher_update_process/{teacherdetail}/', 'App\Modules\Teacher\Controllers\TeachersWebController@teacherUpdate');


    /******************
    * Delete a Teacher *
    *******************/     
    Route::post('teacher/{teacher}/delete', 'App\Modules\Teacher\Controllers\TeachersWebController@deleteTeacher');


    /***********************
    * Teacher Payment Info *
    ************************/
    Route::get('teacher_payment_all_batch', 'App\Modules\Teacher\Controllers\TeachersWebController@teacherPaymentAllBatch');
    Route::get('get_all_teacher_for_payment', 'App\Modules\Teacher\Controllers\TeachersWebController@getAllTeacherForPayment');

    Route::get('get_all_batch_for_teacher_payment', 'App\Modules\Teacher\Controllers\TeachersWebController@getAllBatchForTeacherPayment');
    
    Route::get('/batch/{batch}/{date}/{batchName}/get_paid_and_non_paid_std_teacher_payment', 'App\Modules\Teacher\Controllers\TeachersWebController@allStudentForTeacherPayment');

    Route::get('/get_paid_students_for_a_batch', 'App\Modules\Teacher\Controllers\TeachersWebController@getPaidStudentsForABatch');

    Route::get('/get_non_paid_students_for_a_batch', 'App\Modules\Teacher\Controllers\TeachersWebController@getNonPaidStudentsForABatch');

    Route::get('/get_student_refund_for_teacher_payment', 'App\Modules\Teacher\Controllers\TeachersWebController@getStudentRefundforTeacherPayment');

    Route::get('app/images/student_Images/{imgname}', function($imgname){
        $file_path = storage_path(). '/app/images/student_Images/' . $imgname;
        $file = File::get($file_path);
        $type = File::mimeType($file_path);
        $response = response()->make($file, 200);
        $response->header("Content-Type", $type);
        return $response;
    });
});