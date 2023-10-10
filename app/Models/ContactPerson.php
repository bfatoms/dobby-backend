<?php

namespace App\Models;

use App\Models\Concerns\Listable;
use App\Models\Concerns\Sortable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactPerson extends Model
{
    use Listable, Sortable, HasFactory;

    protected $table = 'contact_persons';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'is_primary',
        'include_in_emails',
        'contact_id'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'include_in_emails' => 'boolean'
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
