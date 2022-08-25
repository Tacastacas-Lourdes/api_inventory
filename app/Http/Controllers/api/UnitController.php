<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\Unit\StoreUnitRequest;
use App\Http\Requests\Unit\UpdateUnitRequest;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\UnitResource;
use App\Models\Category;
use App\Models\Remark;
use App\Models\Specification;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class UnitController extends BaseController
{
    public function __construct()
    {
        $this->middleware('permission:unit_create', ['only' => ['store']]);
//        $this->middleware('permission:product-create', ['only' => ['create','store']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $unit = Unit::all();

        return $this->sendResponse(CompanyResource::collection($unit), 'Unit retrieved successfully.');
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
        $unit = Unit::query()->create($input);

        if ($unit instanceof Unit) {
            $unit->company()->associate($input['company_id']);
            $unit->category()->associate($input['category_id']);
            $unit->status()->associate($input['status_id']);
            $unit->save();
        }
//        dd($unit->category);
        $unit->count = Unit::query()->where('category_id', $unit->category->id)->max('count') + 1;
        $unit->unit_id = $unit->company->acronym.'-'.$unit->category->name.'-'.str_pad($unit->count, 6, 0, STR_PAD_LEFT);
        $unit->save();

        $details = $input['details'];
        $sync = [];
        Specification::query()
            ->where('category_id', $input['category_id'])
            ->each(function (Specification $spec) use ($details, &$sync) {
                $sync[$spec->id] = ['details' => Arr::get($details, $spec->name)];
            });
        $unit->specs()->sync($sync);
        $name = $input['remarks'];
        $date = $input['date'];
        for ($i = 0; $i < count($name); $i++) {
            Remark::query()->create([
                'name' => $name[$i],
                'date' => $date[$i],
            ])->unit()->associate($unit)->save();
        }
        $unit->load('specs', 'company', 'status', 'category', 'remark');

        return $this->sendResponse(new UnitResource($unit), 'Unit created successfully.');
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
    public function update(UpdateUnitRequest $request, Unit $unit): JsonResponse
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $unit->name = $input['name'];
        $unit->detail = $input['detail'];
        $unit->save();

        return $this->sendResponse(new CompanyResource($unit), 'Unit updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Unit  $unit
     * @return JsonResponse
     */
    public function destroy(Unit $unit): JsonResponse
    {
        $unit->delete();

        return $this->sendResponse([], 'Unit delete.');
    }

    //another function for assign user to the unit
}
