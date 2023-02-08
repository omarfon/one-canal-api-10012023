<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Constants\Role;
use App\Models\Activity;
use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Admin\AdministratorRequest;
use App\Http\Requests\Admin\DeleteSelectedRequest;
use App\Http\Requests\Admin\ChangeStatusSelectedRequest;

class AdministratorController extends Controller
{
    public function index(Request $request)
    {
        $administrators = User::filterByColumns($request->all())->where('role', Role::ADMIN)->orderBy('id', 'desc')->paginate();

        return $this->successResponse($administrators, 200);
    }

    public function show($id)
    {
        $administrator = User::where('role', Role::ADMIN)->whereId($id)->first();

        return $this->successResponse([
            'administrator' => $administrator
        ], 200);
    }

    public function store(AdministratorRequest $request)
    {
        $data = $request->validated();

        $password = substr(md5(mt_rand()), 0, 7);

        $data['password'] = $password;
        $data['role'] = Role::ADMIN;

        $administrator = User::create($data);

        $this->dispatch(new SendEmailJob($data['email'], $data, "SendMailNewAdministrator"));

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "administrator",
            "color" => "text-primary",
            "bold" => true,
            "text" => "Creacíon de administrador " . $data['names'] . " " . $data['surnames']
        ]);

        return $this->successResponse([
            'administrator' => $administrator
        ], 200);
    }

    public function update(AdministratorRequest $request, $id)
    {
        $data = $request->validated();

        $administrator = User::where('role', Role::ADMIN)->whereId($id)->first();

        $administrator->update($data);

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "administrator",
            "color" => "text-primary",
            "bold" => true,
            "text" => "Actualización de administrador " . $data['names'] . " " . $data['surnames']
        ]);

        return $this->successResponse([
            'administrator' => $administrator
        ], 200);
    }

    public function delete($id)
    {
        $administrator = User::where('role', Role::ADMIN)->whereId($id)->first();

        $administrator->delete();

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "administrator",
            "color" => "text-primary",
            "bold" => true,
            "text" => "Eliminación de administrador " . $administrator->names . " " . $administrator->surnames
        ]);

        return $this->successResponse([
            'administrator' => $administrator
        ], 200);
    }

    // Selected

    public function deleteSelected(DeleteSelectedRequest $request)
    {
        $data = $request->validated();

        $users = User::whereIn('id', $data['ids'])->delete();

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "administrator",
            "color" => "text-primary",
            "bold" => true,
            "text" => "Eliminación de administradores"
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

        Activity::create([
            "user_id" => Auth::user()->id,
            "model" => "administrator",
            "color" => "text-primary",
            "bold" => true,
            "text" => "Cambio de estado de administradores"
        ]);

        return $this->successResponse([
            'users' => $users
        ], 200);
    }
}
