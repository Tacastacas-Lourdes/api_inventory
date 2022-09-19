<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Specification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Category Management
 *
 * APIs to manage the category resource.
 */
class CategoryController extends BaseController
{
    public function __construct()
    {
//        $this->middleware('permission:category_create', ['only' => ['index']]);
//        $this->middleware('permission:product-create', ['only' => ['create','store']]);
//        $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
//        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     *
     * @header Authorization Bearer {token}
     * @apiResourceCollection App\Http\Resources\CategoryResource
     * @apiResourceModel App\Models\Category
     * @authenticated
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $category = QueryBuilder::for(Category::class)
            ->allowedFilters('name', AllowedFilter::exact('id'))
            ->with(['units', 'specs'])->get();
        if ($category->isNotEmpty()) {
            return $this->sendResponse($category, 'Category retrieved successfully.');
        }

        return $this->sendError('No Record.');
    }

    public function getSpecs($id): JsonResponse
    {
        $categories = Specification::query()->whereHas('category', function (Builder $query) use ($id) {
            $query->where('id', '=', $id);
        })->get();
        if ($categories->isNotEmpty()) {
            return $this->sendResponse($categories, 'Related categories were successfully retrieved.');
        }
        return $this->sendError('No record found.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @bodyParam name string required Name of the category. Example: Laptop
     * @bodyParam spec object[] required Example: [
     * {
     * "name": "CPU"
     * }]
     * @bodyParam spec[].name string required Name of the specification. Example: CPU
     *
     * @param  StoreCategoryRequest  $request
     * @return JsonResponse
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $input = $request->validated();
        $category = Category::query()->create($input);
        if ($specs = $request->get('spec')) {
            $category->specs()->createMany($specs);

            return $this->sendResponse(new CategoryResource($category), 'Category & Specification created successfully.');
        } else {
            return $this->sendResponse(new CategoryResource($category), 'Category created successfully.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @urlParam id int required The ID of the category.
     * @apiResource  App\Http\Resources\CategoryResource
     * @apiResourceModel App\Models\Category
     *
     * @param  Category  $category
     * @return JsonResponse
     */
    public function show(Category $category): JsonResponse
    {
        return $this->sendResponse(new CategoryResource($category), 'Category retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateCategoryRequest  $request
     * @param  Category  $category
     * @return JsonResponse
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $input = $request->validated();
        $category->name = $input['name'];
        $category->save();

        return $this->sendResponse(new CategoryResource($category), 'Category updated successfully.');
    }
}
