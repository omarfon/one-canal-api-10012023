<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Constants\Role;
use App\Models\Account;
use App\Constants\Message;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Api\AccountResource;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Http\Requests\Api\UpdatePasswordRequest;
use App\Http\Resources\Api\ClientProfileResource;
use App\Http\Requests\Api\ToggleSalaryViewRequest;

class MeController extends Controller
{
    public function get()
    {
        $auth_user = Auth::user();

        $user = User::where('id', $auth_user->id)
            ->where('role', Role::EMPLOYEE)
            ->first();

        return $this->successResponse(new ClientProfileResource($user), 200);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $data = $request->validated();

        $user = User::where('id', Auth::user()->id)->first();

        if (Hash::check($data['current_password'], $user->password)) {
            $user->update([
                "password" => $data['password']
            ]);

            return $this->successResponse(new ClientProfileResource($user), 200, Message::RECOVER_PASSWORD);
        } else {
            return $this->errorResponse(Message::DATA_NOT_CONFIRMED, 409);
        }
    }

    public function indexAccounts()
    {
        $auth_user = Auth::user();

        $accounts = Account::where('user_id', $auth_user->id)
            ->where('confirmed', 1)
            ->where('active', 1)
            ->orderBy('bank_id')
            ->get();

        return $this->successResponse(AccountResource::collection($accounts), 200);
    }

    public function toggleSalaryView(ToggleSalaryViewRequest $request)
    {
        $data = $request->validated();

        $auth_user = Auth::user();

        $user = User::where('id', $auth_user->id)
            ->where('role', Role::EMPLOYEE)
            ->first();

        $user->update([
            "salary_view" => $data['salary_view']
        ]);

        return $this->successResponse([
            "salary" => $user->salary,
            "salary_view" => $user->salary_view
        ], 200);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $data = $request->validated();

        $user = User::where('id', Auth::user()->id)->first();

        if ($user->salary_updated) {
            if ($data['salary'] && $data['salary'] != $user->salary && $user->salary > 0) {
                return $this->errorResponse("El salario sólo se puede modificar una vez, por favor, vuelva a enviar los datos sin modificar el salario.", 422);
            } else {
                if ($user->salary && $user->salary > 0.1) {
                    unset($data['salary']);
                }
            }
        } else {
            if ($data['salary'] && $data['salary'] != $user->salary) {
                $data['salary_updated'] = 1;
            }
        }

        $user->update($data);

        return $this->successResponse(new ClientProfileResource($user->fresh()), 200);
    }
}
