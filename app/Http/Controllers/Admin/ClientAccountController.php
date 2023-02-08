<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Account;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AccountRequest;
use App\Http\Resources\Admin\AccountResource;
use App\Http\Requests\Admin\DeleteSelectedAccountsRequest;
use App\Http\Requests\Admin\ChangeStatusSelectedAccountsRequest;

class ClientAccountController extends Controller
{
    public function index(Request $request, $client_id)
    {
        $accounts = Account::filterByColumns($request->all())->where('user_id', $client_id)->with('bank')->orderBy('id', 'desc')->paginate(5);

        return $this->successResponse($accounts, 200);
    }

    public function show($id)
    {
        $account = Account::whereId($id)->first();

        return $this->successResponse([
            'account' => new AccountResource($account)
        ], 200);
    }

    public function store(AccountRequest $request)
    {
        $data = $request->validated();

        $account = Account::create($data);

        return $this->successResponse([
            'account' => $account
        ], 200);
    }

    public function update(AccountRequest $request, $id)
    {
        $data = $request->validated();

        $account = Account::whereId($id)->first();

        $account->update($data);

        return $this->successResponse([
            'account' => $account
        ], 200);
    }

    public function confirm(Request $request, $id)
    {
        $data = ["confirmed" => $request['confirmed'], "active" => 1];

        $account = Account::whereId($id)->first();

        $account->update($data);

        // Notification for admins
        $this->dispatch(new SendEmailJob(User::where('id', $account->user_id)->first()->email, $account, "SendMailClientApprovedAccount"));

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

    public function deleteSelectedAccounts(DeleteSelectedAccountsRequest $request)
    {
        $data = $request->validated();

        $accounts = Account::whereIn('id', $data['accounts_id'])->delete();

        return $this->successResponse([
            'accounts' => $accounts
        ], 200);
    }

    public function changeStatusSelectedAccounts(ChangeStatusSelectedAccountsRequest $request)
    {
        $data = $request->validated();

        $accounts = Account::whereIn('id', $data['accounts_id'])->update([
            'active' => $data['status']
        ]);

        return $this->successResponse([
            'accounts' => $accounts
        ], 200);
    }
}
