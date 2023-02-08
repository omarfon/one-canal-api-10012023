<?php

namespace App\Http\Resources\Api;

use App\Helpers\SalaryAdvanceHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientProfileResource extends JsonResource
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
            'salary_updated' => $this->salary_updated,
            'available_salary' => SalaryAdvanceHelper::getAvailableSalary($this->salary, $this->business->reliability),
            'accounts' => AccountResource::collection($this->accounts),
            'marital_status' => new MaritalStatusResource($this->marital_status),
            'address' => $this->address,
            'business_job' => $this->business_job,
        ];
    }
}
