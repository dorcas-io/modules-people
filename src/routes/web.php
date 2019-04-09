<?php

Route::group(['namespace' => 'Dorcas\ModulesPeople\Http\Controllers', 'middleware' => ['web']], function() {
    Route::get('sales', 'ModulesPeopleController@index')->name('sales');
});


Route::group(['middleware' => ['auth'], 'namespace' => 'Businesses', 'prefix' => 'apps/people'], function () {
    Route::get('/', 'Business@index')->name('business');

    Route::get('/departments', 'Departments\Departments@index')->name('business.departments');
    Route::get('/departments/new', 'Departments\Departments@index')->name('business.departments.new');
    Route::get('/departments/{id}', 'Departments\Department@index')->name('business.departments.single');
    Route::post('/departments/{id}', 'Departments\Department@post');

    Route::get('/employees', 'Employees\Employees@index')->name('business.employees');
    Route::get('/employees/new', 'Employees\NewEmployee@index')->name('business.employees.new');
    Route::post('/employees/new', 'Employees\NewEmployee@create');
    Route::get('/employees/{id}', 'Employees\Employee@index')->name('business.employees.single');
    Route::post('/employees/{id}', 'Employees\Employee@post');

    Route::get('/teams', 'Teams\Teams@index')->name('business.teams');
    Route::get('/teams/{id}', 'Teams\Team@index')->name('business.teams.single');
    Route::post('/teams/{id}', 'Teams\Team@post');
});

?>