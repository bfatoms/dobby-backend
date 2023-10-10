<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Mails\ForgotPasswordMail;
use App\Models\PasswordReset;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;



class AuthController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => [
            'login',
            'forgot',
            'verify',
            'reset',
            'resetToken',
            'refresh'
        ]]);
    }

    public function verify($verification_token)
    {
        $user = User::where('verification_token', $verification_token)->first();

        if (empty($user)) {
            return Redirect::to(getOrigin() . '/login');
        }

        $this->transact(function () use ($user) {
            $user->update([
                'email_verified_at' => now()->toDateTimeString(),
                'verification_token' => null
            ]);
            return [];
        }, "EMAIL_VERIFIED", "EMAIL_NOT_VERIFIED");

        return Redirect::to(getOrigin() . '/login');
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->input('email'))->first();

        if (empty($user)) {
            return $this->reject("EMAIL_NOT_REGISTERED", 400);
        }

        if (!$token = auth()->attempt(request(['email', 'password']))) {
            return $this->reject("INCORRECT_PASSWORD", 400);
        }

        $data = [
            'user' => auth()->user()->load('avatar'),
            'permissions' => auth()->user()->getPermissions()
        ];

        return $this->respondWithToken($token, $data);
    }

    public function me()
    {
        return $this->resolve(
            array_merge(
                auth()->user()->toArray(),
                ['permissions' => auth()->user()->getPermissions()]
            ),
            "LOGGED_IN_USER"
        );
    }

    public function logout()
    {
        auth()->logout(true);

        return $this->resolve("LOGOUT_SUCCESSFUL");
    }


    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token, $data = [])
    {
        $data = array_merge($data, [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);

        return $this->resolve($data, "LOGIN_SUCCESSFUL");
    }

    public function forgot(ForgotPasswordRequest $request)
    {
        $user = User::where('email', $request->input('email'))->first();

        if (empty($user)) {
            return $this->reject('EMAIL_NOT_REGISTERED');
        }

        $data = array_merge($user->toArray(), $request->all());

        return $this->sendResetToken($data, $user);
    }

    public function sendResetToken($data, $user)
    {
        return $this->transact(function () use ($data, $user) {
            $password_reset = $user->passwordResets()->create([
                'until' => now()->addDay(1)->toDateTimeString(),
            ]);

            $data['token'] = $password_reset['id'];

            $data['referrer'] = getOrigin();

            try {
                Mail::to($data['email'])->queue(new ForgotPasswordMail($data));
            } catch (Exception $ex) {
                return $this->reject("An error occurred while sending email");
            }
        }, "RESET_PASSWORD_SENT", "RESET_PASSWORD_NOT_SENT");
    }

    public function resetToken($token)
    {
        $password_reset = PasswordReset::find($token);

        if (empty($password_reset)) {
            return $this->resolve(['allowed' => false], 'TOKEN_INVALID', 422);
        }

        if (Carbon::parse($password_reset['until'])->lt(now())) {
            return $this->resolve(['allowed' => false], 'TOKEN_EXPIRED', 422);
        }

        return $this->resolve(['allowed' => true], 'TOKEN_VERIFIED');
    }

    public function reset(PasswordResetRequest $request, $token)
    {
        $password_reset = PasswordReset::with('user')->find($token);

        if (empty($password_reset)) {
            return $this->reject("TOKEN_INVALID");
        }

        if (Carbon::parse($password_reset['until'])->lt(now())) {
            return $this->reject("TOKEN_EXPIRED");
        }

        return $this->transact(function () use ($password_reset, $request) {
            $user = $password_reset['user'];

            $user->update([
                'password' => Hash::make($request->input('password')),
            ]);

            $password_reset->delete();

            return [];
        }, "PASSWORD_CHANGE_SUCCESSFUL", "PASSWORD_CHANGE_FAILED");
    }
}
