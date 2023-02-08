<?php

namespace App\Http\Resources\Api;

use App\Helpers\SalaryAdvanceHelper;
use App\Http\Resources\Api\AccountResource;
use App\Http\Resources\Api\BusinessResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientLoginResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'names' => $this->names,
            'surnames' => $this->surnames,
            'document_type' => $this->document_type,
            'document_number' => $this->document_number,
            'business' => new BusinessResource($this->business),
            'email' => $this->email,
            'salary' => $this->salary,
            'salary_view' => $this->salary_view,
            'available_salary' => SalaryAdvanceHelper::getAvailableSalary($this->salary, $this->business->reliability),
            'accounts' => AccountResource::collection($this->accounts)
        ];
    }
}
