<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

//main bootstrap file for loading the first Page
Route::get('/', function () {
    return view('welcome');
});

////authentication and authorization
//Route::group(['prefix' => 'auth'], function()
//{
//	Route::post('login','AuthController@login');
//	
//    //Route::resource('authenticate', 'AuthenticateController', ['only' => ['index']]);
//    //Route::post('authenticate', 'AuthenticateController@authenticate');
//});

Route::group(['prefix' => 'auth'], function()
{
    Route::post('/', 'AuthController@authenticate');
    Route::post('change-password', 'AuthController@changePassword');
    Route::get('permissions', 'AuthController@getPermissions');
    Route::get('user', 'AuthController@getAuthenticatedUser');
    Route::get('logout', 'AuthController@logout');
});

Route::group(['prefix' => 'security','namespace'=>'Security'], function()
{
    Route::resource('permission', 'PermissionController');
    
    Route::get("role/getperms/{roleId}","RoleController@getAllPermissionsByRole");
    Route::post("role/addperms","RoleController@savePermissionsOnRole");
    Route::resource("role","RoleController");
    
    Route::get("users/getroles/{userId}","UsersController@getAllRolesByUser");
    Route::post("users/addroles","UsersController@saveRolesOnUser");
    Route::post("users/resetpass","UsersController@resetPassword");
    Route::resource("users","UsersController");
    
    
});

Route::group(['prefix' => 'organization','namespace'=>'Organization'], function(){
    
    Route::get('organization/list/{id?}','OrganizationController@getOrganizations');
    Route::resource('organization','OrganizationController');
    
    Route::resource('post','PostController');
    Route::get('employee/list/{orgid}','EmployeeController@listEmployeeByOrg');
    Route::get('employeeuser','EmployeeController@getEmployeeInfoByUserId');
    Route::resource('employee','EmployeeController');
    
});

Route::post('check-email','AuthController@emailExists');
Route::get('authenticate/logout', 'AuthController@logout');

Route::get('machine/list/org/{orgId}','MachineController@machineByOrg');
Route::get('machine/list','MachineController@listMachOptions');
Route::resource('machine','MachineController');
Route::resource('org-machine','OrgMachineController');
Route::resource('empmachine','EmpMachineController');
Route::resource('leave/leave-request','LeaveController');
Route::get('leave/leave-approval','LeaveController@leaveApproval');
