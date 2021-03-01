<?php

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| All the routes for Dashboard module has been written here
|
|
*/
Route::group(['middleware' => ['web']], function () {   
    Route::get('dashboard', 'App\Modules\Dashboard\Controllers\DashboardController@index');
    
});

