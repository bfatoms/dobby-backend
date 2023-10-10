<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    use UsesUuid;

    protected $fillable = [
        'until',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
