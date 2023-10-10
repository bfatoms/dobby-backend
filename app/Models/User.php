<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Concerns\Attachable;
use App\Models\Concerns\Delible;
use App\Models\Concerns\Listable;
use App\Models\Concerns\Sortable;
use App\Models\Concerns\UsesUuid;
use App\Models\PasswordReset;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use Notifiable, UsesUuid, Attachable, Listable, Sortable, Delible, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "first_name",
        "last_name",
        "email",
        "password",
        "verification_token",
        "email_verified_at",
        "status",
        "role",
        "department",
        "logged_in_at",
        "birth_date",
        "mobile_number",
        "website",
        "language",
        "gender",
        "contact",
        "twitter",
        "facebook",
        "instagram",
        "github",
        "codepen",
        "slack",
        "company",
        'address1',
        'address2',
        'post_code',
        'city',
        'state',
        'country',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function passwordResets()
    {
        return $this->hasMany(PasswordReset::class);
    }

    public function isAllowedTo($module, $action)
    {
        $data = $this->permissions()->where('module', $module)->where('action', $action)->first();
        if (!empty($data)) {
            return true;
        }
        return false;
    }

    public function havePermission($module, $permission)
    {
        return $this->isAllowedTo($module, $permission);
    }

    public function permissions()
    {
        return $this->hasMany(UserPermission::class, 'user_id');
    }

    public function setPasswordAttributes($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            $user->permissions()->delete();
        });
    }

    public function getPermissions()
    {
        $data = [];

        foreach ($this->permissions()->get()->groupBy('module') as $module => $actions) {
            $data[] = [
                'module' => $module,
                'actions' => $actions->pluck('action')
            ];
        }

        return $data;
    }

    public function projectSetting()
    {
        return $this->hasOne(ProjectSetting::class, 'user_id', 'id');
    }

    public function projectPrice()
    {
        return $this->hasMany(ProjectPrice::class, 'user_id', 'id');
    }
}
