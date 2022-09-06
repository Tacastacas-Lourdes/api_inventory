<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\Status\StoreStatusRequest;
use App\Http\Requests\Status\UpdateStatusRequest;
use App\Http\Resources\StatusResource;
use App\Models\Status;
use Illuminate\Http\JsonResponse;

class StatusController extends BaseController
{
    public function __construct()
    {
//        $this->middleware('permission:status_create', ['only' => ['store']]);
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
        $category = Status::all();

        return $this->sendResponse(StatusResource::collection($category), 'Status retrieved successfully.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreStatusRequest  $request
     * @return JsonResponse
     */
    public function store(StoreStatusRequest $request): JsonResponse
    {
        $input = $request->validated();
        $status = Status::query()->create($input);

        return $this->sendResponse(new StatusResource($status), 'Status created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  Status  $status
     * @return JsonResponse
     */
    public function show(Status $status): JsonResponse
    {
        return $this->sendResponse(new StatusResource($status), 'Status retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateStatusRequest  $request
     * @param  Status  $status
     * @return JsonResponse
     */
    public function update(UpdateStatusRequest $request, Status $status): JsonResponse
    {
        $input = $request->validated();
        $status->status = $input['name'];

        return $this->sendResponse(new StatusResource($status), 'Status updated successfully.');
    }

//    /**
//     * Remove the specified resource from storage.
//     *
//     * @param  Status  $status
//     * @return JsonResponse
//     */
//    public function destroy(Status $status): JsonResponse
//    {
//        $status->delete();
//
//        return $this->sendResponse($status, 'Status deleted.');
//    }
}
