<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\Company\AddCategoryInCompanyRequest;
use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Company Management
 *
 *  APIs to manage the category resource
 */
class CompanyController extends BaseController
{
    public function __construct()
    {
//        $this->middleware('permission:company_create', ['only' => ['store']]);
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
        $company = QueryBuilder::for(Company::class)
            ->allowedFilters(
                'name',
                'acronym',
                AllowedFilter::exact('id')
            )
            ->with('categories.specs', 'categories.units', 'categories.units.specs', 'users')
            ->get();
//        $company = Company::all();
        if ($company->isNotEmpty()) {
            return $this->sendResponse($company, 'Company retrieved successfully.');
        }

        return $this->sendError('No Record.');
    }

    /**
     * Store a newly created resource in storage.
     *
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
        $company->name = $input['name'];
        $company->acronym = $input['acronym'];
//        $company->status = $input['status'];
        $company->save();

        return $this->sendResponse(new CompanyResource($company), 'Company updated successfully.');
    }

    /**
     * Add Categories on specific company
     *
     * @param  AddCategoryInCompanyRequest  $request
     * @param  Company  $company
     * @return JsonResponse
     */
    public function addCategory(AddCategoryInCompanyRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $company->categories()->sync($input['categories']);
        $company->save();
        $company->load('categories');

        return $this->sendResponse($company, 'Admin successfully added category');
    }
}
