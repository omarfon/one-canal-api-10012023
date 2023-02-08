<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Constants\Role;
use App\Models\Statistic;
use App\Constants\Message;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\LoginAdminRequest;
use App\Http\Resources\Api\ClientLoginResource;
use App\Models\Activity;

class LoginController extends Controller
{
    public function authenticate(LoginRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('document_type', $credentials['document_type'])
            ->where('document_number', $credentials['document_number'])
            ->with(['accounts' => function ($query) {
                $query->where('active', 1)
                ->where('confirmed', 1)
                ->with('bank');
            }])
            ->first();

        if ($user) {
            if ($user->valid) {
                if (!$user->active) {
                    return $this->errorResponse(Message::USER_BLOCKED, 403);
                } else {
                    if (Hash::check($credentials['password'], $user->password)) {
                        $token = $user->createToken('Cl!3NtTok3n');

                        $user->update([
                            "last_login" => Carbon::now(),
                            "attempts" => 0
                        ]);

                        $user->load(['accounts' => function ($query) {
                            $query->where('active', 1)
                                ->orderBy('bank_id')
                                ->with('bank');
                        }]);

                        Statistic::where('date', date('Y-m-d'))->where('type', 'login')->update([
                            'value' => DB::raw('value + 1')
                        ]);

                        return $this->successResponse([
                            'access_token' => $token->plainTextToken,
                            'me' => new ClientLoginResource($user)
                        ], 200);
                    } else {
                        if ($user->attemps >= 2) {
                            return $this->errorResponse(Message::USER_ATTEMPTS_LIMIT, 403);
                        } else {
                            $user->update([
                                "attemps" => $user->attemps + 1
                            ]);
                        }
                        return $this->errorResponse(Message::USER_NOT_FOUND, 403);
                    }
                }
            } else {
                return $this->errorResponse(Message::USER_NOT_ACTIVATED, 206);
            }
        } else {
            return $this->errorResponse(Message::USER_NOT_FOUND, 403);
        }
    }

    public function adminAuthenticate(LoginAdminRequest $request)
    {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])
            ->where('role', Role::ADMIN)
            ->first();

        if ($user) {
            if (Hash::check($credentials['password'], $user->password)) {
                if ($user->active) {
                    $token = $user->createToken('@Dm!NTok3n');

                    $user->update([
                        "last_login" => Carbon::now(),
                        "attempts" => 0
                    ]);

                    Activity::create([
                        "user_id" => $user->id,
                        "model" => "login",
                        "color" => "text-danger",
                        "bold" => false,
                        "text" => "Inicio de sesión"
                    ]);

                    return $this->successResponse([
                        'access_token' => $token->plainTextToken,
                        'me'           => $user
                    ], 200);
                } else {
                    return $this->errorResponse(Message::USER_BLOCKED, 403);
                }
            } else {
                return $this->errorResponse(Message::USER_NOT_FOUND, 403);
            }
        } else {
            return $this->errorResponse(Message::USER_NOT_FOUND, 403);
        }
    }

    public function logout()
    {
        $user = User::where('id', Auth::user()->id)->first();

        $user->tokens()->delete();

        return $this->successResponse([], 200, Message::LOGOUT);
    }
}
