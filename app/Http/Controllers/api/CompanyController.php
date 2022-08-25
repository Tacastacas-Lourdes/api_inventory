<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class CompanyController extends BaseController
{
    public function __construct()
    {
        $this->middleware('permission:company_create', ['only' => ['store']]);
//        $this->middleware('permission:product-create', ['only' => ['create','store']]);
//        $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
//        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $company = Company::all();

        return $this->sendResponse(CompanyResource::collection($company), 'Company retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreCompanyRequest  $request
     * @return JsonResponse
     */
    public function store(StoreCompanyRequest $request): JsonResponse
    {
        $input = $request->validated();
        $company = Company::query()->create($input);

        return $this->sendResponse(new CompanyResource($company), 'Company created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  Company  $company
     * @return JsonResponse
     */
    public function show(Company $company): JsonResponse
    {
        return $this->sendResponse(new CompanyResource($company), 'Company retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateCompanyRequest  $request
     * @param  Company  $company
     * @return JsonResponse
     */
    public function update(UpdateCompanyRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $company->company = $input['name'];
        $company->acronym = $input['acronym'];

        return $this->sendResponse(new CompanyResource($company), 'Company updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Company  $company
     * @return JsonResponse
     */
    public function destroy(Company $company): JsonResponse
    {
        $company->delete();

        return $this->sendResponse($company, 'Company deleted.');
    }
}
