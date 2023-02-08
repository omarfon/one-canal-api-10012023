<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Account;
use App\Jobs\SendEmailJob;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\AccountRequest;
use App\Http\Resources\Api\AccountResource;

class AccountController extends Controller
{

    public function index()
    {
        $auth_user = Auth::user();

        $accounts = Account::where('user_id', $auth_user->id)
            ->where('active', 1)
            ->where('confirmed', 1)
            ->orderBy('bank_id')
            ->get();

        return $this->successResponse(AccountResource::collection($accounts), 200);
    }

    public function store(AccountRequest $request)
    {
        $data = $request->validated();

        $data['active'] = 0;
        $data['confirmed'] = 0;
        $data['user_id'] = Auth::user()->id;

        $existsAccount = Account::where('number', $data['number'])->orWhere('cci', $data['cci'])->first();

        if ($existsAccount) {
            if (!$existsAccount->confirmed) {
                return $this->errorResponse("Cuenta existente. La cuenta está siendo revisada.", 409);
            }

            return $this->errorResponse("Cuenta existente.", 409);
        }

        $account = Account::create($data);

        // Notification for admins
        $this->dispatch(new SendEmailJob(User::where('role', 'admin')->get()->pluck('email'), $account, "SendMailAdminNewUserAccount"));

        return $this->successResponse([
            'account' => $account
        ], 200);
    }

    public function update(AccountRequest $request, $id)
    {
        $data = $request->validated();

        $data['confirmed'] = 0;

        $account = Account::whereId($id)->first();

        $account->update($data);

        return $this->successResponse([
            'account' => $account
        ], 200);
    }

    public function delete($id)
    {
        $account = Account::whereId($id)->first();

        $account->delete();

        return $this->successResponse([
            'account' => $account
        ], 200);
    }
}
