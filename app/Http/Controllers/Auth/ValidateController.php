<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Helpers\Token;
use App\Constants\Message;
use App\Jobs\SendEmailJob;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Auth\ValidateRequest;
use App\Http\Requests\Auth\ValidateTokenRequest;
use App\Http\Requests\Auth\ValidatePasswordRequest;

class ValidateController extends Controller
{
    public function validateAccount(ValidateRequest $request)
    {
        $data = $request->validated();

        $user = User::where('document_type', $data['document_type'])
            ->where('document_number', $data['document_number'])
            ->first();

        if ($user) {
            if (!$user->valid) {
                $token = Token::getEncodedEmailToken($user->email, $user->id);
                Cache::put($token, $user->email, now()->addMinutes(15));

                $user->token = $token;

                $this->dispatch(new SendEmailJob($user->email, $user, "SendMailClientActivate"));

                return $this->successResponse(null, 200, Message::SEND_MAIL_ACTIVATED);
            } else {
                return $this->errorResponse(Message::USER_ACTIVATED, 403);
            }
        } else {
            return $this->errorResponse(Message::USER_NOT_FOUND_FOR_ACTIVATED, 403);
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

        return $this->successResponse(null, 200, Message::TOKEN_VALIDATE);
    }

    public function validatePassword(ValidatePasswordRequest $request)
    {
        $data = $request->validated();

        $encoded_token = $data['access_token'];
        $password = $data['password'];

        if (!Cache::has($encoded_token)) {
            return $this->errorResponse(Message::TOKEN_NOT_FOUND, 403);
        };

        $email = Token::getEmailByEncodedToken($encoded_token);
        $token_life = Token::getTokenLifeByEncodedToken($encoded_token);
        $user_id = Token::getUserIdByEncodedToken($encoded_token);

        if (Carbon::now()->diffInMinutes($token_life) <= 1) {
            return $this->errorResponse(Message::TOKEN_EXPIRED, 403);
        }

        $user = User::whereId($user_id)->whereEmail($email)->first();

        if (!$user) {
            return $this->errorResponse(Message::USER_NOT_FOUND, 403);
        }

        $user->update([
            "password" => $password,
            "attemps" => 0,
            "valid" => 1
        ]);

        $this->dispatch(new SendEmailJob($user->email, $user, "SendMailSuccessClientActivate"));

        return $this->successResponse(null, 200, Message::ACCOUNT_VALIDATED);
    }
}
