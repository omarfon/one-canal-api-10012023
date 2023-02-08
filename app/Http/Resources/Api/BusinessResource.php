<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'ruc' => $this->ruc,
            'name' => $this->name
        ];
    }
}
