<?php

namespace App\Http\Controllers\Admin;

use App\Models\Reason;
use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\SalaryAdvance;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\ReasonRequest;
use App\Http\Requests\Admin\DeleteSelectedRequest;
use App\Http\Requests\Admin\ChangeStatusSelectedRequest;

class ReasonController extends Controller
{
    public function all()
    {
        $banks = Reason::orderBy('name', 'asc')->get();

        return $this->successResponse($banks, 200);
    }

    public function index(Request $request)
    {
        $reasons = Reason::filterByColumns($request->all())->orderBy('id', 'desc')->paginate();

        return $this->successResponse($reasons, 200);
    }

    public function show($id)
    {
        $reason = Reason::whereId($id)->first();

        return $this->successResponse([
            'reason' => $reason
        ], 200);
    }

    public function store(ReasonRequest $request)
    {
        $data = $request->validated();

        $reason = Reason::create($data);

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "reasons",
            "color" => "text-info",
            "bold" => true,
            "text" => "Creación de motivo " . $data['name']
        ]);

        return $this->successResponse([
            'reason' => $reason
        ], 200);
    }

    public function update(ReasonRequest $request, $id)
    {
        $data = $request->validated();

        $reason = Reason::whereId($id)->first();

        $reason->update($data);

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "reasons",
            "color" => "text-info",
            "bold" => true,
            "text" => "Actualización de motivo " . $reason->name
        ]);

        return $this->successResponse([
            'reason' => $reason
        ], 200);
    }

    public function delete($id)
    {
        $reason = Reason::whereId($id)->first();

        if (SalaryAdvance::where('reason_id', $id)->count()) {
            return $this->errorResponse("El motivo no se puede eliminar. Tiene solicitudes de adelanto asociadas", 409);
        }

        $reason->delete();

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "reasons",
            "color" => "text-info",
            "bold" => true,
            "text" => "Eliminación de motivo " . $reason->name
        ]);

        return $this->successResponse([
            'reason' => $reason
        ], 200);
    }

    // Selected

    public function deleteSelected(DeleteSelectedRequest $request)
    {
        $data = $request->validated();

        $reasons = Reason::whereIn('id', $data['ids'])->get();

        foreach ($reasons as $key => $reason) {
            if (!SalaryAdvance::where('reason_id', $reason->id)->count()) {
                $reason->delete();
            }
        }

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "reasons",
            "color" => "text-info",
            "bold" => true,
            "text" => "Eliminación de motivos"
        ]);

        return $this->successResponse([
            'reasons' => $reasons
        ], 200);
    }

    public function changeStatusSelected(ChangeStatusSelectedRequest $request)
    {
        $data = $request->validated();

        $reasons = Reason::whereIn('id', $data['ids'])->update([
            'active' => $data['status']
        ]);

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "reasons",
            "color" => "text-info",
            "bold" => true,
            "text" => "Cambio de estado de motivos"
        ]);

        return $this->successResponse([
            'reasons' => $reasons
        ], 200);
    }
}
