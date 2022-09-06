<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\Remark\StoreRemarkRequest;
use App\Http\Requests\Remark\UpdateRemarkRequest;
use App\Http\Resources\RemarkResource;
use App\Models\Remark;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;

class RemarkController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $unit = Remark::all();
        if ($unit->isNotEmpty()) {
            return $this->sendResponse(RemarkResource::collection($unit), 'Remark retrieved successfully.');
        }

        return $this->sendError('No record.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreRemarkRequest  $request
     * @return JsonResponse
     */
    public function store(StoreRemarkRequest $request): JsonResponse
    {
        $input = $request->validated();
        $unit = Unit::query()->findOrFail($input['unit']);
        $name = $input['name'];
        $date = $input['date'];
        for ($i = 0; $i < count($input['name']); $i++) {
            $remark = Remark::query()->create([
                'name' => $name[$i],
                'date' => $date[$i],
            ])->unit()->associate($unit)->save();
        }

        return $this->sendResponse(new RemarkResource($remark), 'Remarks created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  Remark  $remark
     * @return JsonResponse
     */
    public function show(Remark $remark): JsonResponse
    {
        return $this->sendResponse(new RemarkResource($remark), 'Remarks retrieved successfully.');
    }

//    /**
//     * Update the specified resource in storage.
//     *
//     * @param  UpdateRemarkRequest  $request
//     * @param  Remark  $remark
//     * @return JsonResponse
//     */
//    public function update(UpdateRemarkRequest $request, Remark $remark): JsonResponse
//    {
//        $input = $request->validated();
//        $remark->company = $input['name'];
//        $remark->acronym = $input['date'];
//        return $this->sendResponse(new RemarkResource($remark), 'Remark created successfully.');
//    }
}
