<?php

namespace App\Http\Controllers\Auth\v2;

use App\Models\User;
use App\Constants\Message;
use App\Helpers\Functions;
use App\Jobs\SendEmailJob;
use App\Models\PasswordReset;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ValidateRequest;
use App\Http\Requests\Auth\ValidateCodeRequest;
use App\Http\Requests\Auth\ValidateCodePasswordRequest;

class ForgotPasswordController extends Controller
{
    public function sendMail(ValidateRequest $request)
    {
        $data = $request->validated();

        $user = User::where('document_type', $data['document_type'])
            ->where('document_number', $data['document_number'])
            ->first();

        if (!$user) {
            return $this->errorResponse(Message::USER_NOT_FOUND, 403);
        }

        if (PasswordReset::where('user_id', $user->id)
        ->where('expired_at', '>', date('Y-m-d H:i:s'))
        ->exists()) {
            return $this->errorResponse(Message::DONT_SEND_EMAIL_PASSWORD_RESET, 403);
        }

        if ($user) {
            if ($user->valid) {
                $code = rand(1000,9999);

                PasswordReset::create([
                    'email' => $user->email,
                    'user_id'    => $user->id,
                    'token'       => $code,
                    'expired_at' => date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' + 15 minute'))
                ]);

                $user->token = $code;

                $this->dispatch(new SendEmailJob($user->email, $user, "SendMailClientCodeForgotPassword"));

                return $this->successResponse([
                    "user_id" => $user->id,
                    "email" => Functions::maskEmail($user->email)
                ], 200, Message::SEND_MAIL_FORGOT_PASSWORD);
            } else {
                return $this->errorResponse(Message::USER_NOT_ACTIVATED, 403);
            }
        } else {
            return $this->errorResponse(Message::USER_NOT_ACTIVATED, 403);
        }
    }

    public function validateToken(ValidateCodeRequest $request)
    {
        $data = $request->validated();

        $code = $data['code'];
        $userId = $data['user_id'];
        $emailMask = $data['email'];

        $user = User::whereId($userId)->first();

        if (!$user) {
            return $this->errorResponse(Message::USER_NOT_FOUND, 403);
        }

        if ($emailMask != Functions::maskEmail($user->email)) {
            return $this->errorResponse(Message::TOKEN_NOT_FOUND, 409);
        }

        $passwordReset = PasswordReset::where([
            "user_id" => $userId,
            "email" => $user->email,
            "token" => $code
        ])->where('expired_at', '>', date('Y-m-d H:i:s'))
        ->orderBy('created_at', 'desc')->first();

        if (!$passwordReset) {
            return $this->errorResponse(Message::TOKEN_NOT_FOUND, 409);
        }

        return $this->successResponse(null, 200, Message::TOKEN_VALIDATE);
    }

    public function recoverPassword(ValidateCodePasswordRequest $request)
    {
        $data = $request->validated();

        $code = $data['code'];
        $userId = $data['user_id'];
        $emailMask = $data['email'];
        $password = $data['password'];

        $user = User::whereId($userId)->first();

        if (!$user) {
            return $this->errorResponse(Message::USER_NOT_FOUND, 403);
        }

        if ($emailMask != Functions::maskEmail($user->email)) {
            return $this->errorResponse(Message::TOKEN_NOT_FOUND, 409);
        }

        $passwordReset = PasswordReset::where([
            "user_id" => $userId,
            "email" => $user->email,
            "token" => $code
        ])->where('expired_at', '>', date('Y-m-d H:i:s'))
        ->orderBy('created_at', 'desc')->first();

        if (!$passwordReset) {
            return $this->errorResponse(Message::TOKEN_NOT_FOUND, 409);
        }

        $user->update([
            "password" => $password,
            "attemps" => 0
        ]);

        PasswordReset::where(["user_id" => $userId])->delete();

        return $this->successResponse([], 200, Message::RECOVER_PASSWORD);
    }
}
