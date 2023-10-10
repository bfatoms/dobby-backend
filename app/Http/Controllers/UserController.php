<?php

namespace App\Http\Controllers;

use App\Http\Requests\InviteUserRequest;
use App\Http\Requests\ProjectSettingUpdateRequest;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mails\InviteUserMail;
use App\Models\Project;
use App\Models\ProjectPrice;
use App\Models\ProjectSetting;
use Exception;

class UserController extends BaseController
{
    protected $model = User::class;

    protected $create_request = UserCreateRequest::class;

    protected $update_request = UserUpdateRequest::class;

    public function permissions(User $user)
    {
        return $this->resolve($user->getPermissions(), "USER_PERMISSIONS");
    }

    public function invite(InviteUserRequest $request)
    {
        $input = $request->all();

        $user = User::where('email', $input['email'])->first();

        if (!empty($user)) {
            return $this->reject('EMAIL_ALREADY_REGISTERED', 422);
        }

        $mail['password'] = $input['password'];

        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);

        $mail = $user->toArray();

        $mail['referrer'] = getOrigin();

        $password_reset = $user->passwordResets()->create([
            'until' => now()->addDay(1)->toDateTimeString(),
        ]);

        $mail['token'] = $password_reset['id'];

        try {
            Mail::to($user['email'])->queue(new InviteUserMail($mail));
            // Mail::to($user['email'])->send(new InviteUserMail($mail));
            // Mail::send(new InviteUserMail($mail), $user->toArray());
        } catch (Exception $e) {
            throw $e;
            return $this->reject('An error occurred while sending email');
        }

        return $this->resolve($user, "INVITE_SUCCESSFUL");
    }

    public function updateProjectSetting(User $user, ProjectSettingUpdateRequest $request)
    {
        $this->authorize('all', Project::class);

        $data = request()->all();

        return $this->transact(function () use ($data, $user) {
            $user_data = [
                'user_id' => $user->id,
            ];

            return ProjectSetting::updateOrCreate($user_data, array_merge($data, $user_data));
        }, "RESOURCE_UPDATED", "RESOURCE_NOT_UPDATED");
    }

    public function updateProjectPrice(User $user)
    {
        $this->authorize('all', Project::class);

        $data = request()->all();

        return $this->transact(function () use ($data, $user) {
            $user_project = [
                'user_id' => $user->id,
                'project_id' => $data['project_id'],
            ];

            return ProjectPrice::updateOrCreate($user_project, array_merge($data, $user_project));
        }, "RESOURCE_UPDATED", "RESOURCE_NOT_UPDATED");
    }
}
