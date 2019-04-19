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
use App\Exceptions\RecordNotFoundException;

class ModulesPeopleController extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->data = [
            'page' => ['title' => config('modules-people.title')],
            'header' => ['title' => config('modules-people.title')],
            'selectedMenu' => 'people',
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
        $this->data['selectedMenu'] = 'departments';
        $this->data['submenuAction'] = '<a href="#" v-on:click.prevent="createDepartment" class="btn btn-primary btn-block">Add Department</a>';

        $this->setViewUiResponse($request);
        $this->data['departments'] = $this->getDepartments($sdk);
        return view('modules-people::departments.departments', $this->data);
    }



    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function departments_create(Request $request, Sdk $sdk)
    {
        $query = $sdk->createDepartmentResource()->addBodyParam('name', $request->input('name'))
                                                    ->send('POST');
        # send request
        if (!$query->isSuccessful()) {
            $message = $query->getErrors()[0]['title'] ?? 'Failed while trying to create the department.';
            throw new \RuntimeException($message);
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.departments.'.$company->id);
        return response()->json($query->getData());
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
    public function departments_update(Request $request, Sdk $sdk, string $id)
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
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeEmployees(Request $request, Sdk $sdk, string $id)
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


}