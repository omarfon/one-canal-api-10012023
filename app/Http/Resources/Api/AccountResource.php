<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\Api\BankResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'bank_id' => $this->bank_id,
            'bank' => new BankResource($this->bank),
            'number' => $this->number,
            'active' => $this->active,
            'confirmed' => $this->confirmed
        ];
    }
}
