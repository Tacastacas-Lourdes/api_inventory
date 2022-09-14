<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\Unit\StoreUnitRequest;
use App\Http\Requests\Unit\UpdateUnitRequest;
use App\Http\Resources\UnitResource;
use App\Models\Specification;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Unit Management
 */
class UnitController extends BaseController
{
    public function __construct()
    {
//        $this->middleware('permission:unit_create', ['only' => ['store']]);
//        $this->middleware('permission:product-create', ['only' => ['create','store']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $unit = QueryBuilder::for(Unit::class)
            ->allowedFilters(
                'brand',
                'model',
                'serial',
                'employee_id',
                AllowedFilter::exact('id')
            )
            ->with('category', 'company', 'status', 'remarks', 'user', 'specs')
            ->get();

        return $this->sendResponse($unit, 'Unit retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreUnitRequest  $request
     * @return JsonResponse
     */
    public function store(StoreUnitRequest $request): JsonResponse
    {
        $input = $request->validated();
        $unit = Unit::query()->make($input);
        $unit->company()->associate($input['company_id']);
        $unit->category()->associate($input['category_id']);
        $unit->save();
        $unit->remarks()->createMany($input['remarks']);
        $details = $input['details'];
        $sync = [];
//        dd($details);
        Specification::query()
            ->where('category_id', $input['category_id'])
            ->each(function (Specification $spec) use ($details, &$sync) {
//                dd(Arr::get($details, $spec->name));
                $sync[$spec->id] = ['details' => Arr::get($details, $spec->name)];
            });
        $unit->specs()->sync($sync);
        $unit->load('specs', 'company', 'category', 'remarks', 'user');

        return $this->sendResponse($unit, 'Unit created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  Unit  $unit
     * @return JsonResponse
     */
    public function show(Unit $unit): JsonResponse
    {
        return $this->sendResponse(new UnitResource($unit), 'Unit retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateUnitRequest  $request
     * @param  Unit  $unit
     * @return JsonResponse
     */
    public function update(UpdateUnitRequest $request, Unit $unit): JsonResponse //not done yet
    {
        $input = $request->all();
        $unit->brand = $input['brand'];
        $unit->model = $input['model'];
        $unit->serial = $input['serial'];
        $unit->save();
        $details = $input['details'];
        $sync = [];
        Specification::query()
            ->where('category_id', $input['category_id'])
            ->each(function (Specification $spec) use ($details, &$sync) {
                $sync[$spec->id] = ['details' => Arr::get($details, $spec->name)];
            });
        $unit->specs()->sync($sync);

        return $this->sendResponse(new UnitResource($unit), 'Unit updated successfully.');
    }

    //another function for assign user to the unit
    public function assignUser(User $user)
    {
    }
}
