<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'unit_id' => $this->unit_id,
            'brand' => $this->brand,
            'model' => $this->model,
            'serial' => $this->serial,
            //            'company_id' => $this->company_id,
            //            'category_id' => $this->category_id,
            //            'status_id' => $this->status_id,
            //            'user_id' => $this->user_id,
            //            'specification' => $this->specs,
            //            'remarks' => $this->remark,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
