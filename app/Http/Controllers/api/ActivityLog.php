<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\JsonResponse;
use Spatie\Activitylog\Models\Activity;

/**
 * @group Activity Logs
 */
class ActivityLog extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function getActivityLogs(): JsonResponse
    {
        return $this->sendResponse(Activity::all(), 'Activity Logs retrieved successfully');
    }
}
