<?php

namespace App\Http\Controllers\Admin;

use App\Models\Bank;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\BankRequest;
use App\Http\Requests\Admin\DeleteSelectedRequest;
use App\Http\Requests\Admin\ChangeStatusSelectedRequest;

class BankController extends Controller
{
    public function all()
    {
        $banks = Bank::where('active', 1)->orderBy('name', 'asc')->get();

        return $this->successResponse($banks, 200);
    }

    public function index(Request $request)
    {
        $banks = Bank::filterByColumns($request->all())->orderBy('id', 'desc')->paginate();

        return $this->successResponse($banks, 200);
    }

    public function show($id)
    {
        $bank = Bank::whereId($id)->first();

        return $this->successResponse([
            'bank' => $bank
        ], 200);
    }

    public function store(BankRequest $request)
    {
        $data = $request->validated();

        $bank = Bank::create($data);

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "bank",
            "color" => "text-primary",
            "bold" => true,
            "text" => "Creación de banco " . $data['short_name']
        ]);

        return $this->successResponse([
            'bank' => $bank
        ], 200);
    }

    public function update(BankRequest $request, $id)
    {
        $data = $request->validated();

        $bank = Bank::whereId($id)->first();

        $bank->update($data);

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "bank",
            "color" => "text-primary",
            "bold" => true,
            "text" => "Actualización de banco " . $data['short_name']
        ]);

        return $this->successResponse([
            'bank' => $bank
        ], 200);
    }

    public function delete($id)
    {
        $bank = Bank::whereId($id)->first();

        $bank->delete();

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "bank",
            "color" => "text-primary",
            "bold" => true,
            "text" => "Eliminación de banco " . $bank->short_name
        ]);

        return $this->successResponse([
            'bank' => $bank
        ], 200);
    }

    // Selected

    public function deleteSelected(DeleteSelectedRequest $request)
    {
        $data = $request->validated();

        $banks = Bank::whereIn('id', $data['ids'])->delete();

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "bank",
            "color" => "text-primary",
            "bold" => true,
            "text" => "Eliminación de bancos"
        ]);

        return $this->successResponse([
            'banks' => $banks
        ], 200);
    }

    public function changeStatusSelected(ChangeStatusSelectedRequest $request)
    {
        $data = $request->validated();

        $banks = Bank::whereIn('id', $data['ids'])->update([
            'active' => $data['status']
        ]);

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "bank",
            "color" => "text-primary",
            "bold" => true,
            "text" => "Cambio de estado de bancos"
        ]);

        return $this->successResponse([
            'banks' => $banks
        ], 200);
    }
}
