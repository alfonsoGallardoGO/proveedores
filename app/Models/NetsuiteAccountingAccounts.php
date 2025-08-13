<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NetsuiteAccountingAccounts extends Model
{
    protected $table = 'netsuite_accounting_accounts';
    // protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'external_id',
        'account_number',
        'name',
        'type',
        'description',
        'currency',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

}
