<?php

namespace App\Http\Controllers\Admin;

use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\Configuration;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\FeeRequest;

class ConfigurationController extends Controller
{
    public function get()
    {
        $terms = Configuration::where('type', 'terms')->first();

        return $this->successResponse($terms, 200);
    }

    public function update(Request $request, $id)
    {
        $data = $request->only(['text']);

        if ($id) {
            $terms = Configuration::whereId($id)->first();
        } else {
            $terms = Configuration::firstOrCreate([
                "type" => "terms"
            ], [
                "text" => ''
            ]);
        }

        $terms->update($data);

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "fees",
            "color" => "text-success",
            "bold" => true,
            "text" => "Actualización de términos y condiciones"
        ]);

        return $this->successResponse([
            'termns' => $terms->fresh()
        ], 200);
    }
}
