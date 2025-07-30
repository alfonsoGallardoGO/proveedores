<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SupplierUser extends Authenticatable
{
    use Notifiable;


    protected $table = 'supplier_users';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'rfc',
        'phone_number',
        'url_photo_user',
        'default_locale',
        'signature',
        'request_change_password',
        'recovery_token',
        'recovery_token_expires',
        'current_team_id',
        'current_branch_office_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'two_factor_confirmed_at' => 'datetime',
        'recovery_token_expires' => 'datetime',
    ];
}
