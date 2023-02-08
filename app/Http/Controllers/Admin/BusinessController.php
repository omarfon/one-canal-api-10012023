<?php

namespace App\Http\Controllers\Admin;

use App\Models\Activity;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\BusinessRequest;
use App\Http\Requests\Admin\DeleteSelectedRequest;
use App\Http\Requests\Admin\ChangeStatusSelectedRequest;

class BusinessController extends Controller
{
    public function index(Request $request)
    {
        $business = Business::filterByColumns($request->all())->orderBy('id', 'desc')->paginate();

        return $this->successResponse($business, 200);
    }

    public function indexAll()
    {
        $business = Business::orderBy('name', 'asc')->get();

        return $this->successResponse($business, 200);
    }

    public function show($id)
    {
        $business = Business::whereId($id)->first();

        return $this->successResponse([
            'business' => $business
        ], 200);
    }

    public function store(BusinessRequest $request)
    {
        $data = $request->validated();

        $business = Business::create($data);

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "business",
            "color" => "text-success",
            "bold" => true,
            "text" => "Creación de empresa " . $business->name
        ]);

        return $this->successResponse([
            'business' => $business
        ], 200);
    }

    public function update(BusinessRequest $request, $id)
    {
        $data = $request->validated();

        $business = Business::whereId($id)->first();

        $business->update($data);

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "business",
            "color" => "text-success",
            "bold" => true,
            "text" => "Actualización de empresa " . $business->name
        ]);

        return $this->successResponse([
            'business' => $business
        ], 200);
    }

    public function delete($id)
    {
        $business = Business::whereId($id)->first();

        $business->delete();

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "business",
            "color" => "text-success",
            "bold" => true,
            "text" => "Eliminación de empresa " . $business->name
        ]);

        return $this->successResponse([
            'business' => $business
        ], 200);
    }

    // Selected

    public function deleteSelected(DeleteSelectedRequest $request)
    {
        $data = $request->validated();

        $business = Business::whereIn('id', $data['ids'])->delete();

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "business",
            "color" => "text-success",
            "bold" => true,
            "text" => "Eliminación de empresas"
        ]);

        return $this->successResponse([
            'business' => $business
        ], 200);
    }

    public function changeStatusSelected(ChangeStatusSelectedRequest $request)
    {
        $data = $request->validated();

        $business = Business::whereIn('id', $data['ids'])->update([
            'active' => $data['status']
        ]);

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "business",
            "color" => "text-success",
            "bold" => true,
            "text" => "Cambio de estado de empresas"
        ]);

        return $this->successResponse([
            'business' => $business
        ], 200);
    }

    public function storeFeesRange(Request $request, $id)
    {
        $data = $request->all();

        return $this->successResponse([
            'request' => $data
        ], 200);
    }
}
