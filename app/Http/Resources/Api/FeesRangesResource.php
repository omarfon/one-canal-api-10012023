<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class FeesRangesResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'min' => $this->min,
            'max' => $this->max,
            'fee' => $this->fee
        ];
    }
}
