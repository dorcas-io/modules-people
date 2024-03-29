<?php

Route::group(['namespace' => 'Dorcas\ModulesPeople\Http\Controllers', 'middleware' => ['auth','web'], 'prefix' => 'mpe'], function() {
    Route::get('people-main', 'ModulesPeopleController@main')->name('people-main');

    Route::get('people-departments', 'ModulesPeopleController@departments')->name('people-departments');
    Route::delete('people-departments/{id}', 'ModulesPeopleController@departments_delete');
    Route::post('people-departments', 'ModulesPeopleController@departments_post')->name('people-departments-post');
    //Route::put('people-departments/{id}', 'ModulesPeopleController@departments_update')->name('people-departments-update');
    Route::get('/people-departments/{id}', 'ModulesPeopleController@departments_view')->name('people-departments-view');
    Route::post('/people-departments/{id}/employees', 'ModulesPeopleController@departments_employees_add');
    Route::delete('/people-departments/{id}/employees', 'ModulesPeopleController@departments_employees_delete');

    Route::get('/people-employees', 'ModulesPeopleController@employees')->name('people-employees');
    Route::post('/people-employees', 'ModulesPeopleController@employees_import')->name('people-employees-import');
    Route::delete('/people-employees/{id}', 'ModulesPeopleController@employees_delete');
    Route::get('/people-employees-new', 'ModulesPeopleController@employees_new')->name('people-employees-new');
    Route::post('/people-employees-new', 'ModulesPeopleController@employees_create');
    Route::post('/finance-entries', 'ModulesFinanceController@entries_create');
    Route::get('/people-employees/{id}', 'ModulesPeopleController@employees_view')->name('people-employees-view');
    Route::put('/people-employees/{id}', 'ModulesPeopleController@employees_update');
    Route::post('/people-employees/{id}', 'ModulesPeopleController@employees_post');

    Route::get('/people-teams', 'ModulesPeopleController@teams')->name('people-teams');
    Route::post('people-teams', 'ModulesPeopleController@teams_post')->name('people-teams-post');
    Route::delete('people-teams/{id}', 'ModulesPeopleController@teams_delete');
    Route::get('people-teams/{id}', 'ModulesPeopleController@teams_view')->name('people-teams-view');
    Route::post('/people-teams/{id}/employees', 'ModulesPeopleController@teams_employees_add');
    Route::delete('/people-teams/{id}/employees', 'ModulesPeopleController@teams_employees_delete');

    Route::get('people-tasks','ModulesPeopleController@tasks')->name('people-tasks');
    Route::post('task/create','ModulesPeopleController@createTask')->name('create-tasks');
    Route::get('task/{id}','ModulesPeopleController@Task')->name('task-edit');
    Route::post('task/{id}/update','ModulesPeopleController@updateTask')->name('task-update');
    Route::post('task-status/{id}/update','ModulesPeopleController@updateTaskStatus')->name('task-status-update');
    Route::post('task/{id}/assign','ModulesPeopleController@assignTaskToEmployee')->name('task-assign');
    Route::post('task/{id}/remove_employee','ModulesPeopleController@removeTaskToEmployee')->name('task-remove');
    Route::get('task/{id}/task_employee','ModulesPeopleController@allTaskEmployee')->name('task-employee');
    Route::get('people-tasks/{task_id}','ModulesPeopleController@viewTask')->name('task-view');
    Route::delete('people-task/{task_id}','ModulesPeopleController@deleteTask')->name('task-delete');




    Route::get('people-projects','ModulesPeopleController@projects')->name('people-projects');
    Route::post('project/create','ModulesPeopleController@createProject')->name('create-projects');
    Route::get('project/{id}','ModulesPeopleController@Project')->name('project-edit');
    Route::post('project/update','ModulesPeopleController@updateProject')->name('project-update');
    Route::post('project-status/{id}/update','ModulesPeopleController@updateProjectStatus')->name('project-status-update');
    Route::post('project/department-assign/{id}','ModulesPeopleController@assignProjectToDepartment')->name('assign-project');
    Route::post('project/department-unassign/{id}','ModulesPeopleController@unassignProjectToDepartment')->name('unassign-project');
    // Route::post('task/{id}/remove_employee','ModulesPeopleController@removeTaskToEmployee')->name('task-remove');
    // Route::get('task/{id}/task_employee','ModulesPeopleController@allTaskEmployee')->name('task-employee');
    Route::get('people-projects/{project_id}','ModulesPeopleController@viewProject')->name('project-view');
    Route::delete('people-project/{project_id}','ModulesPeopleController@deleteProject')->name('project-delete');
    
});


Route::group(['middleware' => ['auth'], 'namespace' => 'Businesses', 'prefix' => 'xapps/people'], function () {

    Route::get('/', 'Business@index')->name('business');
    Route::get('/departments', 'Departments\Departments@index')->name('business.departments');
    Route::get('/departments/new', 'Departments\Departments@index')->name('business.departments.new');

    

    
    
    Route::post('/teams/{id}', 'Teams\Team@post');
});


Route::group(['middleware' => ['auth'], 'namespace' => 'Ajax', 'prefix' => 'xxhr'], function () {
    Route::get('/businesses', 'Business\Businesses@search');

    //Route::post('/business/employees', 'Business\Employee@create');
    Route::put('/business/employees/{id}', 'Business\Employee@update');
    Route::delete('/business/employees/{id}/teams', 'Business\Employee@removeTeams');

    Route::post('/business/teams', 'Business\Team@create');
    Route::delete('/business/teams/{id}', 'Business\Team@delete');
    Route::put('/business/teams/{id}', 'Business\Team@update');
    Route::delete('/business/teams/{id}/employees', 'Business\Team@removeEmployees');

});

?>