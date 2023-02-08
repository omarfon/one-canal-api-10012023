<?php

namespace App\Http\Controllers\Admin;

use App\Models\Fee;
use App\Models\Activity;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\FeeRequest;

class FeeController extends Controller
{
    public function index()
    {
        $banks = Fee::whereIn('type', ['IGV', 'ITF'])->orderBy('type', 'asc')->get();

        return $this->successResponse($banks, 200);
    }

    public function update(FeeRequest $request, $id)
    {
        $data = $request->only(['value']);

        $fee = Fee::whereId($id)->first();

        $fee->update($data);

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "fees",
            "color" => "text-success",
            "bold" => true,
            "text" => "Actualización de comisión " . $fee->type
        ]);

        return $this->successResponse([
            'fee' => $fee->fresh()
        ], 200);
    }
}
