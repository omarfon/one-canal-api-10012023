<?php

namespace App\Http\Controllers\Admin;

use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;
use App\Models\SalaryAdvance;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SalaryAdvanceRequest;
use App\Http\Requests\Admin\ChangeStatusSelectedRequest;

class ClientHistoryController extends Controller
{
    public function index(Request $request, $client_id)
    {
        $transactions = SalaryAdvance::filterByColumns($request->all())
        ->with('account.bank', 'reason')
        ->where('user_id', $client_id)
        ->orderBy('id', 'desc')
        ->paginate(5);

        foreach ($transactions as $key => $transaction) {
            $transaction->logs = json_decode($transaction->logs);
        }

        return $this->successResponse($transactions, 200);
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

    public function changeStatusSelectedAccounts(ChangeStatusSelectedRequest $request)
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

        return $this->successResponse([
            'transactions' => $transactions
        ], 200);
    }
}
