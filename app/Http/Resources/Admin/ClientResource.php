<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\Admin\BusinessResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'names' => $this->names,
            'surnames' => $this->surnames,
            'document_type' => $this->document_type,
            'document_number' => $this->document_number,
            'business_id' => $this->business_id,
            'business' => new BusinessResource($this->business),
            'email' => $this->email
        ];
    }
}
