<?php

namespace Dorcas\ModulesPeople\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dorcas\ModulesPeople\Models\ModulesPeople;
use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use App\Http\Controllers\HomeController;
use Hostville\Dorcas\Sdk;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\DeletingFailedException;
use App\Exceptions\RecordNotFoundException;

class ModulesPeopleController extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->data = [
            'page' => ['title' => config('modules-people.title')],
            'header' => ['title' => config('modules-people.title')],
            'selectedMenu' => 'modules-people',
            'submenuConfig' => 'navigation-menu.modules-people.sub-menu',
            'submenuAction' => ''
        ];
    }

    public function main()
    {
    	$this->data['availableModules'] = HomeController::SETUP_UI_COMPONENTS;
    	//return view('modules-people::index', $this->data);
    }

    public function departments(Request $request, Sdk $sdk)
    {
        $this->data['page']['title'] .= ' &rsaquo; Departments';
        $this->data['header']['title'] = 'Departments';
        $this->data['selectedSubMenu'] = 'people-departments';
        $this->data['submenuAction'] = '<a href="#" v-on:click.prevent="createDepartment" class="btn btn-primary btn-block">Add Department</a>';

        $this->setViewUiResponse($request);
        $this->data['departments'] = $this->getDepartments($sdk);
        //dd($this->data['departments']);
        return view('modules-people::departments.departments', $this->data);
    }



    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function departments_post(Request $request, Sdk $sdk)
    {
        /*$query = $sdk->createDepartmentResource()
                    ->addBodyParam('name', $request->input('name'))
                    ->addBodyParam('description', $request->input('description'))
                                                    ->send('POST');
        # send request
        if (!$query->isSuccessful()) {
            $message = $query->getErrors()[0]['title'] ?? 'Failed while trying to create the department.';
            throw new \RuntimeException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.departments.'.$company->id);
        return response()->json($query->getData());*/


        $this->validate($request,[
            'name' => 'required|string|max:80',
            'description' => 'nullable|string'
        ]);
        # validate the request
        try {
            $departmentId = $request->has('department_id') ? $request->input('department_id') : null;
            $resource = $sdk->createDepartmentResource($departmentId);
            $payload = $request->only(['name', 'description']);
            foreach ($payload as $key => $value) {
                $resource->addBodyParam($key, $value);
            }
            $response = $resource->send(empty($departmentId) ? 'post' : 'put');
            # send the request
            if (!$response->isSuccessful()) {
                # it failed
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while '. (empty($departmentId) ? 'adding' : 'updating') .' the department. '.$message);
            }
            $company = $this->getCompany();
            Cache::forget('business.departments.'.$company->id);
            $response = (tabler_ui_html_response(['Successfully '. (empty($departmentId) ? 'added' : 'updated the') .' department.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);


    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function departments_delete(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createDepartmentResource($id);
        $response = $model->send('delete');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            $message = $response->getErrors()[0]['title'] ?? 'Failed while deleting the department.';
            throw new RecordNotFoundException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.departments.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /*public function departments_update(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createDepartmentResource($id);
        $response = $model->addBodyParam('name', $request->input('name', ''))
                            ->addBodyParam('description', $request->input('description', ''))
                            ->send('put');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            $message = $response->getErrors()[0]['title'] ?? 'Failed while updating the department.';
            throw new RecordNotFoundException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.departments.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }*/



    public function departments_view(Request $request, Sdk $sdk, string $id)
    {

        $this->setViewUiResponse($request);
        $response = $sdk->createDepartmentResource($id)
                        ->addQueryArgument('include', 'employees:limit(10000|0)')
                        ->send('get');
        if (!$response->isSuccessful()) {
            abort(404, 'Could not find the customer at this URL.');
        }

        # try to get the department information
        $this->data['department'] = $department = $response->getData(true);
        # get the information
        $employees = $this->getEmployees($sdk);
        $this->data['noEmployeesMessage'] = !empty($employees) && $employees->count() > 0 ?
            'All your employees are already in this department.' : 'You can start by adding one or more employees to your records.';
        # a message to display when the employees list is empty after filtering
        if (!empty($employees) && $employees->count() > 0) {
            $employees = $employees->filter(function ($employee) use ($department) {
                if (empty($employee->department['data'])) {
                    return true;
                }
                return $employee->department['data']['id'] !== $department->id;
            });
        }

        $this->data['employees'] = $employees;

        $this->data['page']['title'] .= ' &rsaquo; Departments &rsaquo; '.$department->name;
        $this->data['header']['title'] = 'Departments &rsaquo; '.$department->name;
        $this->data['selectedSubMenu'] = 'people-departments';
        //$this->data['submenuAction'] = '<a href="#add-employees" class="btn btn-primary btn-block">Add Employee</a>';

        return view('modules-people::departments.department', $this->data);


        /*$this->setViewUiResponse($request);
        $query = $sdk->createDepartmentResource($id)
                        ->addQueryArgument('include', 'employees:limit(10000|0)')
                        ->send('get');
        # try to get the department information
        $this->data['department'] = $department = $query->getData(true);
        # get the information
        $employees = $this->getEmployees($sdk);
        $this->data['noEmployeesMessage'] = !empty($employees) && $employees->count() > 0 ?
            'All your employees are already in this department.' : 'You can start by adding one or more employees to your records.';
        # a message to display when the employees list is empty after filtering
        if (!empty($employees) && $employees->count() > 0) {
            $employees = $employees->filter(function ($employee) use ($department) {
                if (empty($employee->department['data'])) {
                    return true;
                }
                return $employee->department['data']['id'] !== $department->id;
            });
        }
        $this->data['employees'] = $employees;
        $this->data['page']['title'] .= ' - '.$department->name;
        $this->data['breadCrumbs']['crumbs'][2]['text'] = $department->name;

        return view('business.departments.department', $this->data);*/

    }


    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function departments_employees_add(Request $request, Sdk $sdk, string $id)
    {
        /*$this->validate($request, [
            'employees' => 'required_with:add_employees|array',
            'employees.*' => 'string'
        ]);
        # validate the request
        $company = $request->user()->company(true, true);
        try {
            if ($request->has('add_employees')) {

                $query = $sdk->createDepartmentResource($id)->addBodyParam('employees', $request->employees)
                    ->send('post', ['employees']);
                # make the request
                if (!$query->isSuccessful()) {
                    $message = $query->getErrors()[0]['title'] ?? 'Failed while adding the employee record.';
                    throw new \RuntimeException($message);
                }
                Cache::forget('business.employees.'.$company->id);
                $response = (material_ui_html_response(['Successfully added the employees to the department.']))->setType(UiResponse::TYPE_SUCCESS);
            }
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);*/


        $model = $sdk->createDepartmentResource($id)->addBodyParam('employees', $request->employees);
        $response = $model->send('post', ['employees']);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            $message = $response->errors[0]['title'] ?? 'Failed while adding the employee record.';
            throw new \RuntimeException($message);
        }
        //Cache::forget('business.employees.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);


    }


    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function departments_employees_delete(Request $request, Sdk $sdk, string $id)
    {
        $this->validate($request, [
            'employees' => 'required|array',
            'employees.*' => 'string'
        ]);
        # validate the request
        $model = $sdk->createDepartmentResource($id);
        $response = $model->addBodyParam('employees', $request->input('employees', []))
                            ->send('delete', ['employees']);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            $message = $response->getErrors()[0]['title'] ?? 'Failed while removing the employee(s) from the department.';
            throw new DeletingFailedException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.departments.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }


    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function employees(Request $request, Sdk $sdk)
    {
        $this->data['page']['title'] .= ' &rsaquo; Employees';
        $this->data['header']['title'] = 'Employees';
        $this->data['selectedSubMenu'] = 'people-employees';
        $this->data['submenuAction'] = '<a href="'.route("people-employees-new").'"class="btn btn-primary btn-block">Add Employee</a>';

        $this->setViewUiResponse($request);
        $this->data['employees'] = $this->getEmployees($sdk);
        $this->data['departments'] = $this->getDepartments($sdk);
        $this->data['locations'] = $this->getLocations($sdk);
        return view('modules-people::employees.employees', $this->data);
    }



    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function employees_delete(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createEmployeeResource($id);
        $response = $model->send('delete');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            $message = $response->getErrors()[0]['title'] ?? 'Failed while deleting the employee.';
            throw new RecordNotFoundException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.employees.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function employees_new(Request $request, Sdk $sdk)
    {

        $this->data['page']['title'] .= ' &rsaquo; New Employee';
        $this->data['header']['title'] = 'New Employee';
        $this->data['selectedSubMenu'] = 'people-employees-new';
        //$this->data['submenuAction'] = '<a href="'.route("people-employees-new").'" class="btn btn-primary btn-block">Add Customer</a>';

        $this->setViewUiResponse($request);
        $this->data['departments'] = $this->getDepartments($sdk);
        $this->data['locations'] = $this->getLocations($sdk);
        return view('modules-people::employees.new', $this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function employees_create(Request $request, Sdk $sdk)
    {
        $query = $sdk->createEmployeeResource()
                        ->addBodyParam('firstname', $request->input('firstname'))
                        ->addBodyParam('lastname', $request->input('lastname'))
                        ->addBodyParam('phone', $request->input('phone'))
                        ->addBodyParam('email', $request->input('email'))
                        ->addBodyParam('staff_code', $request->input('staff_code'))
                        ->addBodyParam('job_title', $request->input('job_title'))
                        ->addBodyParam('salary_amount', $request->input('salary_amount', 0));
        if ($request->has('salary_period') && !empty($request->salary_period)) {
            $query = $query->addBodyParam('salary_period', $request->salary_period);
        }
        if ($request->has('gender') && !empty($request->gender)) {
            $query = $query->addBodyParam('gender', $request->gender);
        }
        if ($request->has('department') && !empty($request->department)) {
            $query = $query->addBodyParam('department', $request->department);
        }
        if ($request->has('location') && !empty($request->location)) {
            $query = $query->addBodyParam('location', $request->location);
        }
        $query = $query->send('POST');
        # send request
        if (!$query->isSuccessful()) {
            $message = $query->getErrors()[0]['title'] ?? 'Failed while trying to add the employee.';
            throw new \RuntimeException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.employees.'.$company->id);
        return response()->json($query->getData());
    }


    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function employees_update(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createEmployeeResource($id);
        $response = $model->addBodyParam('firstname', $request->input('firstname'))
                            ->addBodyParam('lastname', $request->input('lastname'))
                            ->addBodyParam('gender', $request->input('gender'))
                            ->addBodyParam('phone', $request->input('phone'))
                            ->addBodyParam('email', $request->input('email'))
                            ->addBodyParam('staff_code', $request->input('staff_code'))
                            ->addBodyParam('job_title', $request->input('job_title'))
                            ->addBodyParam('salary_amount', $request->input('salary_amount', 0))
                            ->send('put');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            $message = $response->getErrors()[0]['title'] ?? 'Failed while updating the employee information.';
            throw new RecordNotFoundException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.employees.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function employees_teams_delete(Request $request, Sdk $sdk, string $id)
    {
        $this->validate($request, [
            'teams' => 'required|array',
            'teams.*' => 'string'
        ]);
        # validate the request
        $model = $sdk->createEmployeeResource($id);
        $response = $model->addBodyParam('teams', $request->input('teams', []))
                            ->send('delete', ['teams']);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            $message = $response->getErrors()[0]['title'] ?? 'Failed while removing the teams(s) for the employee.';
            throw new DeletingFailedException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.employees.'.$company->id);
        Cache::forget('business.teams.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }

    /**
     * @param Sdk    $sdk
     * @param string $id
     *
     * @return \stdClass|null
     */
    protected function getEmployee(Sdk $sdk, string $id)
    {
        $query = $sdk->createEmployeeResource($id)->relationships([
                                                        'teams' => ['paginate' => ['limit' => 1000]],
                                                        'department' => ['paginate' => ['limit' => 1000]],
                                                        'location' => ['paginate' => ['limit' => 1000]]
                                                    ])
                                                    ->send('get');
        if (!$query->isSuccessful()) {
            $message = $query->getErrors()[0]['title'] ?? 'Failed while reading the employee information.';
            abort(500, $message);
        }
        return $query->getData(true);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function employees_view(Request $request, Sdk $sdk, string $id)
    {

        $this->setViewUiResponse($request);
        $this->data['departments'] = $this->getDepartments($sdk);
        $this->data['teams'] = $this->getTeams($sdk);
        $this->data['employee'] = $employee = $this->getEmployee($sdk, $id);
        //dd($employee);
        if (!empty($employee->user) && !empty($employee->user['data'])) {
            $configurations = (array) $employee->user['data']['extra_configurations'];
            $currentUiSetup = $configurations['ui_setup'] ?? [];
            $this->data['setupUiFields'] = collect(HomeController::SETUP_UI_COMPONENTS)->map(function ($field) use ($currentUiSetup) {
                if (!empty($field['is_readonly'])) {
                    return $field;
                }
                if (empty($currentUiSetup)) {
                    return $field;
                }
                $field['enabled'] = in_array($field['id'], $currentUiSetup);
                return $field;
            });
            # add the UI components
        }

        $this->data['page']['title'] .= ' &rsaquo; Employee &rsaquo; '.$employee->firstname.' '.$employee->lastname;
        $this->data['header']['title'] = 'Employee &rsaquo; '.$employee->firstname.' '.$employee->lastname;
        $this->data['selectedSubMenu'] = 'people-employees';
        //$this->data['submenuAction'] = '<a href="'.route("people-employees-new").'" class="btn btn-primary btn-block">Add Customer</a>';
        return view('modules-people::employees.employee', $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(Request $request, Sdk $sdk, string $id)
    {
        $this->validate($request, [
            'email' => 'required_if:action,create_user|email',
            'password' => 'required_if:action,create_user|string',
            'firstname' => 'required_if:action,create_user|string|max:30',
            'lastname' => 'required_if:action,create_user|string|max:30',
            'phone' => 'required_if:action,create_user|string|max:30',
            'selected_apps' => 'required_if:action,update_module_access|array',
            'selected_apps.*' => 'string'
        ]);
        # validate the request
        $action =  $request->input('action');
        $employee = $this->getEmployee($sdk, $id);
        try {
            if ($action === 'create_user') {
                # create a user account
                $service = $sdk->createCompanyService()->addBodyParam('employee_id', $id);
                # send the request
                $data = $request->except(['_token', 'action']);
                foreach ($data as $key => $value) {
                    $service->addBodyParam($key, $value);
                }
                $query = $service->send('post', ['users']);
                if (!$query->isSuccessful()) {
                    throw new \RuntimeException($query->getErrors()[0]['title'] ?? 'Failed while creating user account. Please try again.');
                }
                $message = ['Successfully created the user account for this employee.'];
                
            } else {
                # update address information
                $configurations = (array) $employee->user['data']['extra_configurations'];
    
                $readonlyExtend = collect(HomeController::SETUP_UI_COMPONENTS)->filter(function ($field) {
                    return !empty($field['is_readonly']) && !empty($field['enabled']);
                })->pluck('id');
                # get the enabled-readonly values
    
                $readonlyRemovals = collect(HomeController::SETUP_UI_COMPONENTS)->filter(function ($field) {
                    return !empty($field['is_readonly']) && empty($field['enabled']);
                })->pluck('id');
                # get the disabled-readonly values
    
                $selectedApps = collect($request->input('selected_apps', []))->merge($readonlyExtend);
                # set the selected apps
    
                $selectedApps = $selectedApps->filter(function ($id) use ($readonlyRemovals) {
                    return !$readonlyRemovals->contains($id);
                });
                # remove them
    
                $configurations['ui_setup'] = $selectedApps->unique()->all();
                
                $user = (object) $employee->user['data'];
                
                $query = $sdk->createUserResource($user->id)->addBodyParam('extra_configurations', $configurations, true)
                                                            ->send('PUT');
                # send the request
                if (!$query->isSuccessful()) {
                    throw new \RuntimeException('Failed while updating The employee\'s module access. Please try again.');
                }
                $message = ['Successfully updated module access for this '.$employee->firstname];
                
            }
            $response = (material_ui_html_response($message))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function teams(Request $request, Sdk $sdk)
    {

        $this->data['page']['title'] .= ' &rsaquo; Teams';
        $this->data['header']['title'] = 'Teams';
        $this->data['selectedSubMenu'] = 'people-teams';
        $this->data['submenuAction'] = '<a href="#" v-on:click.prevent="createTeam" class="btn btn-primary btn-block">Add Team</a>';

        $this->setViewUiResponse($request);
        $this->data['teams'] = $this->getTeams($sdk);
        return view('modules-people::teams.teams', $this->data);
    }

    public function teams_post(Request $request, Sdk $sdk)
    {

        $this->validate($request,[
            'name' => 'required|string|max:80',
            'description' => 'nullable|string'
        ]);
        # validate the request
        try {
            $teamId = $request->has('team_id') ? $request->input('team_id') : null;
            $resource = $sdk->createTeamResource($teamId);
            $payload = $request->only(['name', 'description']);
            foreach ($payload as $key => $value) {
                $resource->addBodyParam($key, $value);
            }
            $response = $resource->send(empty($teamId) ? 'post' : 'put');
            # send the request
            if (!$response->isSuccessful()) {
                # it failed
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while '. (empty($teamId) ? 'adding' : 'updating') .' the team. '.$message);
            }
            $company = $this->getCompany();
            Cache::forget('business.teams.'.$company->id);
            $response = (tabler_ui_html_response(['Successfully '. (empty($teamId) ? 'added' : 'updated the') .' team.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }


    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function teams_delete(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createTeamResource($id);
        $response = $model->send('delete');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            $message = $response->getErrors()[0]['title'] ?? 'Failed while deleting the team.';
            throw new RecordNotFoundException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.teams.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function teams_view(Request $request, Sdk $sdk, string $id)
    {

        $this->setViewUiResponse($request);
        $query = $sdk->createTeamResource($id)
                        ->addQueryArgument('include', 'employees:limit(10000|0)')
                        ->send('get');
        # try to get the team information
        $this->data['team'] = $team = $query->getData(true);
        # get the information
        $employees = $this->getEmployees($sdk);
        $this->data['noEmployeesMessage'] = !empty($employees) && $employees->count() > 0 ?
            'All your employees are already part of this team.' : 'You can start by adding one or more employees to your records.';
        # a message to display when the employees list is empty after filtering
        if (!empty($employees) && $employees->count() > 0) {
            $employees = $employees->filter(function ($employee) use ($team) {
                if (empty($employee->teams['data'])) {
                    return true;
                }
                $teams = $employee->teams['data'];
                return collect($teams)->where('id', $team->id)->count() === 0;
            });
        }

        $this->data['employees'] = $employees;

        $this->data['page']['title'] .= ' &rsaquo; Teams &rsaquo; '.$team->name;
        $this->data['header']['title'] = 'Teams &rsaquo; '.$team->name;
        $this->data['selectedSubMenu'] = 'people-teams';
        //$this->data['submenuAction'] = '<a href="#" v-on:click.prevent="createTeam" class="btn btn-primary btn-block">Add Team</a>';

        return view('modules-people::teams.team', $this->data);

    }

    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function teams_employees_delete(Request $request, Sdk $sdk, string $id)
    {
        $this->validate($request, [
            'employees' => 'required|array',
            'employees.*' => 'string'
        ]);
        # validate the request
        $model = $sdk->createTeamResource($id);
        $response = $model->addBodyParam('employees', $request->input('employees', []))
                            ->send('delete', ['employees']);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            $message = $response->getErrors()[0]['title'] ?? 'Failed while removing the employee(s) from the team.';
            throw new DeletingFailedException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.teams.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }


    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function teams_employees_add(Request $request, Sdk $sdk, string $id)
    {
        /*$this->validate($request, [
            'employees' => 'required_with:add_employees|array',
            'employees.*' => 'string'
        ]);
        */

        $model = $sdk->createTeamResource($id)->addBodyParam('employees', $request->employees);
        $response = $model->send('post', ['employees']);
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            $message = $response->errors[0]['title'] ?? 'Failed while adding the employee record.';
            throw new \RuntimeException($message);
        }
        Cache::forget('business.teams.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);


    }


}