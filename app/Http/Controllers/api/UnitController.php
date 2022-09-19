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
                'unit_id',
                AllowedFilter::exact('id')
            )
            ->with('category', 'company', 'status', 'remarks', 'user', 'specs')
            ->get();
        if ($unit->isNotEmpty()) {
            return $this->sendResponse($unit, 'Unit retrieved successfully.');
        }

        return $this->sendError('No Record.', ['error' => 'No Record']);
    }

    /**
     *  Display a listing of resource with the given company and category ID
     *
     * @urlParam company_id int required Company ID of the unit. Example: 1
     * @urlParam category_id int required Category ID of the unit. Example: 1
     *
     * @param $com_id
     * @param $cat_id
     * @return JsonResponse
     */
    public function getCategoryCompany($com_id, $cat_id): JsonResponse
    {
        $unit = Unit::query()->where([['company_id', $com_id], ['category_id', $cat_id]])->get();
        $unit->load('status');
        if ($unit->isNotEmpty()) {
            return $this->sendResponse($unit, 'Good Job');
        }

        return  $this->sendError('No Record.', ['error' => 'No Record']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @bodyParam brand string required Brand name of the unit. Example: Intel
     * @bodyParam model string required Model name of the unit. Example: PTSE3U-06N006
     * @bodyParam serial string required Serial of the unit. Example: 7H137673A
     * @bodyParam company_id int required Company ID of the unit. Example: 1
     * @bodyParam category_id int required Category ID of the unit. Example: 1
     * @bodyParam details object[] required Specification details. Example: ["CPU" : "Intel", "Memory" : "8G"]
     * @bodyParam details[].CPU string required Details name for CPU. Example: Intel
     * @bodyParam details[].Memory string required Details name for Memory. Example: 8G
     * @bodyParam remarks object[] string required Remarks of the unit. Example: admin
     * @bodyParam remarks[].name string required Remarks name of the unit. Example: Defected Speaker
     * @bodyParam remarks[].date required Remarks date of the unit. Example: 2022-09-09
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
        Specification::query()
            ->where('category_id', $input['category_id'])
            ->each(function (Specification $spec) use ($details, &$sync) {
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
     * @bodyParam unit_id string required Brand name of the unit. Example: FAu-Laptop-00006
     * @bodyParam brand string required Brand name of the unit. Example: Intel
     * @bodyParam model string required Model name of the unit. Example: PTSE3U-06N006
     * @bodyParam serial string required Serial of the unit. Example: 7H137673A
     * @bodyParam company_id int required Company ID of the unit. Example: 1
     * @bodyParam category_id int required Category ID of the unit. Example: 1
     * @bodyParam details object[] required Specification details. Example: ["CPU" : "Intel", "Memory" : "8G"]
     * @bodyParam details[].CPU string required Details name for CPU. Example: Intel
     * @bodyParam details[].Memory string required Details name for Memory. Example: 8G
     * @bodyParam remarks object[] string required Remarks of the unit. Example: admin
     * @bodyParam remarks[].name string required Remarks name of the unit. Example: Defected Speaker
     * @bodyParam remarks[].date required Remarks date of the unit. Example: 2022-09-09
     *
     * @param  UpdateUnitRequest  $request
     * @param  Unit  $unit
     * @return JsonResponse
     */
    public function update(UpdateUnitRequest $request, Unit $unit): JsonResponse //not done yet
    {
//        dd($unit);
        $input = $request->validated();
        $unit->unit_id = $input['unit_id'];
        $unit->brand = $input['brand'];
        $unit->model = $input['model'];
        $unit->serial = $input['serial'];
        $unit->company()->associate($input['company_id']);
        $unit->category()->associate($input['category_id']);
        $unit->save();
        $unit->remarks()->createMany($input['remarks']);
        $details = $input['details'];
        $sync = [];
        Specification::query()
            ->where('category_id', $input['category_id'])
            ->each(function (Specification $spec) use ($details, &$sync) {
                $sync[$spec->id] = ['details' => Arr::get($details, $spec->name)];
//                dd($spec);
            });
        $unit->specs()->sync($sync);

        return $this->sendResponse(new UnitResource($unit), 'Unit updated successfully.');
    }

    //another function for assign user to the unit
    public function assignUser(User $user)
    {
    }
}
