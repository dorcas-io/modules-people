<?php

Route::group(['namespace' => 'Dorcas\ModulesPeople\Http\Controllers', 'middleware' => ['web'], 'prefix' => 'mpe'], function() {
    Route::get('people-main', 'ModulesPeopleController@main')->name('people-main');
    Route::get('people-departments', 'ModulesPeopleController@departments')->name('people-departments');
    Route::delete('people-departments/{id}', 'ModulesPeopleController@departments_delete');
    Route::post('people-departments', 'ModulesPeopleController@departments_post')->name('people-departments-post');
    //Route::put('people-departments/{id}', 'ModulesPeopleController@departments_update')->name('people-departments-update');
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


Route::group(['middleware' => ['auth'], 'namespace' => 'Ajax', 'prefix' => 'xhr'], function () {
    Route::get('/businesses', 'Business\Businesses@search');
    Route::delete('/business/departments/{id}/employees', 'Business\Department@removeEmployees');

    Route::post('/business/employees', 'Business\Employee@create');
    Route::delete('/business/employees/{id}', 'Business\Employee@delete');
    Route::put('/business/employees/{id}', 'Business\Employee@update');
    Route::delete('/business/employees/{id}/teams', 'Business\Employee@removeTeams');

    Route::post('/business/teams', 'Business\Team@create');
    Route::delete('/business/teams/{id}', 'Business\Team@delete');
    Route::put('/business/teams/{id}', 'Business\Team@update');
    Route::delete('/business/teams/{id}/employees', 'Business\Team@removeEmployees');

});

?>