<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Constants\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\ChangePasswordRequest;

class MeController extends Controller {

    public function get()
    {
        $auth_user = Auth::user();

        $user = User::where('id', $auth_user->id)
        ->where('role', Role::ADMIN)
        ->first();

        return $this->successResponse($user, 200);
    }

    public function updatePassword(ChangePasswordRequest $request)
    {
        $auth_user = Auth::user();

        $data = $request->validated();

        if (! Hash::check($data['current_password'], $auth_user->password)) {
            return $this->errorResponse("Contraseña actual no coincide", 409);
        }

        $user = User::where('id', $auth_user->id)->first();
        $user->update($data);

        return $this->successResponse($user, 200);
    }
}
