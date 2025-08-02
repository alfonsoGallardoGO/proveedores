<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Notifications\CustomResetPassword;

class SupplierUser extends Authenticatable
{
    use Notifiable;
    use TwoFactorAuthenticatable;
    use \Laravel\Jetstream\HasProfilePhoto;
    use \Illuminate\Database\Eloquent\SoftDeletes;


    protected $table = 'supplier_users';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'rfc',
        'phone_number',
        'profile_photo_path',
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
    
    protected $appends = [
        'profile_photo_url',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }
}
