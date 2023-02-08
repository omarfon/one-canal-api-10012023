<?php

namespace App\Http\Controllers\Admin;

use App\Models\Activity;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;
use App\Models\SalaryAdvance;
use App\Exports\SalaryAdvanceExport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\SalaryAdvanceRequest;
use App\Http\Resources\Admin\SalaryAdvanceResource;
use App\Http\Requests\Admin\ChangeStatusSelectedRequest;

class SalaryAdvanceController extends Controller
{
    public function index(Request $request)
    {
        $transactions = SalaryAdvance::filterByColumns($request->all())
        ->with('account.bank', 'reason', 'user.business')
        ->orderBy('id', 'desc')
        ->paginate(20);

        return $this->successResponse(SalaryAdvanceResource::collection($transactions)->response()->getData(true), 200);
    }

    public function update(SalaryAdvanceRequest $request, $id)
    {
        $data = $request->validated();

        $transaction = SalaryAdvance::whereId($id)->first();

        $transaction->update($data);

        return $this->successResponse([
            'transaction' => $transaction
        ], 200);
    }

    public function changeStatusSelected(ChangeStatusSelectedRequest $request)
    {
        $data = $request->validated();

        $transactions = SalaryAdvance::whereIn('id', $data['ids'])->with('user')->get();

        foreach ($transactions as $key => $transaction) {
            SalaryAdvance::whereId($transaction->id)->update([
                'status' => $data['status']
            ]);

            $transaction->status = $data['status'];

            if ($data['status'] == 'CANCELED') {
                $this->dispatch(new SendEmailJob($transaction->user->email, $transaction, "SendMailRefuseSalaryAdvance"));
            } else {
                $this->dispatch(new SendEmailJob($transaction->user->email, $transaction, "SendMailChangeStatusSalaryAdvance"));
            }
        }

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "transactions",
            "color" => "text-warning",
            "bold" => true,
            "text" => "Cambio de estado de transacciones: " . implode(", ", $data['ids'])
        ]);

        return $this->successResponse([
            'transactions' => $transactions
        ], 200);
    }

    public function export(Request $request)
    {
        return $this->excel(new SalaryAdvanceExport($request), 'Transacciones');
    }
}
