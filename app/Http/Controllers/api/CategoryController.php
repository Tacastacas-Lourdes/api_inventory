<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Specification;
use Illuminate\Http\JsonResponse;

class CategoryController extends BaseController
{
    public function __construct()
    {
        $this->middleware('permission:category_create', ['only' => ['store']]);
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
        $category = Category::all();

        return $this->sendResponse(CategoryResource::collection($category), 'Category retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreCategoryRequest  $request
     * @return JsonResponse
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $input = $request->validated();
        $category = Category::query()->create($input);
        if ($specs = $request->get('spec')) {
            foreach ($specs as $spec) {
                Specification::query()->create([
                    'name' => $spec,
                ])->category()->associate($category)->save();
            }

            return $this->sendResponse(new CategoryResource($category), 'Category & Specification created successfully.');
        } else {
            return $this->sendResponse(new CategoryResource($category), 'Category created successfully.');
        }

//            $specs = collect($specs)->map(function($spec){
//               return Specification::create([
//                    'name' => $spec,
//                ]);
//            });
//            $category->spec()->sync($specs);
    }

    /**
     * Display the specified resource.
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

        return $this->sendResponse(new CategoryResource($category), 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Category  $category
     * @return JsonResponse
     */
    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return $this->sendResponse(new CategoryResource($category), 'Category deleted.');
    }
}
