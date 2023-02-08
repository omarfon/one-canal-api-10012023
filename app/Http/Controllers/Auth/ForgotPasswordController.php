<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Helpers\Token;
use App\Constants\Message;
use App\Jobs\SendEmailJob;
use App\Models\PasswordReset;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Auth\ValidateRequest;
use App\Http\Requests\Auth\ValidateAdminRequest;
use App\Http\Requests\Auth\ValidateTokenRequest;
use App\Http\Requests\Auth\ValidatePasswordRequest;
use App\Http\Requests\Auth\ValidateCodeAdminRequest;
use App\Http\Requests\Auth\ChangePasswordAdminRequest;

class ForgotPasswordController extends Controller
{
    public function sendMail(ValidateRequest $request)
    {
        $data = $request->validated();

        $user = User::where('document_type', $data['document_type'])
            ->where('document_number', $data['document_number'])
            ->first();

        if ($user) {
            if ($user->valid) {
                $token = Token::getEncodedEmailCode($user->email, $user->id, rand(1000,9999));
                Cache::put($token, $user->email, now()->addMinutes(15));

                $user->token = $token;

                $this->dispatch(new SendEmailJob($user->email, $user, "SendMailClientForgotPassword"));

                return $this->successResponse([], 200, Message::SEND_MAIL_FORGOT_PASSWORD);
            } else {
                return $this->errorResponse(Message::USER_NOT_ACTIVATED, 403);
            }
        } else {
            return $this->errorResponse(Message::USER_NOT_FOUND, 403);
        }
    }

    public function validateToken(ValidateTokenRequest $request)
    {
        $data = $request->validated();

        $encoded_token = $data['access_token'];

        if (!Cache::has($encoded_token)) {
            return $this->errorResponse(Message::TOKEN_NOT_FOUND, 410);
        };

        $email = Token::getEmailByEncodedToken($encoded_token);
        $token_life = Token::getTokenLifeByEncodedToken($encoded_token);
        $user_id = Token::getUserIdByEncodedToken($encoded_token);

        if (Carbon::now()->diffInMinutes($token_life) <= 1) {
            return $this->errorResponse(Message::TOKEN_EXPIRED, 410);
        }

        $user = User::whereId($user_id)->whereEmail($email)->first();

        if (!$user) {
            return $this->errorResponse(Message::USER_NOT_FOUND, 403);
        }

        return $this->successResponse([], 200, Message::TOKEN_VALIDATE);
    }

    public function recoverPassword(ValidatePasswordRequest $request)
    {
        $data = $request->validated();

        $encoded_token = $data['access_token'];
        $password = $data['password'];

        if (!Cache::has($encoded_token)) {
            return $this->errorResponse(Message::TOKEN_NOT_FOUND, 410);
        };

        $email = Token::getEmailByEncodedToken($encoded_token);
        $token_life = Token::getTokenLifeByEncodedToken($encoded_token);
        $user_id = Token::getUserIdByEncodedToken($encoded_token);

        if (Carbon::now()->diffInMinutes($token_life) <= 1) {
            return $this->errorResponse(Message::TOKEN_EXPIRED, 410);
        }

        $user = User::whereId($user_id)->whereEmail($email)->first();

        if (!$user) {
            return $this->errorResponse(Message::USER_NOT_FOUND, 403);
        }

        $user->update([
            "password" => $password,
            "attemps" => 0
        ]);

        return $this->successResponse([], 200, Message::RECOVER_PASSWORD);
    }

    // Admin

    public function sendAdminMail(ValidateAdminRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if ($user) {
            if ($user->active) {
                $token = rand(100000,999999);

                $user->token = $token;

                PasswordReset::create([
                    "email" => $data['email'],
                    "token" => $token
                ]);

                $this->dispatch(new SendEmailJob($user->email, $user, "SendMailAdminForgotPassword"));

                return $this->successResponse([], 200, Message::SEND_MAIL_FORGOT_PASSWORD);
            } else {
                return $this->errorResponse(Message::USER_BLOCKED, 403);
            }
        } else {
            return $this->errorResponse(Message::ADMIN_NOT_FOUND, 403);
        }
    }

    public function validateCodeAdmin(ValidateCodeAdminRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if ($user) {
            if ($user->active) {
                $passwordReset = PasswordReset::whereToken($data['code'])
                    ->whereEmail($data['email'])
                    ->first();

                if (! $passwordReset) {
                    return $this->errorResponse('El código ingresado no es el correcto, por favor vuelva a intentarlo', 403);
                }

                return $this->successResponse([], 200, Message::SEND_MAIL_FORGOT_PASSWORD);
            } else {
                return $this->errorResponse(Message::USER_BLOCKED, 403);
            }
        } else {
            return $this->errorResponse(Message::ADMIN_NOT_FOUND, 403);
        }
    }

    public function changePasswordAdmin(ChangePasswordAdminRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if ($user) {
            if ($user->active) {
                $user->update([
                    "password" => $data['password']
                ]);

                return $this->successResponse([], 200, Message::SEND_MAIL_FORGOT_PASSWORD);
            } else {
                return $this->errorResponse(Message::USER_BLOCKED, 403);
            }
        } else {
            return $this->errorResponse(Message::ADMIN_NOT_FOUND, 403);
        }
    }
}
