<?php


Route::group(['middleware' => ['web','auth']], function () {

    Route::get('payment_reporting', 'App\Modules\Reporting\Controllers\ReportingWebController@paymentReporting');

    Route::get('get_daily_reporting', 'App\Modules\Reporting\Controllers\ReportingWebController@getDailyReporting');
    
    Route::get('get_due_reporting', 'App\Modules\Reporting\Controllers\ReportingWebController@getDueReporting');

    Route::get('refund_reporting', 'App\Modules\Reporting\Controllers\ReportingWebController@refundReporting');

    Route::get('payment_date_range', 'App\Modules\Reporting\Controllers\ReportingWebController@paymentDateRange');

    Route::get('monthly_statement', 'App\Modules\Reporting\Controllers\ReportingWebController@monthlyStatement');

    Route::get('monthly_due_statement', 'App\Modules\Reporting\Controllers\ReportingWebController@monthlyDueStatement');

    

    Route::get('other_payment_reporting', 'App\Modules\Reporting\Controllers\ReportingWebController@otherPaymentReporting');

    Route::get('get_other_daily_reporting', 'App\Modules\Reporting\Controllers\ReportingWebController@getOtherDailyReporting');

    Route::get('get_other_payment_date_range', 'App\Modules\Reporting\Controllers\ReportingWebController@getOtherPaymentDateRange');

    Route::get('get_other_monthly_statement', 'App\Modules\Reporting\Controllers\ReportingWebController@getMonthlyOtherStatement');

    Route::get('get_other_due_reporting', 'App\Modules\Reporting\Controllers\ReportingWebController@getOtherDueReporting');


});