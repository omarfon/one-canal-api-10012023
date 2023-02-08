<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Constants\Role;
use App\Models\Account;
use App\Models\Activity;
use App\Imports\CountImport;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use App\Imports\ClientImport;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\ClientRequest;
use App\Http\Requests\Admin\AccountRequest;
use App\Http\Resources\Admin\ClientResource;
use App\Http\Resources\Api\DocumentTypeResource;
use App\Http\Requests\Admin\DeleteSelectedRequest;
use App\Http\Requests\Admin\ChangeStatusSelectedRequest;
use App\Http\Requests\Admin\DeleteSelectedAccountsRequest;
use App\Http\Requests\Admin\ChangeStatusSelectedAccountsRequest;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $clients = User::filterByColumns($request->all())->where('role', Role::EMPLOYEE)->orderBy('id', 'desc')
        ->with('business', 'marital_status')->paginate();

        return $this->successResponse($clients, 200);
    }

    public function show($id)
    {
        $client = User::where('role', Role::EMPLOYEE)->whereId($id)->first();

        return $this->successResponse([
            'client' => new ClientResource($client)
        ], 200);
    }

    public function store(ClientRequest $request)
    {
        $data = $request->validated();

        $data['role'] = Role::EMPLOYEE;

        $client = User::create($data);

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "clients",
            "color" => "text-info",
            "bold" => true,
            "text" => "Creación de cliente " . $client->names . " " . $client->surnames
        ]);

        return $this->successResponse([
            'client' => $client
        ], 200);
    }

    public function update(ClientRequest $request, $id)
    {
        $data = $request->validated();

        $client = User::where('role', Role::EMPLOYEE)->whereId($id)->first();

        $client->update($data);

        if (isset($data['active']) && $data['active'] == '0') {
            $client->tokens()->delete();
        }

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "clients",
            "color" => "text-info",
            "bold" => true,
            "text" => "Actualización de cliente " . $client->names . " " . $client->surnames
        ]);

        return $this->successResponse([
            'client' => $client
        ], 200);
    }

    public function delete($id)
    {
        $client = User::where('role', Role::EMPLOYEE)->whereId($id)->first();

        $client->delete();

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "clients",
            "color" => "text-info",
            "bold" => true,
            "text" => "Eliminación de cliente " . $client->names . " " . $client->surnames
        ]);

        return $this->successResponse([
            'client' => $client
        ], 200);
    }

    public function storeAccount(AccountRequest $request)
    {
        $data = $request->validated();

        $account = Account::create($data);

        return $this->successResponse([
            'account' => $account
        ], 200);
    }

    public function updateAccount(AccountRequest $request, $id)
    {
        $data = $request->validated();

        $account = Account::whereId($id)->first();

        $account->update($data);

        return $this->successResponse([
            'account' => $account
        ], 200);
    }

    public function confirmAccount(Request $request, $id)
    {
        $data = $request['confirmed'];

        $account = Account::whereId($id)->first();

        $account->update($data);

        return $this->successResponse([
            'account' => $account
        ], 200);
    }

    public function deleteAccount($id)
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

    public function indexDocumentTypes()
    {
        $document_types = DocumentType::get();

        return $this->successResponse(DocumentTypeResource::collection($document_types), 200);
    }

    // Selected

    public function deleteSelected(DeleteSelectedRequest $request)
    {
        $data = $request->validated();

        $users = User::whereIn('id', $data['ids'])->delete();

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "clients",
            "color" => "text-info",
            "bold" => true,
            "text" => "Eliminación de clientes"
        ]);

        return $this->successResponse([
            'users' => $users
        ], 200);
    }

    public function changeStatusSelected(ChangeStatusSelectedRequest $request)
    {
        $data = $request->validated();

        $users = User::whereIn('id', $data['ids'])->update([
            'active' => $data['status']
        ]);

        if ($data['status'] == '0') {
            foreach (User::whereIn('id', $data['ids'])->get() as $key => $user) {
                $user->tokens()->delete();
            }
        }

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "clients",
            "color" => "text-info",
            "bold" => true,
            "text" => "Cambio de estado de clientes"
        ]);

        return $this->successResponse([
            'users' => $users
        ], 200);
    }

    public function import(Request $request)
    {
        $log = date('Y-m-d_his');
        $authUser = Auth::user();
        $array = (new CountImport())->toArray(request()->file('file'));

        $log .= "_CLIENTES_" . bin2hex(openssl_random_pseudo_bytes(8));
        File::put(storage_path() . '/app/public/import_logs/' . $log . '.log', "");

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "clients",
            "color" => "text-info",
            "bold" => true,
            "text" => "Carga masiva de clientes"
        ]);

        $result = Excel::import(new ClientImport($log, $authUser, count($array[0])), request()->file('file'));
    }

    public function templateDownload()
    {
        $file = Storage::disk('public')->get("templates/clientes.xlsx");

        return (new Response($file, 200))
              ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
