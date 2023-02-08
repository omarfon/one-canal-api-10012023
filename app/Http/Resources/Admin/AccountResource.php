<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'bank_id' => $this->bank_id,
            'bank' => new BankResource($this->bank),
            'user_id' => $this->user_id,
            'number' => $this->number,
            'active' => $this->active
        ];
    }
}
