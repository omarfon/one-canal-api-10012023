<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Statistic;
use App\Constants\Message;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;
use App\Models\SalaryAdvance;
use Illuminate\Support\Facades\DB;
use App\Helpers\SalaryAdvanceHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\PaymentPeriodRequest;
use App\Http\Requests\Api\SalaryAdvanceRequest;
use App\Http\Resources\Api\SalaryAdvanceResource;

class SalaryAdvanceController extends Controller
{
    public function indexHistory(Request $request)
    {
        $transactions = SalaryAdvance::filterByColumns($request->all())
        ->where('user_id', Auth::user()->id)
        ->with('account.bank', 'reason')
        ->orderBy('id', 'desc')
        ->paginate(10);

        return $this->successResponse(SalaryAdvanceResource::collection($transactions)->response()->getData(true), 200);
    }

    public function getPaymentPeriod(PaymentPeriodRequest $request)
    {
        $amount = $request->amount;
        $user = User::whereId(Auth::user()->id)->first();

        if ($amount > SalaryAdvanceHelper::getAvailableSalary($user->salary, $user->business->reliability)) {
            return $this->errorResponse(Message::SALARY_ADVANCE_REQUEST_ERROR, 409);
        };

        if (!SalaryAdvanceHelper::availabilityDays()){
            return $this->errorResponse(Message::OUT_OF_PERIOD, 409);
        }

        $period_name = SalaryAdvanceHelper::getPeriodName();

        if (!SalaryAdvanceHelper::periodLimitUser($user->id, $period_name)){
            return $this->errorResponse(Message::LIMIT_OF_PERIOD, 409);
        }

        return $this->successResponse([
            "period_name" => $period_name,
            "amount" => "S/ " . $amount,
            "fees" => SalaryAdvanceHelper::getFeesAmount($amount),
            "transfer_amount" => SalaryAdvanceHelper::getAmountTransfer($amount),
            "date" => date('d-m-Y')
        ], 200);
    }

    public function store(SalaryAdvanceRequest $request)
    {
        $data = $request->validated();
        $user = User::whereId(Auth::user()->id)->first();

        if (!SalaryAdvanceHelper::availabilityDays()){
            return $this->errorResponse(Message::OUT_OF_PERIOD, 409);
        }

        $period_name = SalaryAdvanceHelper::getPeriodName();

        if (!SalaryAdvanceHelper::periodLimitUser($user->id, $period_name)){
            return $this->errorResponse(Message::LIMIT_OF_PERIOD, 409);
        }

        if ($data['amount'] > SalaryAdvanceHelper::getAvailableSalary($user->salary, $user->business->reliability)) {
            return $this->errorResponse(Message::SALARY_ADVANCE_REQUEST_ERROR, 409);
        };

        if ($data['period_name'] != $period_name) {
            return $this->errorResponse(Message::SALARY_ADVANCE_REQUEST_ERROR, 409);
        }

        if ($data['transfer_amount'] != SalaryAdvanceHelper::getAmountTransfer($data['amount'])) {
            return $this->errorResponse(Message::SALARY_ADVANCE_REQUEST_ERROR, 409);
        }

        if ($data['fees'] != SalaryAdvanceHelper::getFeesAmount($data['amount'])) {
            return $this->errorResponse(Message::SALARY_ADVANCE_REQUEST_ERROR, 409);
        }

        $logs = [
            "date" => date('Y-m-d H:i:s'),
            "status" => "Registro",
            "comment" => "Transferencia registrada por el usuario desde la aplicación One Canal"
        ];

        $salary_advance = SalaryAdvance::create([
            "account_id" => $data['account_id'],
            "user_id" => $user->id,
            "amount" => $data['amount'],
            "fees_amount" => SalaryAdvanceHelper::getFeesAmount($data['amount'], false),
            "transfer_amount" => SalaryAdvanceHelper::getAmountTransfer($data['amount'], false),
            "period_name" => $period_name,
            "logs" => json_encode([$logs]),
            "reason_id" => $data['reason_id']
        ]);

        Statistic::where('date', date('Y-m-d'))->where('type', 'salary-advances-amounts')->update([
            'value' => DB::raw('value + ' . $data['amount'])
        ]);

        Statistic::where('date', date('Y-m-d'))->where('type', 'salary-advances-numbers')->update([
            'value' => DB::raw('value + 1')
        ]);

        $this->dispatch(new SendEmailJob($user->email, [
            "salaryAdvance" => $salary_advance,
            "user" => $user
        ], "SendMailNewSalaryAdvance"));

        $this->dispatch(new SendEmailJob(User::where('role', 'admin')->get()->pluck('email'), $salary_advance, "SendMailAdminNewSalaryAdvance"));

        return $this->successResponse(new SalaryAdvanceResource($salary_advance->fresh()), 200);
    }
}
