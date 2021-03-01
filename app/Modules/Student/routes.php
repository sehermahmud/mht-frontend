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

    /******************************************************
    * Show the information of all Students in a data table *
    *******************************************************/
    Route::get('students_all_students', 'App\Modules\Student\Controllers\StudentsWebController@allStudents');
    Route::get('students_get_students', 'App\Modules\Student\Controllers\StudentsWebController@getStudents');

    /**********************************************************
    * Show the information of active Students in a data table *
    ***********************************************************/
    Route::get('students_active_students', 'App\Modules\Student\Controllers\StudentsWebController@activeStudents');
    Route::get('students_get_active_students', 'App\Modules\Student\Controllers\StudentsWebController@getActiveStudents');

    

    /**********************************************
    * Show the information of a Particular Student *
    ***********************************************/
    Route::get('student/{student}/show/', 'App\Modules\Student\Controllers\StudentsWebController@get_one_Student');



    /*****************************
    * Student Detail information *
    ******************************/
    Route::get('students_student/{student_id}/detail/', 'App\Modules\Student\Controllers\StudentsWebController@student_detail');

    /**********************
    * Create a new Student *
    ***********************/   
    Route::get('students_create_student', 'App\Modules\Student\Controllers\StudentsWebController@addStudent');
    Route::post('create_student_process', 'App\Modules\Student\Controllers\StudentsWebController@addStudentProcess');


    /***************************
    * Edit and Update a Student *
    ****************************/    
    Route::get('students_student/{student}/edit/', 'App\Modules\Student\Controllers\StudentsWebController@editStudent');
    Route::patch('/student_update_process/{student}/', 'App\Modules\Student\Controllers\StudentsWebController@studentUpdateProcess');


    /******************
    * Delete a Student *
    *******************/     
    Route::post('student/{student}/delete', 'App\Modules\Student\Controllers\StudentsWebController@deleteStudent');

    /***********************
    * Payment of a Student *
    ************************/
    Route::get('students_payment_batch_student', 'App\Modules\Student\Controllers\StudentPaymentController@batchPaymentStudent');
    Route::get('get_all_student_for_payment', 'App\Modules\Student\Controllers\StudentPaymentController@getAllStudentForPayment');
    Route::get('get_student_info_for_payment', 'App\Modules\Student\Controllers\StudentPaymentController@getStudentInfoForPayment');
    Route::get('get_batch_info_for_payment', 'App\Modules\Student\Controllers\StudentPaymentController@getBatchInfoForPayment');     
    Route::post('student_payment', 'App\Modules\Student\Controllers\StudentPaymentController@studentPaymentProcess');
    
    Route::get('get_invoice_id_for_print', 'App\Modules\Student\Controllers\StudentPaymentController@getInvoiceIdforPrint');
    Route::get('get_payment_invoice_id', 'App\Modules\Student\Controllers\StudentPaymentController@get_payment_invoice_id');

    Route::get('get_other_invoice_id_for_print', 'App\Modules\Student\Controllers\StudentPaymentController@getOtherInvoiceIdforPrint');
    Route::get('get_other_payment_invoice_id', 'App\Modules\Student\Controllers\StudentPaymentController@get_other_payment_invoice_id');
    
    Route::get('due_payment_student', 'App\Modules\Student\Controllers\StudentPaymentController@due_payment_student');
    Route::post('clear_due_payment', 'App\Modules\Student\Controllers\StudentPaymentController@clear_due_payment');
    Route::get('get_student_payment_history', 'App\Modules\Student\Controllers\StudentPaymentController@get_student_payment_history');
    Route::get('get_student_transaction_history', 'App\Modules\Student\Controllers\StudentPaymentController@get_student_transaction_history');
    Route::get('get_student_refund_history', 'App\Modules\Student\Controllers\StudentPaymentController@get_student_refund_history');

    Route::get('students_payment_other', 'App\Modules\Student\Controllers\StudentPaymentController@otherPayment');
    Route::get('admission_payment_info', 'App\Modules\Student\Controllers\StudentPaymentController@admission_payment_info');
    Route::post('student_admission_payment_process', 'App\Modules\Student\Controllers\StudentPaymentController@student_admission_payment_process');
    Route::post('student_other_payment_process', 'App\Modules\Student\Controllers\StudentPaymentController@student_other_payment_process');

    /**************************************
    * Invoice History Update of a Student *
    ***************************************/
    Route::get('student/{student}/invoice_detail_page/', 'App\Modules\Student\Controllers\StudentPaymentController@invoiceDetailPage');
    Route::get('student/get_all_invoice_details_for_a_student_payment/', 'App\Modules\Student\Controllers\StudentPaymentController@getAllInvoiceDetailsForAStudent');
    Route::get('refund/{invoice_details}/{students}/', 'App\Modules\Student\Controllers\StudentPaymentController@refundPayment');
    
    Route::get('student/{student}/last_paid_update_page/', 'App\Modules\Student\Controllers\StudentPaymentController@lastPaidUpdatePage');
    Route::get('student/get_all_batches_for_last_paid_update/', 'App\Modules\Student\Controllers\StudentPaymentController@get_all_batches_for_last_paid_update');
    Route::post('student/last_payment_date_update', 'App\Modules\Student\Controllers\StudentPaymentController@last_payment_date_update');


    /******************************************************
    * Show the information of all Batches in a data table *
    *******************************************************/
    Route::get('all_batches', 'App\Modules\Student\Controllers\BatchWebController@allBatches');
    Route::get('get_batches/{teacherDetailID}/', 'App\Modules\Student\Controllers\BatchWebController@getBatches');


    /*********************
    * Create a new Batch *
    **********************/
    Route::get('create_batch', 'App\Modules\Student\Controllers\BatchWebController@addBatch');
    Route::post('create_batch_process', 'App\Modules\Student\Controllers\BatchWebController@addBatchProcess');
    Route::post('create_new_batch_process', 'App\Modules\Student\Controllers\BatchWebController@addNewBatchProcess');

    /**************************
    * Select2 helper Function *
    ***************************/       
    Route::get('getallbatch', 'App\Modules\Student\Controllers\BatchWebController@getAllBatch');
    Route::get('get_student_batch_for_edit', 'App\Modules\Student\Controllers\StudentsWebController@StudentBatchForEdit');
    Route::get('get_batch_joining_date_for_edit', 'App\Modules\Student\Controllers\StudentsWebController@get_batch_joining_date_for_edit');


    /**************************
    * Edit and Update a Batch *
    ***************************/
    Route::get('batch/{batch}/edit/', 'App\Modules\Student\Controllers\BatchWebController@editBatch');
    Route::post('batch_update_process', 'App\Modules\Student\Controllers\BatchWebController@batchUpdateProcess');

    // Route::get('batch/{batch}/edit/', 'App\Modules\Student\Controllers\StudentsWebController@editNewBatch');
    // Route::post('batch_new_update_process/', 'App\Modules\Student\Controllers\BatchWebController@batchNewUpdate');


    /****************
    * Delete a Batch*
    *****************/     
    Route::post('batch/{batch}/delete', 'App\Modules\Student\Controllers\BatchWebController@deleteBatch');

    /*************************************
    * All batches and number of students *
    **************************************/
    Route::get('get_all_batches_and_students', 'App\Modules\Student\Controllers\StudentsWebController@get_all_batches_and_students');

    /*********************
    * Batch Wise Students*
    **********************/
    Route::get('students_batch_wise_student_page', 'App\Modules\Student\Controllers\BatchWebController@batchWiseStudentPage');
    Route::get('students_get_all_batches_for_a_subject', 'App\Modules\Student\Controllers\BatchWebController@get_all_batches_for_a_subject');
    
    Route::get('students_all_students_per_batch_page/{batch}/{total_student}', 'App\Modules\Student\Controllers\BatchWebController@all_students_per_batch_page');
    Route::get('students_get_all_inactive_students_per_batch', 'App\Modules\Student\Controllers\BatchWebController@students_get_all_inactive_students_per_batch');
    Route::get('students_get_all_active_students_per_batch', 'App\Modules\Student\Controllers\BatchWebController@students_get_all_active_students_per_batch');


    /******************************************************
    * Show the information of all Subjects in a data table*
    *******************************************************/
    Route::get('all_subjects', 'App\Modules\Student\Controllers\SubjectWebController@allSubjects');
    Route::get('get_subjects', 'App\Modules\Student\Controllers\SubjectWebController@getSubjects');
    /**********************
    * Create a new Subject*
    ***********************/
    Route::get('create_subject', 'App\Modules\Student\Controllers\SubjectWebController@addSubject');
    Route::post('create_subject_process', 'App\Modules\Student\Controllers\SubjectWebController@addSubjectProcess');
    /****************************
    * Edit and Update a Subject *
    *****************************/
    Route::get('subject/{subject}/edit/', 'App\Modules\Student\Controllers\SubjectWebController@editSubject');
    Route::patch('subject_update_process/{subject}/', 'App\Modules\Student\Controllers\SubjectWebController@subjectUpdateProcess');
    /*******************
    * Delete a Subject *
    ********************/     
    Route::post('subject/{subject}/delete', 'App\Modules\Student\Controllers\SubjectWebController@deleteSubject');

    
    
    /*****************************************************
    * Show the information of all Grades in a data table *
    ******************************************************/
    Route::get('all_grades', 'App\Modules\Student\Controllers\GradWebController@allGrades');
    Route::get('get_grades', 'App\Modules\Student\Controllers\GradWebController@getGrades');
    /******************************
    * Create a new Grade or Class *
    *******************************/
    Route::get('create_grade', 'App\Modules\Student\Controllers\GradWebController@addGrade');
    Route::post('create_grade_process', 'App\Modules\Student\Controllers\GradWebController@addGradeProcess');
    /**************************
    * Edit and Update a Grade *
    ***************************/
    Route::get('grade/{grade}/edit/', 'App\Modules\Student\Controllers\GradWebController@editGrade');
    Route::patch('grade_update_process/{grade}/', 'App\Modules\Student\Controllers\GradWebController@gradeUpdate');

    /*****************
    * Delete a Grade *
    ******************/     
    Route::post('grade/{grade}/delete', 'App\Modules\Student\Controllers\GradWebController@deleteGrade');





    /*****************************************************
    * Show the information of all Schools in a data table*
    ******************************************************/
    Route::get('all_schools', 'App\Modules\Student\Controllers\SchoolWebController@allSchools');
    Route::get('get_schools', 'App\Modules\Student\Controllers\SchoolWebController@getSchools');
    /**********************
    * Create a new School *
    ***********************/
    Route::get('create_school', 'App\Modules\Student\Controllers\SchoolWebController@addSchool');
    Route::post('create_school_process', 'App\Modules\Student\Controllers\SchoolWebController@addSchoolProcess');
    /**************************
    * Edit and Update a School *
    ***************************/
    Route::get('school/{school}/edit/', 'App\Modules\Student\Controllers\SchoolWebController@editSchool');
    Route::patch('school_update_process/{school}/', 'App\Modules\Student\Controllers\SchoolWebController@schoolUpdateProcess');
    /*****************
    * Delete a School *
    ******************/     
    Route::post('school/{school}/delete', 'App\Modules\Student\Controllers\SchoolWebController@deleteSchool');

    





    /******************************************
    * BatchType related Functions. Incomplete *
    *******************************************/
    Route::get('create_batch_type', 'App\Modules\Student\Controllers\BatchWebController@addBatchType');
    Route::post('create_batch_type_process', 'App\Modules\Student\Controllers\BatchWebController@addBatchTypeProcess');



    Route::get('edit_student_phn_num', 'App\Modules\Student\Controllers\SchoolWebController@edit_std_phn_num');

    // Test export option for datatable
    Route::get('students', 'App\Modules\Student\Controllers\StudentsWebController@testDatatable');

});