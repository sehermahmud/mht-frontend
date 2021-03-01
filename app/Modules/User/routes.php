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
Route::group(['middleware' => ['web']], function () {

    //Route::get('/', 'App\Modules\User\Controllers\IndexController@index');


    //Login Module
    Route::get('login', 'App\Modules\User\Controllers\IndexController@index');
    Route::post('user_login', 'App\Modules\User\Controllers\IndexController@loginUser');

    Route::get('logout', 'App\Modules\User\Controllers\IndexController@logoutUser');

    //User Module
    Route::get('allusers', 'App\Modules\User\Controllers\IndexController@allUsers');
    Route::get('getusers', 'App\Modules\User\Controllers\IndexController@getUsers');
    Route::get('create_users', 'App\Modules\User\Controllers\IndexController@createUsers');
    Route::post('create_users_process', 'App\Modules\User\Controllers\IndexController@createUsersProcess');
    
    Route::get('users/{id}/edit', 'App\Modules\User\Controllers\IndexController@editUsers');
    Route::post('edit_users_process', 'App\Modules\User\Controllers\IndexController@editUsersProcess');


    //User Settings
    Route::get('roles', 'App\Modules\User\Controllers\UserSettingsController@allRoles');
    Route::get('getroles', 'App\Modules\User\Controllers\UserSettingsController@getRoles');
    Route::post('role_add_process', 'App\Modules\User\Controllers\UserSettingsController@addRolesProcess');   
    Route::get('roles/{id}/edit', 'App\Modules\User\Controllers\UserSettingsController@editRoles');
    Route::post('role_edit_process', 'App\Modules\User\Controllers\UserSettingsController@editRolesProcess');
    

    Route::get('permissions', 'App\Modules\User\Controllers\UserSettingsController@allPermissions');
    Route::get('getpermissions', 'App\Modules\User\Controllers\UserSettingsController@getPermissions');
    Route::post('permission_add_process', 'App\Modules\User\Controllers\UserSettingsController@addPermissionsProcess');
    Route::get('permission/{id}/edit', 'App\Modules\User\Controllers\UserSettingsController@editPermission');
    Route::post('permission_edit_process', 'App\Modules\User\Controllers\UserSettingsController@editPermissionProcess');
    
   
    /****************
    * Delete a User *
    *****************/     
    Route::post('users/{user}/delete', 'App\Modules\User\Controllers\IndexController@deleteUser');

    /**********************
    * Correct the Invoice *
    ***********************/

  Route::get('invoice_correction', 'App\Modules\User\Controllers\UserSettingsController@invoice_correction');

    
});

