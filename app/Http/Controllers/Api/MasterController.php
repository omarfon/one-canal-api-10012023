<?php

namespace App\Http\Controllers\Api;

use App\Models\Fee;
use App\Models\Bank;
use App\Models\User;
use App\Models\Format;
use App\Models\Reason;
use App\Models\DocumentType;
use App\Models\Configuration;
use App\Models\MaritalStatus;
use App\Helpers\SalaryAdvanceHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Api\FeeResource;
use App\Http\Resources\Api\ReasonResource;
use App\Http\Resources\Api\DocumentTypeResource;

class MasterController extends Controller
{
    public function indexDocumentTypes()
    {
        $document_types = DocumentType::get();

        return $this->successResponse(DocumentTypeResource::collection($document_types), 200);
    }

    public function indexFees()
    {
        $user = User::whereId(Auth::user()->id)->with('business.fees_ranges')->first();

        $fees = Fee::all();

        foreach ($fees as $key => $fee) {
            if($fee->type == 'FEE') {
                $fee->value = $user->business->fees_ranges ?? [];
            }
        }

        return $this->successResponse(FeeResource::collection($fees), 200);
    }

    public function indexReasons()
    {
        $reasons = Reason::where('active', 1)->orderBy('name')->get();

        return $this->successResponse(ReasonResource::collection($reasons), 200);
    }

    public function indexTerms()
    {
        $type = "terms";

        if (request()->type) {
            $type = request()->type;
        }

        $format = Format::where('type', $type)->first();

        return $this->successResponse([
            "id" => $format->id,
            "type" => $type,
            "text" => $format->body,
            "created_at" => $format->created_at,
            "updated_at" => $format->updated_at
        ], 200);
    }

    public function indexAdvanceFormat()
    {
        $type = "advance";

        $user = User::whereId(Auth::user()->id)->first();

        $format = Format::where('type', $type)->first();

        $body = SalaryAdvanceHelper::generatePdf($user, $type, "html");

        return $this->successResponse([
            "id" => $format->id,
            "type" => $type,
            "text" => $body,
            "created_at" => $format->created_at,
            "updated_at" => $format->updated_at
        ], 200);
    }

    public function indexBanks()
    {
        $banks = Bank::where('active', 1)->orderBy('name', 'asc')->get();

        return $this->successResponse($banks, 200);
    }

    public function indexMaritalStatus()
    {
        $banks = MaritalStatus::where('active', 1)->get();

        return $this->successResponse($banks, 200);
    }
}
