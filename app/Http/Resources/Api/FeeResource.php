<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class FeeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'value' => is_string($this->value) ? $this->value : FeesRangesResource::collection($this->value)
        ];
    }
}
