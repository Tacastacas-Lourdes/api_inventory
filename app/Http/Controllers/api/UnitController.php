<?php

namespace App\Http\Controllers\api;

use App\Http\Resources\CompanyResource;
use App\Http\Resources\UnitResource;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;

class UnitController extends BaseController
{
    function __construct()
    {
        $this->middleware('permission:unit_create', ['only' => ['store']]);
//        $this->middleware('permission:product-create', ['only' => ['create','store']]);

    }
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index()
    {
        $unit = Unit::all();
        return $this->sendResponse(CompanyResource::collection($unit), 'Unit retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand' => 'required',
            'model' => 'required',
            'serial' => 'required',
//            'company_id' => 'required',
//            'category_id' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $unit = Unit::create([
            'brand' => $request['brand'],
            'model' => $request['model'],
            'serial' => $request['serial'],]);
        if ($unit instanceof Unit){
            $unit->company()->associate($request->company_id);
            $unit->category()->associate($request->company_id);
            $unit->status()->associate($request->status_id);
        }
        //Unit::query()->has('status')->toSql()  ---> select all from `statuses` where `units`.`status_id` = `statuses`.`id`
        $unit->save();
        return $this->sendResponse(new UnitResource($unit), 'Unit created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $unit = Unit::find($id);
        if (is_null($unit)) {
            return $this->sendError('Unit not found.');
        }
        return $this->sendResponse(new CompanyResource($unit), 'Unit retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, Unit $unit)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required'
        ]);
        if($validator->fails()){
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
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();
        return $this->sendResponse([], 'Unit delete.');
    }

    //another function for assign user to the unit

}
